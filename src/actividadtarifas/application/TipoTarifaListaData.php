<?php

namespace src\actividadtarifas\application;

use core\ConfigGlobal;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\value_objects\TarifaModoId;

/**
 * Data builder para la pantalla "catalogo de tipos de tarifa".
 *
 * Devuelve cabeceras + filas serializables y las banderas de permiso
 * que el frontend necesita para pintar el `web\Lista` y decidir si
 * muestra el enlace "añadir tarifa" / "modificar".
 *
 * Sucesor de la rama `tarifas` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */
final class TipoTarifaListaData
{
    /**
     * @return array{
     *   a_cabeceras: array<int,string>,
     *   a_valores: array<int,array<int,string|array{script:string,valor:string}>>,
     *   puede_editar: bool,
     *   puede_anadir: bool
     * }
     */
    public static function execute(): array
    {
        $miSfsv = ConfigGlobal::mi_sfsv();
        $a_seccion = [1 => _("sv"), 2 => _("sf")];
        $a_modos_tarifa = TarifaModoId::getArrayModo();

        $repo = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $cTipoTarifas = $repo->getTipoTarifas(['_ordre' => 'sfsv,letra']);

        $a_valores = [];
        $t = 0;
        if (is_array($cTipoTarifas)) {
            foreach ($cTipoTarifas as $oTipoTarifa) {
                $t++;
                $id_tarifa = $oTipoTarifa->getId_tarifa();
                $modo = $oTipoTarifa->getModo();
                $letra = $oTipoTarifa->getLetra();
                $sfsv = $oTipoTarifa->getSfsv();
                $observ = $oTipoTarifa->getObserv();

                $a_valores[$t][1] = (string)$id_tarifa;
                $a_valores[$t][2] = $a_seccion[$sfsv] ?? '';
                $a_valores[$t][3] = (string)$letra;
                $a_valores[$t][4] = $a_modos_tarifa[$modo] ?? '';
                $a_valores[$t][5] = (string)$observ;
                if ($miSfsv === $sfsv && $_SESSION['oPerm']->have_perm_oficina('adl')) {
                    $a_valores[$t][6] = [
                        'script' => "fnjs_modificar($id_tarifa)",
                        'valor' => _("modificar"),
                    ];
                } else {
                    $a_valores[$t][6] = '';
                }
            }
        }

        $a_cabeceras = [
            _("id_tarifa"),
            _("sección"),
            _("letra"),
            _("modo"),
            _("observ"),
            '',
        ];

        $puede_anadir = $_SESSION['oPerm']->have_perm_oficina('adl')
            || $_SESSION['oPerm']->have_perm_oficina('pr')
            || $_SESSION['oPerm']->have_perm_oficina('calendario');

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'puede_editar' => (bool)$_SESSION['oPerm']->have_perm_oficina('adl'),
            'puede_anadir' => (bool)$puede_anadir,
        ];
    }
}
