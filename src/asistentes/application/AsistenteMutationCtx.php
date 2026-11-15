<?php

declare(strict_types=1);

namespace src\asistentes\application;

use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/**
 * Cápsulas HashB de las mutaciones de asistentes (alta, edición, cambio de actividad,
 * eliminación y asignación de plaza).
 *
 * Las firman los builders que montan los formularios y las listas; los controladores las
 * abren, de modo que un POST sin cápsula válida no puede apuntar, cambiar ni borrar
 * asistentes de otras actividades o personas (fase 6.5 del plan de revisión de tests).
 */
final class AsistenteMutationCtx
{
    public const GUARDAR = 'asistente_guardar';
    public const ELIMINAR = 'asistente_eliminar';
    public const PLAZA_ASIGNAR = 'asistente_plaza_asignar';

    /**
     * Cápsula de un formulario que guarda una asistencia. Fija los ids que la pantalla ya
     * conoce (0 = lo elige el usuario en el formulario): desde una actividad, la actividad;
     * desde una persona, la persona; al modificar, las dos; al cambiar de actividad, la
     * persona y la actividad de la que sale.
     */
    public static function guardar(int $idActiv, int $idNom, int $idActivOld = 0): string
    {
        return HashB::sign(self::GUARDAR, ['id_activ' => $idActiv, 'id_nom' => $idNom, 'id_activ_old' => $idActivOld]);
    }

    /** Cápsula de una fila de la lista: eliminar esa asistencia. */
    public static function eliminar(int $idActiv, int $idNom): string
    {
        return HashB::sign(self::ELIMINAR, ['id_activ' => $idActiv, 'id_nom' => $idNom]);
    }

    /** Cápsula de la lista de asistentes de una actividad: asignar plaza a los marcados. */
    public static function plazaAsignar(int $idActiv): string
    {
        return HashB::sign(self::PLAZA_ASIGNAR, ['id_activ' => $idActiv]);
    }

    /**
     * @param array<string, mixed> $post
     * @return array{id_activ: int, id_nom: int, id_activ_old: int}|null  null si la cápsula no es
     *         válida o no fija ni la actividad ni la persona
     */
    public static function abrirGuardar(array $post): ?array
    {
        try {
            $ctx = HashB::open(FuncTablasSupport::inputString($post, 'ctx_guardar'), self::GUARDAR);
        } catch (HashBInvalidException) {
            return null;
        }
        $fijado = [
            'id_activ' => FuncTablasSupport::inputInt($ctx, 'id_activ'),
            'id_nom' => FuncTablasSupport::inputInt($ctx, 'id_nom'),
            'id_activ_old' => FuncTablasSupport::inputInt($ctx, 'id_activ_old'),
        ];

        return ($fijado['id_activ'] === 0 && $fijado['id_nom'] === 0) ? null : $fijado;
    }

    /**
     * @param array<string, mixed> $post
     * @return array{id_activ: int, id_nom: int}|null  null si la cápsula no es válida
     */
    public static function abrirEliminar(array $post): ?array
    {
        try {
            $ctx = HashB::open(FuncTablasSupport::inputString($post, 'ctx_eliminar_asistente'), self::ELIMINAR);
        } catch (HashBInvalidException) {
            return null;
        }
        $idActiv = FuncTablasSupport::inputInt($ctx, 'id_activ');
        $idNom = FuncTablasSupport::inputInt($ctx, 'id_nom');

        return ($idActiv === 0 || $idNom === 0) ? null : ['id_activ' => $idActiv, 'id_nom' => $idNom];
    }

    /**
     * @param array<string, mixed> $post
     * @return int|null  id de la actividad; null si la cápsula no es válida
     */
    public static function abrirPlazaAsignar(array $post): ?int
    {
        try {
            $ctx = HashB::open(FuncTablasSupport::inputString($post, 'ctx_plaza'), self::PLAZA_ASIGNAR);
        } catch (HashBInvalidException) {
            return null;
        }
        $idActiv = FuncTablasSupport::inputInt($ctx, 'id_activ');

        return $idActiv === 0 ? null : $idActiv;
    }
}
