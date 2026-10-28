<?php ob_start(); ?>
<section class="container py-4">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div><p class="text-uppercase small text-muted mb-1">Khu vực manager</p><h1 class="h2 mb-1">Quản trị người dùng</h1><p class="text-muted mb-0">Quản lý trạng thái tài khoản khách hàng và nhân viên.</p></div>
        <div class="d-flex gap-2"><a class="btn btn-primary" href="/admin/users/new">Tạo nhân viên</a><a class="btn btn-outline-secondary" href="/admin">Quay lại quản trị</a></div>
    </div>
    <?php if (is_array($flash)): ?><div class="alert alert-<?= h($flash['type'] ?? 'info') ?>" role="status"><?= h($flash['message'] ?? '') ?></div><?php endif; ?>
    <form class="card card-body mb-4" method="get" action="/admin/users">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-4"><label class="form-label" for="q">Tìm theo tên, email hoặc mã</label><input class="form-control" id="q" name="q" maxlength="100" value="<?= h($filters['q']) ?>"></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="type">Loại tài khoản</label><select class="form-select" id="type" name="type"><option value="all" <?= $filters['type'] === 'all' ? 'selected' : '' ?>>Tất cả</option><option value="customer" <?= $filters['type'] === 'customer' ? 'selected' : '' ?>>Khách hàng</option><option value="staff" <?= $filters['type'] === 'staff' ? 'selected' : '' ?>>Nhân viên</option></select></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="role">Vai trò</label><select class="form-select" id="role" name="role"><option value="all" <?= $filters['role'] === 'all' ? 'selected' : '' ?>>Tất cả</option><option value="customer" <?= $filters['role'] === 'customer' ? 'selected' : '' ?>>Khách hàng</option><option value="staff" <?= $filters['role'] === 'staff' ? 'selected' : '' ?>>Staff</option><option value="manager" <?= $filters['role'] === 'manager' ? 'selected' : '' ?>>Manager</option></select></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="status">Trạng thái</label><select class="form-select" id="status" name="status"><option value="all" <?= $filters['status'] === 'all' ? 'selected' : '' ?>>Tất cả</option><option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Đang hoạt động</option><option value="locked" <?= $filters['status'] === 'locked' ? 'selected' : '' ?>>Đã khóa</option></select></div>
            <div class="col-12 col-lg-2 d-flex gap-2"><button class="btn btn-primary" type="submit">Lọc danh sách</button><a class="btn btn-outline-secondary" href="/admin/users">Xóa lọc</a></div>
        </div>
    </form>
    <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h5 mb-0">Danh sách tài khoản</h2><span class="text-muted small">Tìm thấy <?= count($users) ?> tài khoản</span></div>
    <?php if ($users === []): ?><div class="alert alert-light border">Không có tài khoản phù hợp.</div><?php else: ?>
    <div class="table-responsive"><table class="table table-hover align-middle bg-white"><thead><tr><th>Tài khoản</th><th>Loại / vai trò</th><th>Username / điện thoại</th><th>Trạng thái</th><th></th></tr></thead><tbody>
        <?php foreach ($users as $account): ?><tr>
            <td><strong><?= h($account['name'] !== '' ? $account['name'] : 'Chưa cập nhật tên') ?></strong><div class="small text-muted"><?= h($account['legacy_id']) ?></div></td>
            <td><?= $account['type'] === 'customer' ? 'Khách hàng' : 'Nhân viên' ?><?php if ($account['role'] !== 'customer'): ?><div class="small text-muted"><?= $account['role'] === 'manager' ? 'Manager' : 'Staff' ?></div><?php endif; ?></td>
            <td><?= h($account['email']) ?><?php if ($account['phone'] !== ''): ?><div class="small text-muted"><?= h($account['phone']) ?></div><?php endif; ?></td>
            <td><span class="badge text-bg-<?= $account['active'] ? 'success' : 'secondary' ?>"><?= $account['active'] ? 'Hoạt động' : 'Đã khóa' ?></span></td>
            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="/admin/users/<?= rawurlencode($account['id']) ?>">Chi tiết</a></td>
        </tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?>
    <p class="small text-muted mt-3">Manager có thể khóa/mở khóa khách hàng và nhân viên. Tài khoản manager không thể bị khóa để bảo đảm luôn còn người quản trị.</p>
</section>
<?php $content = (string) ob_get_clean(); require dirname(__DIR__, 2) . '/layout.php';