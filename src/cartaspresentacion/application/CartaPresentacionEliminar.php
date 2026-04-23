<?php

namespace src\cartaspresentacion\application;

use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;

/**
 * Mutacion: elimina una `CartaPresentacion` por `id_ubi` + `id_direccion`.
 *
 * Sucesor de la rama `que_mod=eliminar` del dispatcher
 * `apps/cartaspresentacion/controller/cartas_presentacion_ajax.php`.
 */
final class CartaPresentacionEliminar
{
    /**
     * @param array{id_ubi?: int|string, id_direccion?: int|string} $input
     * @return array{ok: bool, mensaje: string}
     */
    public static function execute(array $input): array
    {
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $id_direccion = (int)($input['id_direccion'] ?? 0);
        if ($id_ubi === 0 || $id_direccion === 0) {
            return ['ok' => false, 'mensaje' => (string)_("Faltan id_ubi o id_direccion")];
        }

        $repo = $GLOBALS['container']->get(CartaPresentacionRepositoryInterface::class);
        $oCarta = $repo->findById($id_ubi, $id_direccion);
        if ($oCarta === null) {
            return ['ok' => false, 'mensaje' => (string)_("Carta de presentacion no encontrada")];
        }

        if ($repo->Eliminar($oCarta) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha borrado.")];
        }
        return ['ok' => true, 'mensaje' => ''];
    }
}
