<?php

namespace SupportModule\Sdk\Exceptions;

use Exception;

class SupportSdkException extends Exception
{
    public static function httpError(int $status, string $message = '', array $context = []): self
    {
        $msg = $message !== '' ? $message : 'Support API request failed';
        $detail = $context ? (' | context: ' . json_encode($context)) : '';
        return new self("HTTP {$status}: {$msg}{$detail}");
    }
}

