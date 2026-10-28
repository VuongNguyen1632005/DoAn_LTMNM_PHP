<?php

declare(strict_types=1);

namespace App\Auth;

use RuntimeException;

final class UserAdministrationService
{
    public function __construct(private readonly AccountRepository $accounts)
    {
    }

    /** @param array<string, mixed> $filters
     *  @return list<array<string, mixed>>
     */
    public function list(array $filters): array
    {
        $type = in_array($filters['type'] ?? null, ['customer', 'staff'], true) ? $filters['type'] : 'all';
        $status = in_array($filters['status'] ?? null, ['active', 'locked'], true) ? $filters['status'] : 'all';
        $role = in_array($filters['role'] ?? null, ['customer', 'staff', 'manager'], true) ? $filters['role'] : 'all';
        $query = trim((string) ($filters['q'] ?? ''));
        if (mb_strlen($query) > 100) $query = mb_substr($query, 0, 100);
        $needle = mb_strtolower($query, 'UTF-8');

        $users = [];
        foreach ($this->accounts->allForAdministration() as $account) {
            $safe = self::safeAccount($account);
            if ($type !== 'all' && $safe['type'] !== $type) continue;
            if ($status === 'active' && $safe['active'] !== true) continue;
            if ($status === 'locked' && $safe['active'] === true) continue;
            if ($role !== 'all' && $safe['role'] !== $role) continue;
            if ($needle !== '') {
                $haystack = mb_strtolower(implode(' ', [
                    (string) $safe['name'], (string) $safe['email'], (string) $safe['legacy_id'],
                ]), 'UTF-8');
                if (!str_contains($haystack, $needle)) continue;
            }
            $users[] = $safe;
        }
        usort($users, static fn (array $a, array $b): int => [$a['type'], $a['name'], $a['legacy_id']] <=> [$b['type'], $b['name'], $b['legacy_id']]);
        return $users;
    }

    /** @return array<string, mixed>|null */
    public function detail(string $id): ?array
    {
        $account = $this->accounts->accountById($id);
        return $account === null ? null : self::safeAccount($account);
    }

    /** @return array<string, mixed> */
    public function updateProfile(string $id, string $expectedRevision, string $name, string $phone, string $actorLegacyId): array
    {
        $account = $this->accounts->accountById($id);
        if ($account === null) throw new RuntimeException('Không tìm thấy tài khoản.');
        if ($expectedRevision === '' || !hash_equals((string) ($account['_rev'] ?? ''), $expectedRevision)) {
            throw new AccountRevisionConflictException();
        }
        $name = trim($name);
        $phone = trim($phone);
        if ($name === '' || mb_strlen($name) > 100 || preg_match('/[\x00-\x1F\x7F]/u', $name) === 1) {
            throw new RuntimeException('Họ tên phải có từ 1 đến 100 ký tự hợp lệ.');
        }
        if (mb_strlen($phone) > 30 || preg_match('/[\x00-\x1F\x7F]/u', $phone) === 1) {
            throw new RuntimeException('Số điện thoại không hợp lệ (tối đa 30 ký tự).');
        }
        $account['profile'] = is_array($account['profile'] ?? null) ? $account['profile'] : [];
        $account['profile']['name'] = $name;
        $account['profile']['phone'] = $phone;
        $account['meta'] = is_array($account['meta'] ?? null) ? $account['meta'] : [];
        $account['meta']['profile_updated_at'] = gmdate('c');
        $account['meta']['profile_updated_by'] = $actorLegacyId;
        $this->accounts->saveAdministrationChange($account);
        return self::safeAccount($account);
    }
    public function setActive(string $id, bool $active, string $actorId, string $actorLegacyId): void
    {
        $account = $this->accounts->accountById($id);
        if ($account === null) throw new RuntimeException('Không tìm thấy tài khoản.');
        if ($id === $actorId && !$active) throw new RuntimeException('Bạn không thể tự khóa tài khoản đang đăng nhập.');
        if (($account['type'] ?? null) === 'staff' && ($account['role'] ?? null) === 'manager' && !$active) {
            throw new RuntimeException('Không thể khóa tài khoản manager. Giới hạn này bảo đảm luôn còn manager hoạt động, kể cả khi nhiều yêu cầu chạy đồng thời.');
        }
        if (($account['active'] ?? false) === $active) {
            throw new RuntimeException($active ? 'Tài khoản này đã hoạt động.' : 'Tài khoản này đã bị khóa.');
        }

        $account['active'] = $active;
        $account['meta'] = is_array($account['meta'] ?? null) ? $account['meta'] : [];
        $account['meta']['account_status_changed_at'] = gmdate('c');
        $account['meta']['account_status_changed_by'] = $actorLegacyId;
        $this->accounts->saveAdministrationChange($account);
    }

    /** @param array<string, mixed> $account
     *  @return array<string, mixed>
     */
    private static function safeAccount(array $account): array
    {
        $auth = is_array($account['auth'] ?? null) ? $account['auth'] : [];
        $profile = is_array($account['profile'] ?? null) ? $account['profile'] : [];
        $meta = is_array($account['meta'] ?? null) ? $account['meta'] : [];
        return [
            'id' => (string) ($account['_id'] ?? ''),
            'rev' => (string) ($account['_rev'] ?? ''),
            'legacy_id' => (string) ($account['legacy_id'] ?? ''),
            'type' => (string) ($account['type'] ?? ''),
            'name' => (string) ($profile['name'] ?? ''),
            'email' => (string) ($profile['email'] ?? $auth['username'] ?? ''),
            'phone' => (string) ($profile['phone'] ?? ''),
            'role' => (string) ($account['type'] ?? '') === 'customer' ? 'customer' : (string) ($account['role'] ?? ''),
            'active' => ($account['active'] ?? false) === true,
            'created_at' => (string) ($meta['created_at'] ?? ''),
        ];
    }
}
