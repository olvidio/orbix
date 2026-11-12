<?php

declare(strict_types=1);

namespace src\actividadcargos\application;

use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/**
 * Cápsulas HashB de las mutaciones de cargos (alta, edición, eliminación).
 *
 * Las firman los builders que montan formularios y listas; los controladores
 * las abren y toman de ellas los ids, de modo que un POST sin cápsula válida no
 * puede crear, cambiar ni borrar cargos de otras actividades o personas.
 */
final class CargoMutationCtx
{
    public const NUEVO = 'cargo_nuevo';
    public const EDITAR = 'cargo_editar';
    public const ELIMINAR = 'cargo_eliminar';

    /**
     * Cápsula del formulario. Con `id_item` es una edición (actividad y persona
     * fijas); sin él, un alta en la que solo se fijan los ids ya conocidos
     * (0 = lo elige el usuario en el formulario).
     */
    public static function guardar(int $idItem, int $idActiv, int $idNom): string
    {
        if ($idItem > 0) {
            return HashB::sign(self::EDITAR, ['id_item' => $idItem, 'id_activ' => $idActiv, 'id_nom' => $idNom]);
        }

        return HashB::sign(self::NUEVO, ['id_activ' => $idActiv, 'id_nom' => $idNom]);
    }

    public static function eliminar(int $idItem, int $elimAsis): string
    {
        return HashB::sign(self::ELIMINAR, ['id_item' => $idItem, 'elim_asis' => $elimAsis]);
    }

    /**
     * Abre la cápsula del formulario y sobrescribe en `$post` los ids que fija.
     *
     * @param array<string, mixed> $post
     * @return array<string, mixed>|null  null si la cápsula no es válida
     */
    public static function aplicarGuardar(array $post, string $accion): ?array
    {
        try {
            $ctx = HashB::open(FuncTablasSupport::inputString($post, 'ctx_guardar'), $accion);
        } catch (HashBInvalidException) {
            return null;
        }
        unset($post['ctx_guardar']);
        foreach (['id_item', 'id_activ', 'id_nom'] as $campo) {
            $valor = FuncTablasSupport::inputInt($ctx, $campo);
            if ($valor !== 0) {
                $post[$campo] = $valor;
            } elseif ($campo === 'id_item') {
                unset($post[$campo]);
            }
        }

        return $post;
    }

    /**
     * @param array<string, mixed> $post
     * @return array{id_item: int, elim_asis: int}|null  null si la cápsula no es válida
     */
    public static function abrirEliminar(array $post): ?array
    {
        try {
            $ctx = HashB::open(FuncTablasSupport::inputString($post, 'ctx_eliminar_cargo'), self::ELIMINAR);
        } catch (HashBInvalidException) {
            return null;
        }
        $idItem = FuncTablasSupport::inputInt($ctx, 'id_item');
        if ($idItem <= 0) {
            return null;
        }

        return ['id_item' => $idItem, 'elim_asis' => FuncTablasSupport::inputInt($ctx, 'elim_asis')];
    }
}
