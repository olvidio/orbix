<?php

namespace src\asistentes\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Formulario de elección de centro / periodo (`que_ctr_lista.php`).
 * Hash, bloque PeriodoQue y URL absoluta del action en
 * {@see \frontend\asistentes\helpers\QueCtrListaRender}.
 */
final class QueCtrListaData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function build(array $input): array
    {
        $tipo = FuncTablasSupport::inputString($input, 'tipo');
        $ssfsv = FuncTablasSupport::inputString($input, 'ssfsv');
        $Qlista = FuncTablasSupport::inputString($input, 'lista');
        $Qsasistentes = FuncTablasSupport::inputString($input, 'sasistentes');
        $Qsactividad = FuncTablasSupport::inputString($input, 'sactividad');
        $Qn_agd = FuncTablasSupport::inputString($input, 'n_agd');
        $Qid_ubi = FuncTablasSupport::inputInt($input, 'id_ubi', 0);
        $Qyear = FuncTablasSupport::inputInt($input, 'year', 0);
        $Qperiodo = FuncTablasSupport::inputString($input, 'periodo');

        $tituloGros = '';
        $titulo = '';
        $action = '';
        $a_camposHidden = [];
        $nomUbi = '';

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
                $nomUbi = ucfirst(_("nombre del centro"));
                $action = 'frontend/asistentes/controller/lista_est_ctr.php';
                $a_camposHidden = [
                    'tipo' => $tipo,
                    'ssfsv' => $ssfsv,
                    'sasistentes' => $Qsasistentes,
                    'sactividad' => $Qsactividad,
                ];
                break;
            default:
                break;
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
            case 'agd':
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

        $oGesCentros = $this->centroDlRepository;
        $aOpciones = $oGesCentros->getArrayCentros("WHERE active = 't' AND tipo_ctr ~ '^a|^n' ");
        $aOpcionesSerialized = $aOpciones;

        $periodo_form = null;
        if ($Qlista === 'list_activ' || $Qlista === 'list_est') {
            $aOpcionesP = [
                'curso_ca' => _("curso ca"),
                'curso_crt' => _("curso crt"),
                'tot_any' => _("todo el año"),
                'separador' => '---------',
                'otro' => _("otro"),
            ];
            $periodo_sel = 'tot_any';
            switch ($Qsactividad) {
                case 'ca':
                    $periodo_sel = 'curso_ca';
                    break;
                case 'crt':
                    $periodo_sel = 'curso_crt';
                    break;
                default:
                    break;
            }
            if ($Qperiodo !== '') {
                $periodo_sel = $Qperiodo;
            }
            $year_sel = $Qyear !== 0 ? $Qyear : (int)date('Y');

            $periodo_form = [
                'opciones_periodos' => $aOpcionesP,
                'titulo' => FuncTablasSupport::strtoupperDlb(_("periodo de inicio o finalización de las actividades")),
                'form_name' => 'modifica',
                'periodo_sel' => $periodo_sel,
                'year_sel' => $year_sel,
            ];
        }

        return [
            'tituloGros' => $tituloGros,
            'action' => $action,
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
            'locale_us' => ConfigGlobal::is_locale_us(),
            'mi_sfsv' => ConfigGlobal::mi_sfsv(),
            'hash_main' => [
                'campos_form' => 'n_agd!empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val',
                'campos_no' => 'id_ubi',
                'campos_hidden' => $a_camposHidden,
            ],
            'periodo_form' => $periodo_form,
        ];
    }
}
