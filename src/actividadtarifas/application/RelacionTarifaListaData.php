<?php

namespace src\actividadtarifas\application;

use core\ConfigGlobal;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use web\TiposActividades;

/**
 * Data builder: listado de relaciones `TipoTarifa` ↔ tipo de
 * actividad.
 *
 * Sucesor de la rama `get` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_tipo_actividad_ajax.php`.
 */
final class RelacionTarifaListaData
{
    /**
     * @return array{
     *   a_cabeceras: array<int,string>,
     *   a_valores: array<int,array<int,string|array{script:string,valor:string}>>,
     *   puede_anadir: bool
     * }
     */
    public static function execute(): array
    {
        $miSfsv = ConfigGlobal::mi_sfsv();
        $a_modos_tarifa = TarifaModoId::getArrayModo();

        $repoRel = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repoTipoTarifa = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);

        $cRelaciones = $repoRel->getTipoActivTarifas(['_ordre' => 'substring(id_tipo_activ::text,1)']);

        $a_valores = [];
        $i = 0;
        if (is_array($cRelaciones)) {
            foreach ($cRelaciones as $oRelacion) {
                $i++;
                $id_item = $oRelacion->getId_item();
                $id_tarifa = $oRelacion->getId_tarifa();
                $id_tipo_activ = $oRelacion->getId_tipo_activ();

                $oTipoActividad = new TiposActividades($id_tipo_activ);
                $isfsv = $oTipoActividad->getSfsvId();
                $nom_tipo = $oTipoActividad->getNom();

                $oTipoTarifa = $repoTipoTarifa->findById($id_tarifa);
                $letra = $oTipoTarifa !== null ? (string)$oTipoTarifa->getLetra() : '';
                $modo = $oTipoTarifa !== null ? (int)$oTipoTarifa->getModo() : 0;
                $nombre_tarifa = $letra . '  (' . ($a_modos_tarifa[$modo] ?? '') . ')';

                $a_valores[$i][1] = $nom_tipo;
                $a_valores[$i][2] = $nombre_tarifa;
                if ($miSfsv === $isfsv && $_SESSION['oPerm']->have_perm_oficina('adl')) {
                    $a_valores[$i][3] = [
                        'script' => "fnjs_modificar($id_item)",
                        'valor' => _("modificar"),
                    ];
                } else {
                    $a_valores[$i][3] = '';
                }
            }
        }

        $a_cabeceras = [
            _("tipo actividad"),
            _("tarifa"),
            '',
        ];

        $puede_anadir = $_SESSION['oPerm']->have_perm_oficina('adl')
            || $_SESSION['oPerm']->have_perm_oficina('pr')
            || $_SESSION['oPerm']->have_perm_oficina('calendario');

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'puede_anadir' => (bool)$puede_anadir,
        ];
    }
}
