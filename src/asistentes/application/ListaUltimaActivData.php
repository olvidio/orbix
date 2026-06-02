<?php

namespace src\asistentes\application;

use DateInterval;
use frontend\shared\web\Lista;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Listado última actividad / seguimiento (`lista_ultima_activ.php`).
 */
final class ListaUltimaActivData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private PersonaSRepositoryInterface $personaSRepository,
        private AsistenteActividadService $asistenteActividadService,
        private ActividadAllRepositoryInterface $actividadAllRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{alert_html: string, titulo: string, stats_html: string, tabla_html: string}
     */
    public function build(array $input): array
    {
        $Qque = (string)($input['que'] ?? '');
        $Qcurso = (string)($input['curso'] ?? '');
        $Qid_ubi = (string)($input['id_ubi'] ?? '');

        $any = (int)date('Y');

        $aWhereP = [];
        $aOperadorP = [];
        if (!empty($Qid_ubi) && ($Qid_ubi != '999')) {
            $oCentroDl = $this->centroDlRepository->findById((int)$Qid_ubi);
            $nombre_ubi = $oCentroDl->getNombre_ubi();
            $aWhereP['id_ctr'] = $Qid_ubi;
            $aWhereP['_ordre'] = 'apellido1, apellido2, nom';
        } else {
            $nombre_ubi = '';
        }

        $aWhereA = [];
        $aOperadorA = [];

        if (strstr($Qque, 'crt')) {
            $mes_ini = $_SESSION['oConfig']->getMesIniCrt();
            $dia_ini = $_SESSION['oConfig']->getDiaIniCrt();
            $mes_fin = $_SESSION['oConfig']->getMesFinCrt();
            $dia_fin = $_SESSION['oConfig']->getDiaFinCrt();
            $any = $_SESSION['oConfig']->any_final_curs('crt');
        } else {
            $mes_ini = $_SESSION['oConfig']->getMesIniStgr();
            $dia_ini = $_SESSION['oConfig']->getDiaIniStgr();
            $mes_fin = $_SESSION['oConfig']->getMesFinStgr();
            $dia_fin = $_SESSION['oConfig']->getDiaFinStgr();
            $any = $_SESSION['oConfig']->any_final_curs('est');
        }
        switch ($Qcurso) {
            case 'anterior':
                $any_ini = $any - 2;
                $any_fin = $any - 1;
                break;
            case 'actual':
                $any_ini = $any - 1;
                $any_fin = $any;
                break;
            default:
                if ($Qque === 'crt_cel' || $Qque === 'crt_s') {
                    $any_ini = $any - 4;
                    $any_fin = $any - 1;
                } else {
                    $any_ini = $any - 1;
                    $any_fin = $any;
                }
        }
        $oDateIni = new DateTimeLocal("$any_ini/$mes_ini/$dia_ini");
        $oDateFin = new DateTimeLocal("$any_fin/$mes_fin/$dia_fin");

        $QempiezaminIso = $oDateIni->getIso();
        $QfinIso = $oDateFin->getIso();
        $fecha_ini = $oDateIni->getFromLocal('-');
        $fecha_fin = $oDateFin->getFromLocal('-');
        $titulo_fecha = sprintf(_('entre %s y %s'), $fecha_ini, $fecha_fin);

        $alert = '';
        switch ($Qque) {
            case 'crt_s_sg':
                $aWhereA['id_tipo_activ'] = '^1[45]1';
                $aOperadorA['id_tipo_activ'] = '~';
                if (empty($nombre_ubi)) {
                    $titulo_actividad = _('s que todavía no han asistido a crt-s o crt-sg');
                } else {
                    $titulo_actividad = sprintf(_('s de %s que todavía no han asistido a crt-s o crt-sg'), $nombre_ubi);
                }
                break;
            case 'crt_s':
                $aWhereA['id_tipo_activ'] = '^141';
                $aOperadorA['id_tipo_activ'] = '~';
                $titulo_actividad = sprintf(_('s no celadores que NO han asistido al crt interno'));
                $alert = '*' . sprintf(_("para indicar si es celador, el campo Eap debe contener algo tipo: 'C12'"));
                $aWhereP['eap'] = "COALESCE(eap,'x') !~* 'C\d\d'";
                $aOperadorP['eap'] = 'TXT';
                break;
            case 'crt_cel':
                $aWhereA['id_tipo_activ'] = '^141';
                $aOperadorA['id_tipo_activ'] = '~';
                $titulo_actividad = sprintf(_('s celadores que NO han asistido a un crt interno'));
                $alert = '*' . sprintf(_("para indicar si es celador, el campo Eap debe contener algo tipo: 'C12'"));
                $aWhereP['eap'] = "COALESCE(eap,'x') ~* 'C\d\d'";
                $aOperadorP['eap'] = 'TXT';
                break;
            case 'cv_s':
                $aWhereA['id_tipo_activ'] = '^143';
                $aOperadorA['id_tipo_activ'] = '~';
                if (empty($nombre_ubi)) {
                    $titulo_actividad = _('s que no han asistido a cv de s');
                } else {
                    $titulo_actividad = sprintf(_('s de %s que todavía no han asistido a cv de s'), $nombre_ubi);
                }
                break;
            case 'cv_s_ad':
                $aWhereA['id_tipo_activ'] = '1431';
                $titulo_actividad = sprintf(_('s con ad reciente -entre 6 y 18 meses antes de la fecha última cv admisión del año- que todavía no han asistido a cv de ad'));
                $anyCv = (int)date('Y');
                $fin_d = $_SESSION['oConfig']->getDiaFinCrt();
                $fin_m = $_SESSION['oConfig']->getMesFinCrt();
                $f_iso_final = "$anyCv-$fin_m-$fin_d";

                $aWhereUltima = ['id_tipo_activ' => '1431',
                    'status' => 2,
                    'f_ini' => $f_iso_final,
                    '_ordre' => 'f_ini DESC',
                ];
                $aOperadorUltima = ['f_ini' => '<'];
                $cActividades = $this->actividadDlRepository->getActividades($aWhereUltima, $aOperadorUltima);
                if (is_array($cActividades) && !empty($cActividades)) {
                    $oActividadU = $cActividades[0];
                    $oFini = $oActividadU->getF_fin();
                    $nom_activ = $oActividadU->getNom_activ();
                } else {
                    $oFini = new DateTimeLocal();
                    $nom_activ = _('No hay');
                }
                $iso_fin = $oFini->sub(new DateInterval('P6M'))->getIso();
                $iso_ini = $oFini->sub(new DateInterval('P12M'))->getIso();
                $aWhereP['inc'] = 'ad';
                $aWhereP['f_inc'] = "'$iso_ini','$iso_fin'";
                $aOperadorP['f_inc'] = 'BETWEEN';
                $alert = '*' . sprintf(_('última cv: %s'), $nom_activ);
                break;
            case 'cv_jovenes':
                $titulo_actividad = _('s jóvenes (<30) que no han asistido a cv de s');
                $f_joven = date('Y-m-d', mktime(0, 0, 0, 1, 1, $any - 30));
                $aWhereP['f_nacimiento'] = $f_joven;
                $aOperadorP['f_nacimiento'] = '>';
                $aWhereP['ce_ini'] = 'x';
                $aOperadorP['ce_ini'] = 'IS NULL';
                $aWhereP['ce_fin'] = 'x';
                $aOperadorP['ce_fin'] = 'IS NULL';
                break;
            default:
                exit(_('No sé en que tipo de actividad hay que mirar las asistencias'));
        }

        $aWhereA['_ordre'] = 'f_ini DESC';
        $a_id_activ_f_ini = $this->actividadRepository->getArrayIdsWithKeyFini($aWhereA, $aOperadorA);

        $titulo = $titulo_actividad . ' ' . $titulo_fecha;

        $aWhereP['situacion'] = 'A';
        $cPersonas = $this->personaSRepository->getPersonas($aWhereP, $aOperadorP);
        $i = 0;
        $falta = 0;
        $a_valores = [];
        foreach ($cPersonas as $oPersona) {
            $i++;
            $id_nom = $oPersona->getId_nom();
            $ape_nom = $oPersona->getPrefApellidosNombre();

            if ($Qid_ubi == '999' || empty($Qid_ubi)) {
                $nombre_ubi = '';
                $id_ctr = $oPersona->getId_ctr();
                $cCentros = $this->centroDlRepository->getCentros(['id_ubi' => $id_ctr]);
                if (is_array($cCentros) && !empty($cCentros)) {
                    $nombre_ubi = $cCentros[0]->getNombre_ubi();
                }
            }

            $cAsistentes = $this->asistenteActividadService->getAsistenciasPersonaDeActividades($id_nom, $a_id_activ_f_ini, true);
            if (!empty($cAsistentes)) {
                reset($cAsistentes);
                $oAsistente = current($cAsistentes);
                $id_activ = $oAsistente->getId_activ();
                $oActividad = $this->actividadAllRepository->findById($id_activ);
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $oFini = $oActividad->getF_ini();
                $f_ini_iso = $oFini->getIso();

                if ($f_ini_iso >= $QempiezaminIso && $f_ini_iso <= $QfinIso) {
                    continue;
                }
                $f_ini = $oFini->getFromLocal();

                $oTipoActividad = new TiposActividades($id_tipo_activ);
                $sactividad = $oTipoActividad->getActividadText();
                $sasistentes = $oTipoActividad->getAsistentesText();
                $snom_tipo = $oTipoActividad->getNom_tipoText();
                $msg = "$sactividad  $sasistentes  $snom_tipo";
            } else {
                $msg = _('No hay datos');
                $f_ini = _('No consta');
                $a_valores[$i]['clase'] = 'tono2';
            }
            $falta++;

            $a_valores[$i][1] = $ape_nom;
            $a_valores[$i][2] = $nombre_ubi;
            $a_valores[$i][3] = $f_ini;
            $a_valores[$i][4] = $msg;
        }

        $a_cabeceras = [ucfirst(_('apellidos, nombre')),
            ucfirst(_('centro')),
            ucfirst(_('fecha última asistencia')),
            ucfirst(_('tipo actividad')),
        ];

        $oTabla = new Lista();
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setDatos($a_valores);

        $stats_html = sprintf(_('número de personas: %s de %s'), $falta, $i);

        return [
            'alert_html' => $alert,
            'titulo' => $titulo,
            'stats_html' => $stats_html,
            'tabla_html' => $oTabla->lista(),
        ];
    }
}
