<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use frontend\shared\web\Periodo;
use src\actividades\domain\entity\TiposActividades;
use function src\shared\domain\helpers\input_string;

/**
 * Data builder de la pantalla principal `gestion_plazas`.
 *
 * Calcula el grupo de estudios de mi dl, la lista de actividades del
 * periodo y las plazas concedidas/pedidas por dl. Devuelve arrays
 * neutros; el controlador frontend monta el `frontend\shared\web\TablaEditable`.
 *
 * Sucesor de la mayor parte de `apps/actividadplazas/controller/gestion_plazas.php`.
 */
final class GestionPlazasData
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private ActividadPlazasRepositoryInterface $actividadPlazasRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *     a_cabeceras: list<array<string, mixed>>,
     *     a_valores: array<int, array<string, mixed>>,
     *     a_grupo: array<string,int>,
     *     extendida: bool,
     *     id_tipo_activ: string,
     *     sactividad: string,
     *     year: int|string,
     *     periodo: string,
     *     empiezamin: string,
     *     empiezamax: string
     * }
     */
    public function execute(array $input): array
    {
        $Qid_tipo_activ = input_string($input, 'id_tipo_activ');
        $Qyear = input_string($input, 'year');
        $Qperiodo = input_string($input, 'periodo');
        $Qempiezamin = input_string($input, 'empiezamin');
        $Qempiezamax = input_string($input, 'empiezamax');
        $Qsactividad = '';
        $extendida = false;

        if ($Qid_tipo_activ === '') {
            $Qssfsv = 'sv';
            if (ConfigGlobal::mi_sfsv() === 2) {
                $Qssfsv = 'sf';
            }
            $Qsasistentes = input_string($input, 'sasistentes');
            $Qsactividad = input_string($input, 'sactividad');
            $Qsactividad2 = input_string($input, 'sactividad2');
            if ($Qsactividad2 !== '') {
                $extendida = true;
            }
            $oTipoActiv = new TiposActividades();
            $oTipoActiv->setSfsvText($Qssfsv);
            $oTipoActiv->setAsistentesText($Qsasistentes);
            if ($Qsactividad2 !== '') {
                $oTipoActiv->setActividad2DigitosText($Qsactividad2);
            } else {
                $oTipoActiv->setActividadText($Qsactividad);
            }
            $Qid_tipo_activ = (string)$oTipoActiv->getId_tipo_activ();
        } else {
            $oTipoActiv = new TiposActividades($Qid_tipo_activ);
            $Qsactividad = (string)$oTipoActiv->getActividadText();
        }

        if ($Qyear === '') {
            $Qyear = (int)date('Y');
        }
        if ($Qperiodo === '') {
            switch ($Qsactividad) {
                case 'ca':
                case 'cv':
                    $Qperiodo = 'curso_ca';
                    break;
                case 'crt':
                case 'cve':
                    $Qperiodo = 'curso_crt';
                    break;
            }
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setAny((string)$Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $periodoCalculo = ($Qempiezamin !== '' && $Qempiezamax !== '')
            ? 'otro'
            : $Qperiodo;
        $oPeriodo->setPeriodo($periodoCalculo);
        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();

        $mi_reg = ConfigGlobal::mi_region();
        $mi_dl = ConfigGlobal::mi_delef();
        $cDelegaciones = $this->delegacionRepository->getDelegaciones(['region' => $mi_reg, 'dl' => $mi_dl]);
        if (empty($cDelegaciones)) {
            return [
                'a_cabeceras' => [],
                'a_valores' => [],
                'a_grupo' => [],
                'extendida' => $extendida,
                'id_tipo_activ' => $Qid_tipo_activ,
                'sactividad' => $Qsactividad,
                'year' => $Qyear,
                'periodo' => $Qperiodo,
                'empiezamin' => $Qempiezamin,
                'empiezamax' => $Qempiezamax,
            ];
        }
        $oMiDelegacion = $cDelegaciones[0];
        $grupo_estudios = $oMiDelegacion->getGrupoEstudiosVo()?->value();

        if ($grupo_estudios === null) {
            $cDelegaciones = [$oMiDelegacion];
        } else {
            $cDelegaciones = $this->delegacionRepository->getDelegaciones([
                'grupo_estudios' => $grupo_estudios,
                'active' => true,
                '_ordre' => 'region,dl',
            ]);
        }

        // Actividades publicadas en el periodo, agrupadas por dl_org.
        $a_grupo = [];
        $cActividades = [];
        $id_tipo_activ_regex = '^' . $Qid_tipo_activ;
        $status = StatusId::ACTUAL;
        foreach ($cDelegaciones as $oDelegacion) {
            $dl = (string) $oDelegacion->getDlVo()->value();
            $id_dl = (int) $oDelegacion->getIdDlVo()->value();
            $a_grupo[$dl] = $id_dl;
            $aWhere = [
                'dl_org' => $dl,
                'id_tipo_activ' => $id_tipo_activ_regex,
                'status' => $status,
                'f_ini' => "'$inicioIso','$finIso'",
                '_ordre' => 'publicado,f_ini',
            ];
            $aOperador = ['id_tipo_activ' => '~', 'f_ini' => 'BETWEEN'];
            $cActividades1 = $this->actividadRepository->getActividades($aWhere, $aOperador);
            array_push($cActividades, ...$cActividades1);
        }

        // Plazas por actividad + dl.
        $i = 0;
        $a_valores = [];
        foreach ($cActividades as $oActividad) {
            $i++;
            $id_activ = $oActividad->getId_activ();
            $nom = $oActividad->getNom_activ();
            $dl_org = $oActividad->getDl_org();
            $plazas_totales = $oActividad->getPlazas();
            if (empty($plazas_totales)) {
                $plazas_totales = '?';
                $id_ubi = $oActividad->getId_ubi();
                if ($id_ubi !== null) {
                    $oCasa = Ubi::NewUbi($id_ubi);
                    if ($oCasa !== null && method_exists($oCasa, 'getPlazas')) {
                        $plazas_totales = $oCasa->getPlazas();
                    }
                }
            }
            if ($mi_dl === $dl_org) {
                $a_valores[$i]['clase'] = 'tono2';
            }
            $a_valores[$i]['id'] = $id_activ;
            $a_valores[$i]['actividad'] = $nom;
            $a_valores[$i]['dlorg'] = $dl_org;
            $a_valores[$i]['tot'] = [
                'editable' => $mi_dl === $dl_org ? 'true' : 'false',
                'valor' => $plazas_totales,
            ];

            foreach ($a_grupo as $dl => $id_dl) {
                $pedidas = '-';
                $concedidas = '-';
                $cActividadPlazas = $this->actividadPlazasRepository->getActividadesPlazas([
                    'id_dl' => $id_dl,
                    'id_activ' => $id_activ,
                ]);
                foreach ($cActividadPlazas as $oActividadPlazas) {
                    $dl_tabla = $oActividadPlazas->getDl_tabla();
                    if ($dl_org === $dl_tabla) {
                        $concedidas = $oActividadPlazas->getPlazas();
                    } else {
                        $pedidas = $oActividadPlazas->getPlazas();
                    }
                }
                $dl_c = $dl . '-c';
                $dl_p = $dl . '-p';
                if ($mi_dl === $dl) {
                    if ($mi_dl === $dl_org) {
                        $a_valores[$i][$dl_c] = ['editable' => 'true', 'valor' => $concedidas];
                        $a_valores[$i][$dl_p] = ['editable' => 'false', 'valor' => $pedidas];
                    } else {
                        $a_valores[$i][$dl_c] = ['editable' => 'false', 'valor' => $concedidas];
                        $a_valores[$i][$dl_p] = ['editable' => 'true', 'valor' => $pedidas];
                    }
                } else {
                    if ($mi_dl === $dl_org) {
                        $a_valores[$i][$dl_c] = ['editable' => 'true', 'valor' => $concedidas];
                    } else {
                        $a_valores[$i][$dl_c] = ['editable' => 'false', 'valor' => $concedidas];
                    }
                    $a_valores[$i][$dl_p] = ['editable' => 'false', 'valor' => $pedidas];
                }
            }
        }

        $a_cabeceras = [
            ['name' => _("id_activ"), 'field' => 'id', 'visible' => 'no'],
            ['name' => _("actividad"), 'field' => 'actividad', 'width' => 200, 'formatter' => 'clickFormatter'],
            ['name' => _("org"), 'title' => _("organiza"), 'field' => 'dlorg', 'width' => 40],
            [
                'name' => _("total"),
                'title' => _("totales actividad"),
                'field' => 'tot',
                'width' => 40,
                'editor' => 'Slick.Editors.Integer',
                'formatter' => 'cssFormatter',
            ],
        ];
        foreach ($a_grupo as $dl => $_id_dl) {
            $a_cabeceras[] = [
                'name' => $dl . '-c',
                'title' => _("concedidas"),
                'field' => $dl . '-c',
                'width' => 15,
                'editor' => 'Slick.Editors.Integer',
                'formatter' => 'cssFormatter',
            ];
            $a_cabeceras[] = [
                'name' => $dl . '-p',
                'title' => _("pedidas"),
                'field' => $dl . '-p',
                'width' => 15,
                'editor' => 'Slick.Editors.Integer',
                'formatter' => 'cssFormatter',
            ];
        }

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'a_grupo' => $a_grupo,
            'extendida' => $extendida,
            'id_tipo_activ' => $Qid_tipo_activ,
            'sactividad' => $Qsactividad,
            'year' => $Qyear,
            'periodo' => $Qperiodo,
            'empiezamin' => $Qempiezamin,
            'empiezamax' => $Qempiezamax,
        ];
    }
}
