<?php ob_start(); ?>
<section class="container py-4" style="max-width: 1000px">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><p class="text-uppercase small text-muted mb-1">Khu vực manager</p><h1 class="h2 mb-1">Sao lưu dữ liệu</h1><p class="text-muted mb-0">Tạo và tải xuống bản sao lưu JSON của database ứng dụng.</p></div>
        <a class="btn btn-outline-secondary" href="/admin">Quay lại quản trị</a>
    </div>
    <?php if (is_array($flash)): ?><div class="alert alert-<?= h($flash['type'] ?? 'info') ?>" role="status"><?= h($flash['message'] ?? '') ?></div><?php endif; ?>
    <div class="card card-body mb-4"><p class="mb-3">File mới không ghi đè file cùng tên. Backup gồm documents, design documents và cấu hình `_security`.</p><form method="post" action="/admin/backup/create"><input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>"><button class="btn btn-primary" type="submit">Tạo backup</button></form></div>
    <h2 class="h5">File backup</h2>
    <?php if ($files === []): ?><p class="text-muted">Chưa có file backup trong thư mục lưu trữ.</p><?php else: ?>
        <div class="table-responsive"><table class="table align-middle bg-white"><thead><tr><th>Tên file</th><th>Kích thước</th><th>Ngày sửa (UTC)</th><th></th></tr></thead><tbody>
            <?php foreach ($files as $file): ?><tr><td><code><?= h($file['name']) ?></code></td><td><?= number_format((int) $file['size']) ?> bytes</td><td><?= h(gmdate('Y-m-d H:i:s', (int) $file['modified_at'])) ?></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="/admin/backup/download/<?= rawurlencode($file['name']) ?>">Tải xuống</a></td></tr><?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
</section>
<?php $content = (string) ob_get_clean(); require dirname(__DIR__, 2) . '/layout.php';
