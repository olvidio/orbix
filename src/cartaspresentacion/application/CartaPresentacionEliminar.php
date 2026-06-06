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
    public function __construct(
        private CartaPresentacionRepositoryInterface $cartaPresentacionRepository,
    ) {
    }

    /**
     * @param array{id_ubi?: int, id_direccion?: int} $input
     * @return array{ok: bool, mensaje: string}
     */
    public function execute(array $input): array
    {
        $id_ubi = $input['id_ubi'] ?? 0;
        $id_direccion = $input['id_direccion'] ?? 0;
        if ($id_ubi === 0 || $id_direccion === 0) {
            return ['ok' => false, 'mensaje' => (string)_("Faltan id_ubi o id_direccion")];
        }

        $oCarta = $this->cartaPresentacionRepository->findById($id_ubi, $id_direccion);
        if ($oCarta === null) {
            return ['ok' => false, 'mensaje' => (string)_("Carta de presentacion no encontrada")];
        }

        if ($this->cartaPresentacionRepository->Eliminar($oCarta) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha borrado.")];
        }
        return ['ok' => true, 'mensaje' => ''];
    }
}
