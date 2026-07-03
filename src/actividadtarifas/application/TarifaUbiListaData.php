<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividades\domain\entity\TiposActividades;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\shared\security\HashB;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;

/**
 * Data builder: listado de `TarifaUbi` por `id_ubi` + `year`.
 */
final class TarifaUbiListaData
{
    public function __construct(
        private TarifaUbiRepositoryInterface $tarifaUbiRepository,
        private RelacionTarifaTipoActividadRepositoryInterface $relacionTarifaRepository,
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   a_cabeceras: array<int,string>,
     *   a_valores: array<int,array<int,string|array{clase?:string,script?:string,valor?:string}>>,
     *   any_anterior: int,
     *   any_actual: int,
     *   puede_anadir: bool,
     *   id_ubi: int,
     *   year: int,
     *   token_copiar: string
     * }
     */
    public function execute(array $input): array
    {
        $id_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $year = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'year');

        $miSfsv = ConfigGlobal::mi_sfsv();
        $a_seccion = [1 => _("sv"), 2 => _("sf")];
        $aTipoSerie = SerieId::getArraySerie();

        $cTarifas = [];
        if ($id_ubi !== 0 && $year !== 0) {
            $cTarifas = $this->tarifaUbiRepository->getTarifaUbis([
                'id_ubi' => $id_ubi,
                'year' => $year,
                '_ordre' => 'year,id_tarifa',
            ]);
        }

        $a_valores = [];
        $i = 0;
        foreach ($cTarifas as $oTarifaUbi) {
            $i++;
            $id_item = $oTarifaUbi->getId_item();
            $id_tarifa = $oTarifaUbi->getId_tarifa();
            $id_serie = $oTarifaUbi->getId_serie();
            $cantidad = $oTarifaUbi->getCantidad();

            $cantidad_txt = "$cantidad " . _("€");

            $cTipoActivTarifas = $this->relacionTarifaRepository->getTipoActivTarifas(['id_tarifa' => $id_tarifa]);
            $aplicado_a = '';
            $t = 0;
            foreach ($cTipoActivTarifas as $oRelacion) {
                $t++;
                $oTipoActividad = new TiposActividades($oRelacion->getId_tipo_activ());
                if ($t > 1) {
                    $aplicado_a .= ', ';
                }
                $aplicado_a .= $oTipoActividad->getNomGral();
            }

            $oTipoTarifa = $this->tipoTarifaRepository->findById($id_tarifa);
            $seccion = $oTipoTarifa !== null ? $oTipoTarifa->getSfsv() : 0;
            $letra = $oTipoTarifa !== null ? (string) $oTipoTarifa->getLetra() : '';
            $letra_serie = $letra . ' (' . ($aTipoSerie[$id_serie] ?? '') . ')';

            $a_valores[$i][1] = $a_seccion[$seccion] ?? '';
            if ($miSfsv === $seccion && $this->havePermOficina('adl')) {
                $letraJs = json_encode($letra, JSON_UNESCAPED_SLASHES);
                $a_valores[$i][2] = [
                    'script' => "fnjs_modificar($id_item,$letraJs)",
                    'valor' => $letra_serie,
                ];
            } else {
                $a_valores[$i][2] = $letra_serie;
            }
            $a_valores[$i][3] = $aplicado_a;
            $a_valores[$i][4] = '0';
            $a_valores[$i][5] = ['clase' => 'derecha', 'valor' => $cantidad_txt];
            $a_valores[$i][6] = $oTipoTarifa !== null ? $oTipoTarifa->getModoTxt() : '';
        }

        if ($a_valores !== []) {
            $secc = [];
            $letr = [];
            foreach ($a_valores as $key => $row) {
                $secc[$key] = $row[1];
                $cell = $row[2];
                if (is_array($cell)) {
                    $letr[$key] = (string) $cell['valor'];
                } else {
                    $letr[$key] = (string) $cell;
                }
            }
            array_multisort($secc, SORT_DESC, $letr, SORT_ASC, SORT_STRING, $a_valores);
        }

        $a_cabeceras = [
            _("sección"),
            _("tarifa"),
            _("se aplica a"),
            _("minimo"),
            _("precio"),
            _("método"),
        ];

        $puede_anadir = $id_ubi !== 0 && (
            $this->havePermOficina('adl')
            || $this->havePermOficina('pr')
            || $this->havePermOficina('calendario')
        );

        $token_copiar = ($puede_anadir && $year !== 0)
            ? HashB::sign('tarifa_ubi_copiar', ['id_ubi' => $id_ubi, 'year' => $year])
            : '';

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'any_anterior' => $year > 0 ? $year - 1 : 0,
            'any_actual' => $year,
            'puede_anadir' => $puede_anadir,
            'id_ubi' => $id_ubi,
            'year' => $year,
            'token_copiar' => $token_copiar,
        ];
    }

    private function havePermOficina(string $perm): bool
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return $oPerm instanceof XPermisos && $oPerm->have_perm_oficina($perm);
    }
}
