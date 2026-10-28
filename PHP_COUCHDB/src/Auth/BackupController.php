<?php

declare(strict_types=1);

namespace App\Auth;

use App\Core\Csrf;
use App\Core\Response;
use App\Core\View;
use RuntimeException;

final class BackupController
{
    public function __construct(
        private readonly AccountRepository $accounts,
        private readonly string $database,
        private readonly string $projectRoot,
    ) {
    }

    public function index(): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        $directory = $this->backupDirectory();
        $files = [];
        if (is_dir($directory) && !is_link($directory)) {
            foreach (glob($directory . '/*.json') ?: [] as $path) {
                $name = basename($path);
                if (!$this->validName($name) || is_link($path) || !is_file($path)) continue;
                $files[] = ['name' => $name, 'size' => filesize($path) ?: 0, 'modified_at' => filemtime($path) ?: 0];
            }
        }
        usort($files, static fn (array $a, array $b): int => $b['modified_at'] <=> $a['modified_at']);
        $flash = $_SESSION['admin_backup_flash'] ?? null;
        unset($_SESSION['admin_backup_flash']);
        return new Response(View::render('admin/backup/index', [
            'title' => 'Sao lưu dữ liệu',
            'files' => $files,
            'flash' => is_array($flash) ? $flash : null,
        ]));
    }

    public function create(): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            return new Response('Yêu cầu CSRF không hợp lệ.', 403);
        }
        if (!$this->allowedDatabase()) return new Response('Database hiện tại không được phép sao lưu.', 409);
        $filename = 'backup-manager-' . gmdate('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.json';
        try {
            $result = $this->runBackup($filename);
            if ($result['exit_code'] !== 0) {
                throw new RuntimeException('Lệnh backup thất bại: ' . $result['stderr'] . ' ' . $result['stdout']);
            }
            $_SESSION['admin_backup_flash'] = ['type' => 'success', 'message' => 'Đã tạo backup ' . $filename . '.'];
        } catch (RuntimeException $exception) {
            $_SESSION['admin_backup_flash'] = ['type' => 'danger', 'message' => $exception->getMessage()];
        }
        return $this->redirect('/admin/backup');
    }

    /** @param array<string, string> $params */
    public function download(array $params): Response
    {
        $guard = $this->managerGuard();
        if ($guard !== null) return $guard;
        $name = (string) ($params['name'] ?? '');
        if (!$this->validName($name) || str_contains($name, '..')) return new Response('Tên file backup không hợp lệ.', 400);
        $directory = $this->backupDirectory();
        $realDirectory = realpath($directory);
        $path = $directory . DIRECTORY_SEPARATOR . $name;
        if ($realDirectory === false || is_link($directory) || is_link($path) || !is_file($path)) {
            return new Response('Không tìm thấy file backup.', 404);
        }
        $realPath = realpath($path);
        if ($realPath === false || !str_starts_with($realPath, $realDirectory . DIRECTORY_SEPARATOR)) {
            return new Response('Đường dẫn file backup không hợp lệ.', 400);
        }
        $contents = file_get_contents($realPath);
        if (!is_string($contents)) return new Response('Không thể đọc file backup.', 500);
        return new Response($contents, 200, 'application/json; charset=utf-8', [
            'Content-Disposition' => 'attachment; filename="' . $name . '"',
            'Content-Length' => (string) strlen($contents),
        ]);
    }

    private function managerGuard(): ?Response
    {
        $user = currentUser();
        if ($user === null) return $this->redirect('/login');
        if (($user['type'] ?? null) !== 'staff' || ($user['role'] ?? null) !== 'manager') {
            return new Response('<h1>403 - Chỉ manager được truy cập backup</h1>', 403);
        }
        $account = $this->accounts->byTokenSubject((string) ($user['document_id'] ?? ''));
        if ($account === null || ($account['type'] ?? null) !== 'staff' || ($account['role'] ?? null) !== 'manager' || ($account['active'] ?? false) !== true) {
            unset($_SESSION['auth_user'], $_SESSION['_cart_line_count']);
            session_regenerate_id(true);
            return $this->redirect('/login');
        }
        return null;
    }

    private function backupDirectory(): string
    {
        return rtrim($this->projectRoot, '/\\') . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'backups';
    }

    private function validName(string $name): bool
    {
        return $name !== '' && basename($name) === $name && preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.json$/', $name) === 1;
    }

    private function allowedDatabase(): bool
    {
        return preg_match('/^[a-z][a-z0-9_$()+\/-]*_(test|demo)$/', $this->database) === 1;
    }

    /** @return array{exit_code:int,stdout:string,stderr:string} */
    private function runBackup(string $filename): array
    {
        $command = [PHP_BINARY, $this->projectRoot . '/scripts/database_backup.php', 'backup', $filename];
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $this->projectRoot, getenv());
        if (!is_resource($process)) throw new RuntimeException('Không thể khởi chạy tiến trình backup.');
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]);
        return ['exit_code' => proc_close($process), 'stdout' => trim((string) $stdout), 'stderr' => trim((string) $stderr)];
    }

    private function redirect(string $location): Response
    {
        return new Response('', 303, 'text/plain; charset=utf-8', ['Location' => $location]);
    }
}
