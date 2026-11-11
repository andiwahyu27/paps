<?php

namespace App\Services;

use InvalidArgumentException;

class MonevAnswerCodec
{
    public function encode(string $inputType, $value): array
    {
        if ($value === null || $value === '') return ['numeric_value' => null, 'text_value' => null];

        if (in_array($inputType, ['integer', 'double', 'numeric'], true)) {
            if (!is_numeric($value)) throw new InvalidArgumentException('Nilai numerik tidak valid.');
            return ['numeric_value' => $value, 'text_value' => null];
        }

        if ($inputType === 'select' && is_array($value)) {
            return ['numeric_value' => null, 'text_value' => json_encode(array_values($value), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)];
        }

        if (!in_array($inputType, ['string', 'radio', 'select', 'boolean', 'textarea'], true)) {
            throw new InvalidArgumentException('Tipe pertanyaan tidak didukung.');
        }

        return ['numeric_value' => null, 'text_value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value];
    }
}
