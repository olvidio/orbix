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
    public function __construct(
        private CartaPresentacionRepositoryInterface $cartaPresentacionRepository,
        private CartaPresentacionDlRepositoryInterface $cartaPresentacionDlRepository,
        private CartaPresentacionExRepositoryInterface $cartaPresentacionExRepository,
        private CentroRepositoryInterface $centroRepository,
    ) {
    }

    /**
     * @param array{
     *   id_ubi?: int,
     *   id_direccion?: int,
     *   pres_nom?: string,
     *   pres_telf?: string,
     *   pres_mail?: string,
     *   zona?: string,
     *   observ?: string
     * } $input
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
            $repoWrite = $this->resolveWriteRepo($id_ubi);
            if ($repoWrite === null) {
                return ['ok' => false, 'mensaje' => (string)_("No puede modificar datos de otra dl")];
            }
            $oCarta = new CartaPresentacion();
            $oCarta->setId_ubi($id_ubi);
            $oCarta->setId_direccion($id_direccion);
        } else {
            $repoWrite = $this->cartaPresentacionRepository;
        }

        $oCarta->setPres_nom($input['pres_nom'] ?? '');
        $oCarta->setPres_telf($input['pres_telf'] ?? '');
        $oCarta->setPres_mail($input['pres_mail'] ?? '');
        $oCarta->setZona($input['zona'] ?? '');
        $oCarta->setObserv($input['observ'] ?? '');

        if ($repoWrite->Guardar($oCarta) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha guardado.")];
        }

        $this->sanear();
        return ['ok' => true, 'mensaje' => ''];
    }

    /**
     * Decide, al crear una carta nueva, que repositorio usar segun si el
     * centro es de la propia dl o un `cr` extranjero.
     */
    private function resolveWriteRepo(int $id_ubi): ?CartaPresentacionRepositoryInterface
    {
        $oCentro = $this->centroRepository->findById($id_ubi);
        if ($oCentro === null) {
            return null;
        }
        if ((string)$oCentro->getDl() === ConfigGlobal::mi_delef()) {
            return $this->cartaPresentacionDlRepository;
        }
        if ((string)$oCentro->getTipo_ctr() === 'cr') {
            return $this->cartaPresentacionExRepository;
        }
        return null;
    }

    /**
     * Elimina cartas de presentacion de la dl cuya direccion ya no
     * pertenece al centro (salvaguarda despues de un update). Copia de
     * `sanear()` del dispatcher legacy.
     */
    private function sanear(): void
    {
        $cCartas = $this->cartaPresentacionDlRepository->getCartasPresentacion();
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
                $this->cartaPresentacionDlRepository->Eliminar($oCarta);
            }
        }
    }
}
