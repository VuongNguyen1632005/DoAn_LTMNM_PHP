<?php

declare(strict_types=1);

namespace App\Auth;

use RuntimeException;

final class AccountRevisionConflictException extends RuntimeException
{
    public function __construct(string $message = 'Tài khoản đã được cập nhật ở nơi khác. Hãy kiểm tra thông tin mới rồi thử lại.')
    {
        parent::__construct($message);
    }
}
