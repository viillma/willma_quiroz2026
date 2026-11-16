<?php

namespace App\Enums;

enum UserStatus: string
{
    case Activo = 'activo';
    case Pendiente = 'pendiente';
    case Suspendido = 'suspendido';

    public function label(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Pendiente => 'Pendiente de aprobación',
            self::Suspendido => 'Suspendido',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
