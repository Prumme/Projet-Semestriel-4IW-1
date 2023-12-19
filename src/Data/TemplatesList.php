<?php

namespace App\Data;

class TemplatesList {
    public static function getTemplateId(string $name): int {
        return match ($name) {
            'welcome_email' => 1,
        };
    }
}