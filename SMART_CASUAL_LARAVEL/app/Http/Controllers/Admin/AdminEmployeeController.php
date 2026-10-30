<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $role = $request->query('role', 'all');
        $status = $request->query('status', 'all');

        $query = NhanVien::withCount('donHangs');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('MaNV', 'LIKE', "%{$search}%")
                    ->orWhere('TenDangNhap', 'LIKE', "%{$search}%")
                    ->orWhere('HoTen', 'LIKE', "%{$search}%");
            });
        }

        if (in_array($role, ['staff', 'manager'], true)) {
            $query->where('ChucVu', $role);
        } else {
            $role = 'all';
        }

        if ($status === 'active') {
            $query->where('TrangThai', true);
        } elseif ($status === 'inactive') {
            $query->where('TrangThai', false);
        } else {
            $status = 'all';
        }

        $employees = $query->orderBy('HoTen')->paginate(15)->withQueryString();

        return view('admin.employees.index', [
            'title' => 'Quản lý nhân viên',
            'employees' => $employees,
            'search' => $search,
            'role' => $role,
            'status' => $status,
            'currentEmployeeId' => Auth::guard('admin')->user()->MaNV,
        ]);
    }

    public function create()
    {
        return view('admin.employees.create', ['title' => 'Thêm nhân viên']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'TenDangNhap' => 'required|string|max:100|unique:NHAN_VIEN,TenDangNhap',
            'HoTen' => 'required|string|max:100',
            'ChucVu' => ['required', Rule::in(['staff', 'manager'])],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'TenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'TenDangNhap.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'HoTen.required' => 'Vui lòng nhập họ tên nhân viên.',
            'ChucVu.in' => 'Vai trò nhân viên không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu ban đầu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $employeeId = $this->generateEmployeeId();

        NhanVien::create([
            'MaNV' => $employeeId,
            'TenDangNhap' => trim($validated['TenDangNhap']),
            'HoTen' => trim($validated['HoTen']),
            'ChucVu' => $validated['ChucVu'],
            'MatKhau' => Hash::make($validated['password']),
            'TrangThai' => true,
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', "Đã tạo tài khoản nhân viên {$validated['TenDangNhap']}.");
    }

    public function edit(string $id)
    {
        return view('admin.employees.edit', [
            'title' => 'Cập nhật nhân viên',
            'employee' => NhanVien::where('MaNV', $id)->firstOrFail(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $employee = NhanVien::where('MaNV', $id)->firstOrFail();

        $validated = $request->validate([
            'TenDangNhap' => ['required', 'string', 'max:100', Rule::unique('NHAN_VIEN', 'TenDangNhap')->ignore($employee->MaNV, 'MaNV')],
            'HoTen' => 'required|string|max:100',
            'ChucVu' => ['required', Rule::in(['staff', 'manager'])],
            'TrangThai' => 'required|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'TenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'TenDangNhap.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'HoTen.required' => 'Vui lòng nhập họ tên nhân viên.',
            'ChucVu.in' => 'Vai trò nhân viên không hợp lệ.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $managerId = Auth::guard('admin')->user()->MaNV;
        if ($employee->MaNV === $managerId && ($validated['ChucVu'] !== 'manager' || !(bool) $validated['TrangThai'])) {
            throw ValidationException::withMessages([
                'ChucVu' => 'Bạn không thể tự hạ quyền hoặc khóa tài khoản đang đăng nhập.',
            ]);
        }

        DB::transaction(function () use ($employee, $validated) {
            $lockedEmployee = NhanVien::where('MaNV', $employee->MaNV)->lockForUpdate()->firstOrFail();
            $removesActiveManager = $lockedEmployee->ChucVu === 'manager'
                && $lockedEmployee->TrangThai
                && ($validated['ChucVu'] !== 'manager' || !(bool) $validated['TrangThai']);

            if ($removesActiveManager) {
                $activeManagers = NhanVien::where('ChucVu', 'manager')->where('TrangThai', true)->lockForUpdate()->count();
                if ($activeManagers <= 1) {
                    throw ValidationException::withMessages([
                        'TrangThai' => 'Cần duy trì ít nhất một tài khoản Quản lý đang hoạt động.',
                    ]);
                }
            }

            $updates = [
                'TenDangNhap' => trim($validated['TenDangNhap']),
                'HoTen' => trim($validated['HoTen']),
                'ChucVu' => $validated['ChucVu'],
                'TrangThai' => (bool) $validated['TrangThai'],
            ];

            if (!empty($validated['password'])) {
                $updates['MatKhau'] = Hash::make($validated['password']);
            }

            $lockedEmployee->update($updates);
        });

        return redirect()->route('admin.employees.index')
            ->with('success', "Đã cập nhật nhân viên {$validated['TenDangNhap']}.");
    }

    public function toggleStatus(string $id)
    {
        $employee = NhanVien::where('MaNV', $id)->firstOrFail();
        $managerId = Auth::guard('admin')->user()->MaNV;

        if ($employee->MaNV === $managerId) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản đang đăng nhập.');
        }

        $newStatus = DB::transaction(function () use ($employee) {
            $lockedEmployee = NhanVien::where('MaNV', $employee->MaNV)->lockForUpdate()->firstOrFail();
            if ($lockedEmployee->ChucVu === 'manager' && $lockedEmployee->TrangThai) {
                $activeManagers = NhanVien::where('ChucVu', 'manager')->where('TrangThai', true)->lockForUpdate()->count();
                if ($activeManagers <= 1) {
                    throw ValidationException::withMessages([
                        'TrangThai' => 'Cần duy trì ít nhất một tài khoản Quản lý đang hoạt động.',
                    ]);
                }
            }

            $newStatus = !$lockedEmployee->TrangThai;
            $lockedEmployee->update(['TrangThai' => $newStatus]);

            return $newStatus;
        });

        return back()->with('success', $newStatus ? 'Đã mở khóa tài khoản nhân viên.' : 'Đã khóa tài khoản nhân viên.');
    }

    private function generateEmployeeId(): string
    {
        do {
            $id = 'NV' . strtoupper(bin2hex(random_bytes(6)));
        } while (NhanVien::where('MaNV', $id)->exists());

        return $id;
    }
}
