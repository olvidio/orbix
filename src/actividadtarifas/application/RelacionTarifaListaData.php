<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use src\actividades\domain\entity\TiposActividades;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;

/**
 * Data builder: listado de relaciones `TipoTarifa` ↔ tipo de actividad.
 */
final class RelacionTarifaListaData
{
    public function __construct(
        private RelacionTarifaTipoActividadRepositoryInterface $relacionTarifaRepository,
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
    ) {
    }

    /**
     * @return array{
     *   a_cabeceras: array<int,string>,
     *   a_valores: array<int,array<int,string|array{script:string,valor:string}>>,
     *   puede_anadir: bool
     * }
     */
    public function execute(): array
    {
        $miSfsv = ConfigGlobal::mi_sfsv();
        $a_modos_tarifa = TarifaModoId::getArrayModo();

        $cRelaciones = $this->relacionTarifaRepository->getTipoActivTarifas([
            '_ordre' => 'substring(id_tipo_activ::text,1)',
        ]);

        $a_valores = [];
        $i = 0;
        foreach ($cRelaciones as $oRelacion) {
            $i++;
            $id_item = $oRelacion->getId_item();
            $id_tarifa = $oRelacion->getId_tarifa();
            $id_tipo_activ = $oRelacion->getId_tipo_activ();

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            $nom_tipo = $oTipoActividad->getNom();

            $oTipoTarifa = $this->tipoTarifaRepository->findById($id_tarifa);
            $letra = $oTipoTarifa !== null ? (string) $oTipoTarifa->getLetra() : '';
            $modo = $oTipoTarifa !== null ? (int) $oTipoTarifa->getModo() : 0;
            $nombre_tarifa = $letra . '  (' . ($a_modos_tarifa[$modo] ?? '') . ')';

            $a_valores[$i][1] = $nom_tipo;
            $a_valores[$i][2] = $nombre_tarifa;
            if ($miSfsv === $isfsv && $this->havePermOficina('adl')) {
                $a_valores[$i][3] = [
                    'script' => "fnjs_modificar($id_item)",
                    'valor' => _("modificar"),
                ];
            } else {
                $a_valores[$i][3] = '';
            }
        }

        $a_cabeceras = [
            _("tipo actividad"),
            _("tarifa"),
            '',
        ];

        $puede_anadir = $this->havePermOficina('adl')
            || $this->havePermOficina('pr')
            || $this->havePermOficina('calendario');

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'puede_anadir' => $puede_anadir,
        ];
    }

    private function havePermOficina(string $perm): bool
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return $oPerm instanceof XPermisos && $oPerm->have_perm_oficina($perm);
    }
}
