<?php

declare(strict_types=1);

namespace src\actividadescentro\application;

use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/**
 * Cápsulas HashB de las mutaciones de centros encargados (asignar, reordenar,
 * quitar). Cada una fija la actividad y el centro; los controladores toman los
 * ids de la cápsula y no del POST.
 */
final class CentroEncargadoMutationCtx
{
    public const ASIGNAR = 'centro_encargado_asignar';
    public const REORDENAR = 'centro_encargado_reordenar';
    public const ELIMINAR = 'centro_encargado_eliminar';

    public static function firmar(string $accion, int $idActiv, int $idUbi): string
    {
        return HashB::sign($accion, ['id_activ' => $idActiv, 'id_ubi' => $idUbi]);
    }

    /**
     * Cápsulas de un centro ya encargado (subir/bajar prioridad y quitar).
     *
     * @return array{ctx_reordenar: string, ctx_eliminar: string}
     */
    public static function deEncargado(int $idActiv, int $idUbi): array
    {
        return [
            'ctx_reordenar' => self::firmar(self::REORDENAR, $idActiv, $idUbi),
            'ctx_eliminar' => self::firmar(self::ELIMINAR, $idActiv, $idUbi),
        ];
    }

    /**
     * @param array<string, mixed> $post
     * @return array{id_activ: int, id_ubi: int}|null  null si la cápsula no es válida
     */
    public static function abrir(array $post, string $accion): ?array
    {
        try {
            $ctx = HashB::open(FuncTablasSupport::inputString($post, 'ctx'), $accion);
        } catch (HashBInvalidException) {
            return null;
        }
        $idActiv = FuncTablasSupport::inputInt($ctx, 'id_activ');
        $idUbi = FuncTablasSupport::inputInt($ctx, 'id_ubi');
        if ($idActiv <= 0 || $idUbi <= 0) {
            return null;
        }

        return ['id_activ' => $idActiv, 'id_ubi' => $idUbi];
    }
}
