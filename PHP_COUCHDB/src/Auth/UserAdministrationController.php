<?php

declare(strict_types=1);

namespace App\Auth;

use App\Core\Csrf;
use App\Core\Response;
use App\Core\View;
use RuntimeException;

final class UserAdministrationController
{
    public function __construct(
        private readonly UserAdministrationService $users,
        private readonly AccountRepository $accounts,
        private readonly AccountService $accountService,
    ) {
    }

    /** @param array<string, mixed> $query */
    public function index(array $params, array $query): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        $flash = $_SESSION['admin_users_flash'] ?? null;
        unset($_SESSION['admin_users_flash']);
        $filters = [
            'q' => is_string($query['q'] ?? null) ? $query['q'] : '',
            'type' => is_string($query['type'] ?? null) ? $query['type'] : 'all',
            'role' => is_string($query['role'] ?? null) ? $query['role'] : 'all',
            'status' => is_string($query['status'] ?? null) ? $query['status'] : 'all',
        ];
        return new Response(View::render('admin/users/index', [
            'title' => 'Quản trị người dùng',
            'users' => $this->users->list($filters),
            'filters' => $filters,
            'flash' => is_array($flash) ? $flash : null,
        ]));
    }

    /** @param array<string, string> $params */
    public function detail(array $params): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        $user = $this->users->detail((string) ($params['id'] ?? ''));
        if ($user === null) return new Response(View::render('errors/not-found', ['title' => 'Không tìm thấy tài khoản']), 404);
        $flash = $_SESSION['admin_users_flash'] ?? null;
        unset($_SESSION['admin_users_flash']);
        return new Response(View::render('admin/users/detail', [
            'title' => 'Chi tiết tài khoản',
            'user' => $user,
            'flash' => is_array($flash) ? $flash : null,
        ]));
    }

    /** @param array<string, mixed> $query */
    public function newStaffForm(array $params, array $query): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        $flash = $_SESSION['admin_users_form_flash'] ?? null;
        unset($_SESSION['admin_users_form_flash']);
        return new Response(View::render('admin/users/form', [
            'title' => 'Tạo tài khoản nhân viên',
            'mode' => 'create',
            'account' => null,
            'oldInput' => is_array($flash) ? ($flash['input'] ?? []) : [],
            'error' => is_array($flash) ? ($flash['error'] ?? null) : null,
        ]));
    }

    public function createStaff(array $params, array $query): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            $_SESSION['admin_users_form_flash'] = ['error' => 'Phiên biểu mẫu hết hạn. Hãy tải lại trang.', 'input' => []];
            return $this->redirect('/admin/users/new');
        }
        $name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
        $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
        $phone = is_string($_POST['phone'] ?? null) ? trim($_POST['phone']) : '';
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $confirmation = is_string($_POST['password_confirmation'] ?? null) ? $_POST['password_confirmation'] : '';
        try {
            $user = currentUser() ?? [];
            $created = $this->accountService->createStaff(
                $name, $email, $phone, $password, $confirmation, (string) ($user['legacy_id'] ?? ''),
            );
            $_SESSION['admin_users_flash'] = ['type' => 'success', 'message' => 'Đã tạo tài khoản nhân viên với vai trò Staff.'];
            return $this->redirect('/admin/users/' . rawurlencode((string) $created['_id']));
        } catch (RuntimeException $exception) {
            $_SESSION['admin_users_form_flash'] = [
                'error' => $exception->getMessage(),
                'input' => ['name' => $name, 'email' => $email, 'phone' => $phone],
            ];
            return $this->redirect('/admin/users/new');
        } finally {
            if (function_exists('sodium_memzero')) {
                if ($password !== '') sodium_memzero($password);
                if ($confirmation !== '') sodium_memzero($confirmation);
            }
        }
    }

    /** @param array<string, string> $params */
    public function editForm(array $params): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        $account = $this->users->detail((string) ($params['id'] ?? ''));
        if ($account === null) return new Response(View::render('errors/not-found', ['title' => 'Không tìm thấy tài khoản']), 404);
        $flash = $_SESSION['admin_users_form_flash'] ?? null;
        unset($_SESSION['admin_users_form_flash']);
        return new Response(View::render('admin/users/form', [
            'title' => 'Chỉnh sửa thông tin tài khoản',
            'mode' => 'edit',
            'account' => $account,
            'oldInput' => is_array($flash) ? ($flash['input'] ?? []) : [],
            'error' => is_array($flash) ? ($flash['error'] ?? null) : null,
        ]));
    }

    /** @param array<string, string> $params */
    public function updateProfile(array $params): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        $id = (string) ($params['id'] ?? '');
        $location = '/admin/users/' . rawurlencode($id) . '/edit';
        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            $_SESSION['admin_users_form_flash'] = ['error' => 'Phiên biểu mẫu hết hạn. Hãy tải lại trang.', 'input' => []];
            return $this->redirect($location);
        }
        $name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
        $phone = is_string($_POST['phone'] ?? null) ? trim($_POST['phone']) : '';
        $revision = is_string($_POST['_rev'] ?? null) ? trim($_POST['_rev']) : '';
        try {
            $user = currentUser() ?? [];
            $this->users->updateProfile($id, $revision, $name, $phone, (string) ($user['legacy_id'] ?? ''));
            $_SESSION['admin_users_flash'] = ['type' => 'success', 'message' => 'Đã cập nhật thông tin hồ sơ.'];
            return $this->redirect('/admin/users/' . rawurlencode($id));
        } catch (AccountRevisionConflictException $exception) {
            $account = $this->users->detail($id);
            if ($account === null) return new Response(View::render('errors/not-found', ['title' => 'Không tìm thấy tài khoản']), 404);
            return new Response(View::render('admin/users/form', [
                'title' => 'Chỉnh sửa thông tin tài khoản',
                'mode' => 'edit',
                'account' => $account,
                'oldInput' => ['name' => $name, 'phone' => $phone],
                'error' => $exception->getMessage(),
            ]), 409);
        } catch (RuntimeException $exception) {
            $_SESSION['admin_users_form_flash'] = ['error' => $exception->getMessage(), 'input' => ['name' => $name, 'phone' => $phone]];
            return $this->redirect($location);
        }
    }
    /** @param array<string, string> $params */
    public function setActive(array $params): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        $user = currentUser() ?? [];
        $id = (string) ($params['id'] ?? '');
        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            $_SESSION['admin_users_flash'] = ['type' => 'danger', 'message' => 'Phiên biểu mẫu hết hạn. Hãy tải lại trang rồi thử lại.'];
            return $this->redirect('/admin/users');
        }
        $activeValue = $_POST['active'] ?? null;
        if (!is_string($activeValue) || !in_array($activeValue, ['0', '1'], true)) {
            $_SESSION['admin_users_flash'] = ['type' => 'danger', 'message' => 'Trạng thái tài khoản không hợp lệ.'];
            return $this->redirect('/admin/users');
        }
        $active = $activeValue === '1';
        try {
            $this->users->setActive(
                $id,
                $active,
                (string) ($user['document_id'] ?? ''),
                (string) ($user['legacy_id'] ?? ''),
            );
            $_SESSION['admin_users_flash'] = ['type' => 'success', 'message' => $active ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.'];
        } catch (RuntimeException $exception) {
            $_SESSION['admin_users_flash'] = ['type' => 'danger', 'message' => $exception->getMessage()];
        }
        return $this->redirect('/admin/users/' . rawurlencode($id));
    }

    private function managerGuard(): ?Response
    {
        $sessionUser = currentUser();
        if ($sessionUser === null) return $this->redirect('/login');
        if (($sessionUser['type'] ?? null) !== 'staff' || ($sessionUser['role'] ?? null) !== 'manager') {
            return new Response('<h1>403 - Chỉ manager được quản trị người dùng</h1><p><a href="/">Về trang chủ</a></p>', 403);
        }
        $account = $this->accounts->byTokenSubject((string) ($sessionUser['document_id'] ?? ''));
        if ($account === null || ($account['active'] ?? false) !== true || ($account['type'] ?? null) !== 'staff' || ($account['role'] ?? null) !== 'manager') {
            unset($_SESSION['auth_user'], $_SESSION['_cart_line_count']);
            session_regenerate_id(true);
            $_SESSION['auth_flash'] = ['type' => 'danger', 'message' => 'Phiên quản trị không còn hiệu lực. Vui lòng đăng nhập lại.'];
            return $this->redirect('/login');
        }
        return null;
    }

    private function redirect(string $location): Response
    {
        return new Response('', 303, 'text/plain; charset=utf-8', ['Location' => $location]);
    }
}
