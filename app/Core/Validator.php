<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    public static function required(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field => $message) {
            if (empty($data[$field])) {
                $errors[$field] = $message;
            }
        }
        return $errors;
    }
}
