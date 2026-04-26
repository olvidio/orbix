<?php

namespace src\asistentes\application;

use frontend\shared\web\PeriodoQue;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use function src\shared\domain\helpers\strtoupper_dlb;
use web\Hash;

/**
 * Formulario de elección de centro / periodo (`que_ctr_lista.php`).
 */
final class QueCtrListaData
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function build(array $input): array
    {
        require_once dirname(__DIR__, 2) . '/shared/domain/helpers/func_tablas.php';

        $tipo = (string)($input['tipo'] ?? '');
        $ssfsv = (string)($input['ssfsv'] ?? '');
        $Qlista = (string)($input['lista'] ?? '');
        $Qsasistentes = (string)($input['sasistentes'] ?? '');
        $Qsactividad = (string)($input['sactividad'] ?? '');
        $Qn_agd = (string)($input['n_agd'] ?? '');
        $Qid_ubi = (int)($input['id_ubi'] ?? 0);
        $Qyear = (int)($input['year'] ?? 0);
        $Qperiodo = (string)($input['periodo'] ?? '');

        $tituloGros = '';
        $titulo = '';
        $action = '';
        $a_camposHidden = [];

        switch ($Qlista) {
            case 'profesion':
                $tituloGros = ucfirst(_("listado de profesiones por centros"));
                $titulo = ucfirst(_("buscar en uno o varios centros"));
                $action = 'programas/sm-agd/lista_profesion.php';
                $a_camposHidden = ['tipo' => $tipo];
                break;
            case 'ctrex':
            case 'list_activ':
                $titulo = ucfirst(_("actividades de personas por centros de la delegación"));
                $tituloGros = ucfirst(_("¿qué centro interesa?"));
                $nomUbi = ucfirst(_("nombre del centro"));
                $action = 'frontend/asistentes/controller/lista_activ_ctr.php';
                $a_camposHidden = [
                    'tipo' => $tipo,
                    'ssfsv' => $ssfsv,
                    'sasistentes' => $Qsasistentes,
                    'sactividad' => $Qsactividad,
                ];
                break;
            case 'list_est':
                $titulo = ucfirst(_("estudios en actividades de personas por centros de la delegación"));
                $tituloGros = ucfirst(_("¿qué centro interesa?"));
                $action = 'frontend/asistentes/controller/lista_est_ctr.php';
                $a_camposHidden = [
                    'tipo' => $tipo,
                    'ssfsv' => $ssfsv,
                    'sasistentes' => $Qsasistentes,
                    'sactividad' => $Qsactividad,
                ];
                break;
            default:
                $tituloGros = '';
                $titulo = '';
                $action = '';
                $a_camposHidden = [];
        }

        $n = '';
        $nj = '';
        $nm = '';
        $a = '';
        $sssc = '';
        $nax = '';
        $c = '';

        switch ($Qn_agd) {
            case 'n':
                $n = 'checked';
                break;
            case 'nj':
                $nj = 'checked';
                break;
            case 'nm':
                $nm = 'checked';
                break;
            case 'a':
                $a = 'checked';
                break;
            case 'sssc':
                $sssc = 'checked';
                break;
            case 'nax':
                $nax = 'checked';
                break;
            case 'c':
                $c = 'checked';
                break;
        }

        $oGesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $aOpciones = $oGesCentros->getArrayCentros("WHERE active = 't' AND tipo_ctr ~ '^a|^n' ");
        $aOpcionesSerialized = $aOpciones;

        $oHash = new Hash();
        $oHash->setCamposForm('n_agd!empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
        $oHash->setcamposNo('id_ubi');
        $oHash->setArraycamposHidden($a_camposHidden);

        $periodo_form_html = '';
        if ($Qlista === 'list_activ' || $Qlista === 'list_est') {
            $aOpcionesP = [
                'curso_ca' => _("curso ca"),
                'curso_crt' => _("curso crt"),
                'tot_any' => _("todo el año"),
                'separador' => '---------',
                'otro' => _("otro"),
            ];
            $oFormP = new PeriodoQue();
            $oFormP->setFormName('modifica');
            $oFormP->setTitulo(strtoupper_dlb(_("periodo de inicio o finalización de las actividades")));
            $oFormP->setPosiblesPeriodos($aOpcionesP);
            switch ($Qsactividad) {
                case 'ca':
                    $oFormP->setDesplPeriodosOpcion_sel('curso_ca');
                    break;
                case 'crt':
                    $oFormP->setDesplPeriodosOpcion_sel('curso_crt');
                    break;
                default:
                    $oFormP->setDesplPeriodosOpcion_sel('tot_any');
                    break;
            }
            if ($Qperiodo !== '') {
                $oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
            }
            if ($Qyear !== 0) {
                $oFormP->setDesplAnysOpcion_sel($Qyear);
            } else {
                $oFormP->setDesplAnysOpcion_sel((int)date('Y'));
            }
            $periodo_form_html = $oFormP->getHtml();
        }

        return [
            'tituloGros' => $tituloGros,
            'action' => $action,
            'hash_form_html' => $oHash->getCamposHtml(),
            'titulo' => $titulo,
            'nomUbi' => $nomUbi,
            'n' => $n,
            'nj' => $nj,
            'nm' => $nm,
            'a' => $a,
            'sssc' => $sssc,
            'nax' => $nax,
            'c' => $c,
            'opciones_centros' => $aOpcionesSerialized,
            'id_ubi_sel' => $Qid_ubi,
            'periodo_form_html' => $periodo_form_html,
            'locale_us' => ConfigGlobal::is_locale_us(),
            'mi_sfsv' => ConfigGlobal::mi_sfsv(),
        ];
    }
}
