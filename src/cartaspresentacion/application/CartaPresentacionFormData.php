<?php

namespace src\cartaspresentacion\application;

use src\shared\config\ConfigGlobal;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use frontend\shared\security\HashFront;

/**
 * Data builder: datos del formulario de modificacion de una
 * `CartaPresentacion`.
 *
 * Sucesor de la rama `que_mod=form_pres` del dispatcher
 * `apps/cartaspresentacion/controller/cartas_presentacion_ajax.php`.
 */
final class CartaPresentacionFormData
{
    /**
     * @param array{id_ubi?: int|string, id_direccion?: int|string} $input
     * @return array{
     *   ok: bool,
     *   mensaje: string,
     *   id_ubi: int,
     *   id_direccion: int,
     *   nombre_ubi: string,
     *   pres_nom: string,
     *   pres_telf: string,
     *   pres_mail: string,
     *   zona: string,
     *   observ: string,
     *   hash_update_html: string
     * }
     */
    public static function execute(array $input): array
    {
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $id_direccion = (int)($input['id_direccion'] ?? 0);

        $base = [
            'ok' => false,
            'mensaje' => '',
            'id_ubi' => $id_ubi,
            'id_direccion' => $id_direccion,
            'nombre_ubi' => '',
            'pres_nom' => '',
            'pres_telf' => '',
            'pres_mail' => '',
            'zona' => '',
            'observ' => '',
            'hash_update_html' => '',
        ];

        if ($id_ubi === 0 || $id_direccion === 0) {
            $base['mensaje'] = (string)_("Faltan id_ubi o id_direccion");
            return $base;
        }

        $repoDireccion = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);
        $repoCentro = $GLOBALS['container']->get(CentroRepositoryInterface::class);

        $oDireccion = $repoDireccion->findById($id_direccion);
        $nom_sede = (string)($oDireccion?->getNom_sede() ?? '');

        $oCentro = $repoCentro->findById($id_ubi);
        if ($oCentro === null) {
            $base['mensaje'] = (string)_("Centro no encontrado");
            return $base;
        }
        $nombre_ubi = (string)$oCentro->getNombre_ubi();
        $nombre_ubi .= $nom_sede === '' ? '' : " ($nom_sede)";

        // Valida permisos: solo se puede editar si es de la propia dl o es un `cr`.
        $dl = (string)$oCentro->getDl();
        $tipo_ctr = (string)$oCentro->getTipo_ctr();
        if ($dl !== ConfigGlobal::mi_delef() && $tipo_ctr !== 'cr') {
            $base['nombre_ubi'] = $nombre_ubi;
            $base['mensaje'] = (string)_("No puede modificar datos de otra dl");
            return $base;
        }

        $repoCarta = $GLOBALS['container']->get(CartaPresentacionRepositoryInterface::class);
        $oCarta = $repoCarta->findById($id_ubi, $id_direccion);

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $oHashUpdate = new HashFront();
        $oHashUpdate->setUrl($web . '/src/cartaspresentacion/carta_presentacion_update');
        $oHashUpdate->setArrayCamposHidden([
            'id_ubi' => $id_ubi,
            'id_direccion' => $id_direccion,
        ]);
        $oHashUpdate->setCamposForm('pres_nom!pres_telf!pres_mail!zona!observ');

        return [
            'ok' => true,
            'mensaje' => '',
            'id_ubi' => $id_ubi,
            'id_direccion' => $id_direccion,
            'nombre_ubi' => $nombre_ubi,
            'pres_nom' => (string)($oCarta?->getPres_nom() ?? ''),
            'pres_telf' => (string)($oCarta?->getPres_telf() ?? ''),
            'pres_mail' => (string)($oCarta?->getPres_mail() ?? ''),
            'zona' => (string)($oCarta?->getZona() ?? ''),
            'observ' => (string)($oCarta?->getObserv() ?? ''),
            'hash_update_html' => $oHashUpdate->getCamposHtml(),
        ];
    }
}
