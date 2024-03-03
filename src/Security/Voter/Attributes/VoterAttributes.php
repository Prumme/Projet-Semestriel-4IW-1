<?php

namespace App\Security\Voter\Attributes;

class VoterAttributes
{
    static public function has(string $attribute): bool
    {
        return in_array($attribute, self::getAttributes());
    }

    static public function getAttributes(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return array_values($reflection->getConstants());
    }
}