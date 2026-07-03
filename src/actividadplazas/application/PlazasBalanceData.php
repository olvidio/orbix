<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\configuracion\domain\value_objects\ConfigSnapshot;

/**
 * Data builder del grid comparativo A vs B de plazas concedidas y
 * libres entre dos dl para un tipo de actividad.
 *
 * Sucesor de `apps/actividadplazas/controller/plazas_balance_dl.php`
 * (reemplaza la funcion suelta + `global` por metodos privados).
 */
final class PlazasBalanceData
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private ActividadPlazasRepositoryInterface $actividadPlazasRepository,
        private AsistenteActividadService $asistenteActividadService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *     error?:string,
     *     dlA:string,
     *     dlB:string,
     *     concedidasA2B:int,
     *     concedidasB2A:int,
     *     a_cabeceras:list<array<string, mixed>>,
     *     a_valores:array<int, array<string, mixed>>
     * }
     */
    public function execute(array $input): array
    {
        $Qdl = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'dl');
        $Qid_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tipo_activ');

        $dlA = ConfigGlobal::mi_delef();
        if ($Qdl === '') {
            return ['error' => (string)_("falta parametro dl"), 'dlA' => $dlA, 'dlB' => '', 'concedidasA2B' => 0, 'concedidasB2A' => 0, 'a_cabeceras' => [], 'a_valores' => []];
        }
        $dlB = $Qdl;
        if ($dlA === $dlB) {
            return ['error' => (string)_("no se puede comparar una dl consigo misma"), 'dlA' => $dlA, 'dlB' => $dlB, 'concedidasA2B' => 0, 'concedidasB2A' => 0, 'a_cabeceras' => [], 'a_valores' => []];
        }

        $oTipoActiv = new TiposActividades((int)$Qid_tipo_activ);
        $sactividad = $oTipoActiv->getActividadText();

        $inicurs = '';
        $fincurs = '';
        /** @var ConfigSnapshot $oConfig */
        $oConfig = $_SESSION['oConfig'];
        switch ($sactividad) {
            case 'ca':
            case 'cv':
                $any = $oConfig->any_final_curs('est');
                $inicurs = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('inicio', $any, 'est')->format('Y-m-d');
                $fincurs = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('fin', $any, 'est')->format('Y-m-d');
                break;
            case 'crt':
                $any = $oConfig->any_final_curs('crt');
                $inicurs = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('inicio', $any, 'crt')->format('Y-m-d');
                $fincurs = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('fin', $any, 'crt')->format('Y-m-d');
                break;
        }

        $status = (string)StatusId::ACTUAL;
        $mi_dl = $dlA;

        $a_plazasA = $this->plazasPorActividad($dlA, $dlB, 'tono1', $Qid_tipo_activ, $status, $inicurs, $fincurs, $mi_dl);
        $concedidasA2B = $a_plazasA['plazasB'];
        $a_valoresA = $a_plazasA['a_valores'];

        $a_plazasB = $this->plazasPorActividad($dlB, $dlA, 'tono2', $Qid_tipo_activ, $status, $inicurs, $fincurs, $mi_dl);
        $concedidasB2A = $a_plazasB['plazasB'];
        $a_valoresB = $a_plazasB['a_valores'];

        $a_valores = array_merge($a_valoresA, $a_valoresB);

        $a_cabeceras = [
            ['field' => 'id', 'name' => _("id_activ"), 'visible' => 'no'],
            ['field' => 'actividad', 'name' => ucfirst((string)_("actividad")), 'width' => 100, 'formatter' => 'clickFormatter'],
            ['field' => 'dlorg', 'name' => _("dl org"), 'width' => 10],
            ['name' => $dlA . '-c', 'title' => _("concedidas"), 'field' => $dlA . '-c', 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
            ['name' => $dlA . '-l', 'title' => _("libres"), 'field' => $dlA . '-l', 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
            ['name' => $dlB . '-c', 'title' => _("concedidas"), 'field' => $dlB . '-c', 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
            ['name' => $dlB . '-l', 'title' => _("libres"), 'field' => $dlB . '-l', 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'],
        ];

        return [
            'dlA' => $dlA,
            'dlB' => $dlB,
            'concedidasA2B' => (int)$concedidasA2B,
            'concedidasB2A' => (int)$concedidasB2A,
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }

    /**
     * @return array{plazasA:int, plazasB:int, a_valores:array<int, array<string, mixed>>}
     */
    private function plazasPorActividad(
        string $dlA,
        string $dlB,
        string $clase,
        string $Qid_tipo_activ,
        string $status,
        string $inicurs,
        string $fincurs,
        string $mi_dl
    ): array {
        $cDelegaciones = $this->delegacionRepository->getDelegaciones(['dl' => $dlA]);
        $id_dlA = $cDelegaciones !== [] ? (int) $cDelegaciones[0]->getIdDlVo()->value() : 0;
        $cDelegaciones = $this->delegacionRepository->getDelegaciones(['dl' => $dlB]);
        $id_dlB = $cDelegaciones !== [] ? (int) $cDelegaciones[0]->getIdDlVo()->value() : 0;

        $aWhereA = [
            'dl_org' => $dlA,
            'id_tipo_activ' => '^' . $Qid_tipo_activ,
            'status' => $status,
            'f_ini' => "'$inicurs','$fincurs'",
            '_ordre' => 'f_ini',
        ];
        $aOperador = ['id_tipo_activ' => '~', 'f_ini' => 'BETWEEN'];
        $cActividadesA = $this->actividadRepository->getActividades($aWhereA, $aOperador);

        $i = 0;
        $a_valores = [];
        $sumaConcedidasA = 0;
        $sumaConcedidasB = 0;
        $dlA_c = $dlA . '-c';
        $dlA_l = $dlA . '-l';
        $dlB_c = $dlB . '-c';
        $dlB_l = $dlB . '-l';
        foreach ($cActividadesA as $oActividad) {
            $i++;
            $id_activ = $oActividad->getId_activ();
            $nom = $oActividad->getNom_activ();
            $dl_org = $oActividad->getDl_org();
            $a_valores[$i]['id'] = $id_activ;
            $a_valores[$i]['actividad'] = $nom;
            $a_valores[$i]['dlorg'] = $dl_org;

            $concedidasA = 0;
            $cActividadPlazas = $this->actividadPlazasRepository->getActividadesPlazas(['id_dl' => $id_dlA, 'id_activ' => $id_activ]);
            foreach ($cActividadPlazas as $oActividadPlazas) {
                if ($dl_org === $oActividadPlazas->getDl_tabla()) {
                    $concedidasA = $oActividadPlazas->getPlazasVo()?->value() ?? 0;
                }
            }
            $ocupadasA = $this->asistenteActividadService->getPlazasOcupadasPorDl($id_activ, $dlA);
            $libresA = $ocupadasA < 0 ? '-' : $concedidasA - $ocupadasA;
            $sumaConcedidasA += $concedidasA;

            $concedidasB = 0;
            $cActividadPlazas = $this->actividadPlazasRepository->getActividadesPlazas(['id_dl' => $id_dlB, 'id_activ' => $id_activ]);
            foreach ($cActividadPlazas as $oActividadPlazas) {
                if ($dl_org === $oActividadPlazas->getDl_tabla()) {
                    $concedidasB = $oActividadPlazas->getPlazasVo()?->value() ?? 0;
                }
            }
            $ocupadasB = $this->asistenteActividadService->getPlazasOcupadasPorDl($id_activ, $dlB);
            $libresB = $ocupadasB < 0 ? '-' : $concedidasB - $ocupadasB;
            $sumaConcedidasB += $concedidasB;

            if ($dlA === $mi_dl) {
                $a_valores[$i][$dlA_c] = ['editable' => 'true', 'valor' => $concedidasA];
                $a_valores[$i][$dlA_l] = ['editable' => 'false', 'valor' => $libresA];
                $a_valores[$i][$dlB_c] = ['editable' => 'true', 'valor' => $concedidasB];
                $a_valores[$i][$dlB_l] = ['editable' => 'false', 'valor' => $libresB];
            } else {
                $a_valores[$i][$dlB_c] = ['editable' => 'false', 'valor' => $concedidasB];
                $a_valores[$i][$dlB_l] = ['editable' => 'false', 'valor' => $libresB];
                $a_valores[$i][$dlA_c] = ['editable' => 'true', 'valor' => $concedidasA];
                $a_valores[$i][$dlA_l] = ['editable' => 'false', 'valor' => $libresA];
            }
            $a_valores[$i]['clase'] = $clase;
        }

        return [
            'plazasA' => (int)$sumaConcedidasA,
            'plazasB' => (int)$sumaConcedidasB,
            'a_valores' => $a_valores,
        ];
    }
}
