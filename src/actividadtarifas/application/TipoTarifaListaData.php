<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;

/**
 * Data builder para la pantalla "catalogo de tipos de tarifa".
 */
final class TipoTarifaListaData
{
    public function __construct(
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
    ) {
    }

    /**
     * @return array{
     *   a_cabeceras: array<int,string>,
     *   a_valores: array<int,array<int,string|array{script:string,valor:string}>>,
     *   puede_editar: bool,
     *   puede_anadir: bool
     * }
     */
    public function execute(): array
    {
        $miSfsv = ConfigGlobal::mi_sfsv();
        $a_seccion = [1 => _("sv"), 2 => _("sf")];
        $a_modos_tarifa = TarifaModoId::getArrayModo();

        $cTipoTarifas = $this->tipoTarifaRepository->getTipoTarifas(['_ordre' => 'sfsv,letra']);

        $a_valores = [];
        $t = 0;
        foreach ($cTipoTarifas as $oTipoTarifa) {
            $t++;
            $id_tarifa = $oTipoTarifa->getId_tarifa();
            $modo = $oTipoTarifa->getModo();
            $letra = $oTipoTarifa->getLetra();
            $sfsv = $oTipoTarifa->getSfsv();
            $observ = $oTipoTarifa->getObserv();

            $a_valores[$t][1] = (string) $id_tarifa;
            $a_valores[$t][2] = $a_seccion[$sfsv] ?? '';
            $a_valores[$t][3] = (string) $letra;
            $a_valores[$t][4] = $a_modos_tarifa[$modo] ?? '';
            $a_valores[$t][5] = (string) $observ;
            if ($miSfsv === $sfsv && $this->havePermOficina('adl')) {
                $a_valores[$t][6] = [
                    'script' => "fnjs_modificar($id_tarifa)",
                    'valor' => _("modificar"),
                ];
            } else {
                $a_valores[$t][6] = '';
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

        $puede_anadir = $this->havePermOficina('adl')
            || $this->havePermOficina('pr')
            || $this->havePermOficina('calendario');

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'puede_editar' => $this->havePermOficina('adl'),
            'puede_anadir' => $puede_anadir,
        ];
    }

    private function havePermOficina(string $perm): bool
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return $oPerm instanceof XPermisos && $oPerm->have_perm_oficina($perm);
    }
}
