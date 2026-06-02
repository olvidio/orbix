<?php

namespace src\asistentes\application;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Actividades pendientes por curso (`activ_pendientes_select.php`).
 * Datos y `link_spec` sin firmar; hash, firmas y tablas en {@see \frontend\asistentes\helpers\ActivPendientesSelectRender}.
 */
final class ActivPendientesSelectData
{
    public function __construct(
        private ActividadRepositoryInterface $actividadRepository,
        private AsistenteRepositoryInterface $asistenteRepository,
        private PersonaNRepositoryInterface $personaNRepository,
        private PersonaAgdRepositoryInterface $personaAgdRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function build(array $input): array
    {
        $Qany = input_int($input, 'any', 0);
        $Qtipo_personas = input_string($input, 'tipo_personas');
        $Qsactividad = input_string($input, 'sactividad');

        if (empty($Qany)) {
            $any = (int)date('Y');
        } else {
            $any = $Qany;
        }

        $chk_any_1 = '';
        $chk_any_2 = '';
        switch ($any) {
            case (int)date('Y'):
                $chk_any_1 = 'selected';
                break;
            case (int)date('Y') + 1:
                $chk_any_2 = 'selected';
                break;
        }
        $any_real = (int)date('Y');
        $txt_curso_1 = ($any_real - 1) . '/' . $any_real;
        $txt_curso_2 = $any_real . '/' . ($any_real + 1);
        $txt_curso = ($any - 1) . '/' . $any;

        $chk_n = '';
        $chk_agd = '';
        $chk_sacd = '';
        switch ($Qtipo_personas) {
            case 'n':
                $chk_n = 'selected';
                break;
            case 'agd':
                $chk_agd = 'selected';
                break;
            case 'sacd':
                $chk_sacd = 'selected';
                break;
        }

        $mi_dele = ConfigGlobal::mi_delef();
        $chk_ca = '';
        $chk_crt = '';
        $id_tipo_activ = '';
        $inicurs = '';
        $fincurs = '';
        switch ($Qsactividad) {
            case 'ca':
                if ($Qtipo_personas === 'n') {
                    $id_tipo_activ = '(112...)|(133...)';
                }
                if ($Qtipo_personas === 'sacd') {
                    $id_tipo_activ = '(112...)|(133...)';
                }
                if ($Qtipo_personas === 'agd') {
                    $id_tipo_activ = '133...';
                }
                if ($Qtipo_personas === 'stgr') {
                    $id_tipo_activ = '(112...)|(133...)';
                }
                $chk_ca = 'selected';
                $inicurs = \src\shared\domain\helpers\curso_est('inicio', $any, 'est')->format('Y-m-d');
                $fincurs = \src\shared\domain\helpers\curso_est('fin', $any, 'est')->format('Y-m-d');
                break;
            case 'crt':
                if ($Qtipo_personas === 'n') {
                    $id_tipo_activ = '1[1376]1...';
                }
                if ($Qtipo_personas === 'agd') {
                    $id_tipo_activ = '131...';
                }
                if ($Qtipo_personas === 'sacd') {
                    $id_tipo_activ = '1[136]1...';
                }
                $chk_crt = 'selected';
                $inicurs = \src\shared\domain\helpers\curso_est('inicio', $any, 'crt')->format('Y-m-d');
                $fincurs = \src\shared\domain\helpers\curso_est('fin', $any, 'crt')->format('Y-m-d');
                break;
        }

        if ($Qsactividad !== 'ca' && $Qsactividad !== 'crt') {
            $cActividades = [];
        } else {
            $aWhereA = [];
            $aOperadorA = [];
            $aWhereA['id_tipo_activ'] = $id_tipo_activ;
            $aOperadorA['id_tipo_activ'] = '~';
            $aWhereA['f_ini'] = "'$inicurs','$fincurs'";
            $aOperadorA['f_ini'] = 'BETWEEN';
            $cActividades = $this->actividadRepository->getActividades($aWhereA, $aOperadorA);
        }
        $aAsistentes = [];
        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $cAsistentes = $this->asistenteRepository->getAsistentes(['id_activ' => $id_activ, 'propio' => 't']);
            foreach ($cAsistentes as $oAsistente) {
                $aAsistentes[] = $oAsistente->getId_nom();
            }
        }

        switch ($Qtipo_personas) {
            case 'n':
                $cPersonas = $this->personaNRepository->getPersonas(['situacion' => 'A', 'dl' => $mi_dele]);
                $obj_pau = 'PersonaN';
                break;
            case 'agd':
                $cPersonas = $this->personaAgdRepository->getPersonas(['situacion' => 'A', 'dl' => $mi_dele]);
                $obj_pau = 'PersonaAgd';
                break;
            case 'sacd':
                $cPersonas = $this->personaDlRepository->getPersonas(['sacd' => 't', 'situacion' => 'A', 'dl' => $mi_dele]);
                $obj_pau = 'PersonaDl';
                break;
            default:
                $cPersonas = [];
                $obj_pau = 'PersonaN';
        }

        $aFaltan = [];
        foreach ($cPersonas as $oPersona) {
            $id_nomP = $oPersona->getId_nom();
            if (in_array($id_nomP, $aAsistentes)) {
                continue;
            }
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $id_ubi = $oPersona->getId_ctr();
            $nivel_stgr = $oPersona->getNivel_stgr();
            if (!empty($ap_nom)) {
                $aFaltan[$ap_nom] = ['id_nom' => $id_nomP, 'id_ubi' => $id_ubi, 'nivel_stgr' => $nivel_stgr];
            }
        }
        uksort($aFaltan, 'src\shared\domain\helpers\strsinacentocmp');

        $titulo = ucfirst(sprintf(_('lista de %s sin %s en el curso %s'), $Qtipo_personas, $Qsactividad, $txt_curso));

        $a_cabeceras = [_('nº'),
            ['name' => ucfirst(_('nombre de la persona')), 'formatter' => 'clickFormatter'],
            'ctr',
            _('nivel stgr'),
        ];
        $i = 0;
        $a_valores = [];
        $aNivelesStgr = NivelStgrId::getArrayNivelStgr();
        foreach ($aFaltan as $ap_nom => $aDatos) {
            $i++;
            $id_nom = $aDatos['id_nom'];
            $id_ubi = $aDatos['id_ubi'];
            $nivel_stgr = $aDatos['nivel_stgr'];

            $oCentroDl = $this->centroDlRepository->findById($id_ubi);
            $nombre_ubi = '?';
            if ($oCentroDl !== null) {
                $nombre_ubi = $oCentroDl->getNombre_ubi();
            }

            $aQuery = ['obj_pau' => $obj_pau, 'id_nom' => $id_nom];
            $a_valores[$i][1] = $i;
            $a_valores[$i][2] = [
                'link_spec' => [
                    'path' => 'frontend/personas/controller/home_persona.php',
                    'query' => $aQuery,
                ],
                'valor' => $ap_nom,
            ];
            $a_valores[$i][3] = $nombre_ubi;
            $a_valores[$i][4] = $aNivelesStgr[$nivel_stgr]?? '';
        }

        $aWhere = [];
        $aOperador = [];
        $aWhere['situacion'] = 'A';
        $aWhere['dl'] = $mi_dele;
        $aOperador['dl'] = '!=';
        switch ($Qtipo_personas) {
            case 'n':
                $cPersonasOtras = $this->personaNRepository->getPersonas($aWhere, $aOperador);
                break;
            case 'agd':
                $cPersonasOtras = $this->personaAgdRepository->getPersonas($aWhere, $aOperador);
                break;
            case 'sacd':
                $aWhere['sacd'] = 't';
                $cPersonasOtras = $this->personaDlRepository->getPersonas($aWhere, $aOperador);
                break;
            default:
                $cPersonasOtras = [];
        }

        $aFaltanOtras = [];
        foreach ($cPersonasOtras as $oPersona) {
            $id_nomP = $oPersona->getId_nom();
            if (in_array($id_nomP, $aAsistentes)) {
                continue;
            }
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $id_ubi = $oPersona->getId_ctr();
            $nivel_stgr = $oPersona->getNivel_stgr();
            $aFaltanOtras[$ap_nom] = ['id_nom' => $id_nomP, 'id_ubi' => $id_ubi, 'nivel_stgr' => $nivel_stgr];
        }
        uksort($aFaltanOtras, 'src\shared\domain\helpers\strsinacentocmp');

        $a_valores_2 = [];
        foreach ($aFaltanOtras as $ap_nom => $aDatos) {
            $i++;
            $id_nom = $aDatos['id_nom'];
            $id_ubi = $aDatos['id_ubi'];
            $nivel_stgr = $aDatos['nivel_stgr'];

            $oCentroDl = $this->centroDlRepository->findById($id_ubi);
            $nombre_ubi = $oCentroDl !== null ? $oCentroDl->getNombre_ubi() : '?';

            $aQuery = ['obj_pau' => $obj_pau, 'id_nom' => $id_nom];
            $a_valores_2[$i][1] = $i;
            $a_valores_2[$i][2] = [
                'link_spec' => [
                    'path' => 'frontend/personas/controller/home_persona.php',
                    'query' => $aQuery,
                ],
                'valor' => $ap_nom,
            ];
            $a_valores_2[$i][3] = $nombre_ubi;
            $a_valores_2[$i][4] = $nivel_stgr;
        }

        return [
            'paths' => [
                'form_action' => 'frontend/asistentes/controller/activ_pendientes_select.php',
            ],
            'hash_main' => [
                'campos_form' => 'tipo_personas!sactividad!any',
            ],
            'chk_n' => $chk_n,
            'chk_agd' => $chk_agd,
            'chk_sacd' => $chk_sacd,
            'chk_ca' => $chk_ca,
            'chk_crt' => $chk_crt,
            'any_real' => $any_real,
            'chk_any_1' => $chk_any_1,
            'txt_curso_1' => $txt_curso_1,
            'chk_any_2' => $chk_any_2,
            'txt_curso_2' => $txt_curso_2,
            'titulo' => $titulo,
            'a_cabeceras_activ_pendientes' => $a_cabeceras,
            'a_valores_activ_pendientes_dl' => $a_valores,
            'a_valores_activ_pendientes_otras' => $a_valores_2,
        ];
    }
}
