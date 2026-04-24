<?php

namespace src\cartaspresentacion\application;

use src\shared\config\ConfigGlobal;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionExRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Ubi;

/**
 * Mutacion: crea o actualiza una `CartaPresentacion`.
 *
 * Sucesor de la rama `que_mod=update` del dispatcher
 * `apps/cartaspresentacion/controller/cartas_presentacion_ajax.php`.
 *
 * Al terminar, ejecuta `sanear()` — igual que el controlador legacy —
 * para eliminar cartas cuyas direcciones ya no pertenecen al centro.
 */
final class CartaPresentacionUpdate
{
    /**
     * @param array<string,mixed> $input
     * @return array{ok: bool, mensaje: string}
     */
    public static function execute(array $input): array
    {
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $id_direccion = (int)($input['id_direccion'] ?? 0);
        if ($id_ubi === 0 || $id_direccion === 0) {
            return ['ok' => false, 'mensaje' => (string)_("Faltan id_ubi o id_direccion")];
        }

        $repoCarta = $GLOBALS['container']->get(CartaPresentacionRepositoryInterface::class);
        $oCarta = $repoCarta->findById($id_ubi, $id_direccion);

        if ($oCarta === null) {
            $repoWrite = self::resolveWriteRepo($id_ubi);
            if ($repoWrite === null) {
                return ['ok' => false, 'mensaje' => (string)_("No puede modificar datos de otra dl")];
            }
            $oCarta = new CartaPresentacion();
            $oCarta->setId_ubi($id_ubi);
            $oCarta->setId_direccion($id_direccion);
        } else {
            $repoWrite = $repoCarta;
        }

        $oCarta->setPres_nom((string)($input['pres_nom'] ?? ''));
        $oCarta->setPres_telf((string)($input['pres_telf'] ?? ''));
        $oCarta->setPres_mail((string)($input['pres_mail'] ?? ''));
        $oCarta->setZona((string)($input['zona'] ?? ''));
        $oCarta->setObserv((string)($input['observ'] ?? ''));

        if ($repoWrite->Guardar($oCarta) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha guardado.")];
        }

        self::sanear();
        return ['ok' => true, 'mensaje' => ''];
    }

    /**
     * Decide, al crear una carta nueva, que repositorio usar segun si el
     * centro es de la propia dl o un `cr` extranjero.
     */
    private static function resolveWriteRepo(int $id_ubi): mixed
    {
        $repoCentro = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $oCentro = $repoCentro->findById($id_ubi);
        if ($oCentro === null) {
            return null;
        }
        if ((string)$oCentro->getDl() === ConfigGlobal::mi_delef()) {
            return $GLOBALS['container']->get(CartaPresentacionDlRepositoryInterface::class);
        }
        if ((string)$oCentro->getTipo_ctr() === 'cr') {
            return $GLOBALS['container']->get(CartaPresentacionExRepositoryInterface::class);
        }
        return null;
    }

    /**
     * Elimina cartas de presentacion de la dl cuya direccion ya no
     * pertenece al centro (salvaguarda despues de un update). Copia de
     * `sanear()` del dispatcher legacy.
     */
    private static function sanear(): void
    {
        $repo = $GLOBALS['container']->get(CartaPresentacionDlRepositoryInterface::class);
        $cCartas = $repo->getCartasPresentacion();
        foreach ($cCartas as $oCarta) {
            $id_ubi = $oCarta->getId_ubi();
            $id_direccion = $oCarta->getId_direccion();

            $oUbi = Ubi::NewUbi($id_ubi);
            if ($oUbi === null) {
                continue;
            }
            $cDirecciones = $oUbi->getDirecciones();
            $a_direcciones_ctr = [];
            foreach ($cDirecciones as $oDir) {
                $a_direcciones_ctr[] = $oDir->getId_direccion();
            }
            if (!in_array($id_direccion, $a_direcciones_ctr, false)) {
                $oCarta->DBEliminar();
            }
        }
    }
}
