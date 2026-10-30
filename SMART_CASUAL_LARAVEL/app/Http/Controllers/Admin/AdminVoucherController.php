<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaGiamGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminVoucherController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $status = $request->query('status', 'all');

        $query = MaGiamGia::withCount('donHangs');

        if ($search !== '') {
            $query->where('MaCode', 'LIKE', '%' . strtoupper($search) . '%');
        }

        if ($status === 'active') {
            $query->where('TrangThai', true);
        } elseif ($status === 'inactive') {
            $query->where('TrangThai', false);
        } else {
            $status = 'all';
        }

        $vouchers = $query->orderBy('MaCode')->paginate(15)->withQueryString();

        return view('admin.vouchers.index', [
            'title' => 'Quản lý mã giảm giá',
            'vouchers' => $vouchers,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('admin.vouchers.create', ['title' => 'Tạo mã giảm giá']);
    }

    public function store(Request $request)
    {
        $request->merge(['MaCode' => strtoupper(trim((string) $request->input('MaCode')))]);
        $validated = $this->validateVoucher($request, true);

        MaGiamGia::create([
            'MaCode' => $validated['MaCode'],
            'LoaiGiamGia' => $validated['LoaiGiamGia'],
            'GiaTri' => $validated['GiaTri'],
            'SoLuong' => $validated['SoLuong'],
            'TrangThai' => true,
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Đã tạo mã giảm giá {$validated['MaCode']}.");
    }

    public function edit(string $code)
    {
        return view('admin.vouchers.edit', [
            'title' => 'Cập nhật mã giảm giá',
            'voucher' => MaGiamGia::where('MaCode', $code)->firstOrFail(),
        ]);
    }

    public function update(Request $request, string $code)
    {
        $voucher = MaGiamGia::where('MaCode', $code)->firstOrFail();
        $validated = $this->validateVoucher($request, false);

        DB::transaction(function () use ($voucher, $validated) {
            $lockedVoucher = MaGiamGia::where('MaCode', $voucher->MaCode)->lockForUpdate()->firstOrFail();
            $lockedVoucher->update([
                'LoaiGiamGia' => $validated['LoaiGiamGia'],
                'GiaTri' => $validated['GiaTri'],
                'SoLuong' => $validated['SoLuong'],
                'TrangThai' => (bool) $validated['TrangThai'],
            ]);
        });

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Đã cập nhật mã giảm giá {$voucher->MaCode}.");
    }

    public function toggleStatus(string $code)
    {
        $voucher = DB::transaction(function () use ($code) {
            $voucher = MaGiamGia::where('MaCode', $code)->lockForUpdate()->firstOrFail();
            $voucher->update(['TrangThai' => !$voucher->TrangThai]);

            return $voucher;
        });

        return back()->with('success', $voucher->TrangThai
            ? "Đã kích hoạt mã giảm giá {$voucher->MaCode}."
            : "Đã tắt mã giảm giá {$voucher->MaCode}.");
    }

    private function validateVoucher(Request $request, bool $creating): array
    {
        $rules = [
            'LoaiGiamGia' => ['required', Rule::in(['cash', 'percent', 'shipping'])],
            'GiaTri' => 'required|numeric|min:0',
            'SoLuong' => 'required|integer|min:0',
        ];

        if ($creating) {
            $rules['MaCode'] = 'required|string|max:50|unique:MA_GIAM_GIA,MaCode';
        } else {
            $rules['TrangThai'] = 'required|boolean';
        }

        $validated = Validator::make($request->all(), $rules, [
            'MaCode.required' => 'Vui lòng nhập mã giảm giá.',
            'MaCode.unique' => 'Mã giảm giá này đã tồn tại.',
            'MaCode.max' => 'Mã giảm giá không được vượt quá 50 ký tự.',
            'LoaiGiamGia.in' => 'Loại giảm giá không hợp lệ.',
            'GiaTri.required' => 'Vui lòng nhập giá trị giảm.',
            'GiaTri.numeric' => 'Giá trị giảm phải là số.',
            'GiaTri.min' => 'Giá trị giảm không được âm.',
            'SoLuong.required' => 'Vui lòng nhập số lượt còn lại.',
            'SoLuong.integer' => 'Số lượt phải là số nguyên.',
            'SoLuong.min' => 'Số lượt không được âm.',
        ])->validate();

        if ($validated['LoaiGiamGia'] === 'percent' && (float) $validated['GiaTri'] > 100) {
            throw ValidationException::withMessages(['GiaTri' => 'Mức giảm phần trăm không được vượt quá 100%.']);
        }

        if ($validated['LoaiGiamGia'] === 'cash' && (float) $validated['GiaTri'] <= 0) {
            throw ValidationException::withMessages(['GiaTri' => 'Mức giảm tiền mặt phải lớn hơn 0.']);
        }

        return $validated;
    }
}
