<?php
// src/ResponseMacros.php
// you could put the macro logic here if you prefer to keep ServiceProvider thin

namespace YourVendor\ResponseMacro;

class ResponseMacros
{
    public static function api(int $code, string $msg, mixed $data = null): array
    {
        $cfg = config('response');
        $result = [
            $cfg['status_key']  => $code,
            $cfg['message_key'] => $msg,
        ];
        if (! is_null($data)) {
            $result[$cfg['data_key']] = $data;
        }
        return $result;
    }
}