<?php ob_start(); $creating = $mode === 'create'; $oldInput = is_array($oldInput ?? null) ? $oldInput : []; ?>
<section class="container py-4" style="max-width: 880px">
    <div class="mb-4"><a href="<?= $creating ? '/admin/users' : '/admin/users/' . rawurlencode($account['id']) ?>" class="small text-decoration-none">&larr; Quay lại</a><h1 class="h2 mt-2 mb-1"><?= h($title) ?></h1><p class="text-muted mb-0"><?= $creating ? 'Tạo tài khoản nhân viên mới. Tài khoản được cấp vai trò Staff.' : 'Chỉ cập nhật tên và số điện thoại; username và vai trò hiện tại được giữ nguyên.' ?></p></div>
    <?php if (is_string($error) && $error !== ''): ?><div class="alert alert-danger" role="alert"><?= h($error) ?></div><?php endif; ?>
    <form class="card card-body" method="post" action="<?= $creating ? '/admin/users/new' : '/admin/users/' . rawurlencode($account['id']) . '/edit' ?>" <?= $creating ? 'autocomplete="off"' : '' ?>>
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <?php if ($creating): ?>
            <div class="mb-3"><label class="form-label" for="email">Username (email)</label><input class="form-control" id="email" name="email" type="email" maxlength="254" autocomplete="username" value="<?= h($oldInput['email'] ?? '') ?>" required></div>
        <?php else: ?>
            <input type="hidden" name="_rev" value="<?= h($account['rev'] ?? '') ?>">
            <div class="mb-3"><label class="form-label">Username (email)</label><div class="form-control-plaintext border rounded px-3"><?= h($account['email']) ?></div></div>
        <?php endif; ?>
        <div class="mb-3"><label class="form-label" for="name">Họ tên</label><input class="form-control" id="name" name="name" type="text" maxlength="100" value="<?= h($oldInput['name'] ?? ($account['name'] ?? '')) ?>" required></div>
        <div class="mb-3"><label class="form-label" for="phone">Số điện thoại</label><input class="form-control" id="phone" name="phone" type="tel" maxlength="30" value="<?= h($oldInput['phone'] ?? ($account['phone'] ?? '')) ?>"></div>
        <?php if ($creating): ?>
            <div class="mb-3"><label class="form-label" for="password">Mật khẩu ban đầu</label><input class="form-control" id="password" name="password" type="password" minlength="12" maxlength="128" autocomplete="new-password" required><div class="form-text">Ít nhất 12 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</div></div>
            <div class="mb-3"><label class="form-label" for="password_confirmation">Nhập lại mật khẩu</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="12" maxlength="128" autocomplete="new-password" required></div>
            <div class="alert alert-light border mb-3">Vai trò được gán cố định là <strong>Staff</strong>. Không thể tạo Manager từ biểu mẫu này.</div>
        <?php else: ?>
            <div class="mb-3"><label class="form-label">Vai trò</label><div class="form-control-plaintext border rounded px-3"><?= $account['role'] === 'manager' ? 'Manager' : ($account['role'] === 'staff' ? 'Staff' : 'Customer') ?> (không chỉnh sửa tại đây)</div></div>
        <?php endif; ?>
        <div class="d-flex flex-wrap gap-2"><button class="btn btn-primary" type="submit"><?= $creating ? 'Tạo tài khoản nhân viên' : 'Lưu thông tin' ?></button><a class="btn btn-outline-secondary" href="<?= $creating ? '/admin/users' : '/admin/users/' . rawurlencode($account['id']) ?>">Hủy</a></div>
    </form>
</section>
<?php $content = (string) ob_get_clean(); require dirname(__DIR__, 2) . '/layout.php';
