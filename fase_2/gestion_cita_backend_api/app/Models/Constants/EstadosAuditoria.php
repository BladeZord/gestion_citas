<?php

namespace App\Model\Constants;

final class EstadosAuditoria
{
    public const ACTIVO = 'A';

    public const INACTIVO = 'I';

    public const ELIMINADO = 'E';

    /**
     * @return array<int, string>
     */
    public static function valores(): array
    {
        return [
            self::ACTIVO,
            self::INACTIVO,
            self::ELIMINADO,
        ];
    }

    public static function esValido(?string $estado): bool
    {
        return in_array($estado, self::valores(), true);
    }
}
