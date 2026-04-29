<?php

namespace src\actividadestudios\application;

use frontend\shared\config\OrbixRuntime;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\PosiblesCa;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use frontend\shared\web\Periodo;
use function frontend\shared\helpers\is_true;

/**
 * Misma lógica que `frontend/.../ca_posibles.php`; respuesta serializable.
 * En modo `lista`, `pagina_link_spec` lo firma el front ({@see frontend\actividadestudios\controller\ca_posibles}).
 *
 * @param array<string, mixed> $post
 *
 * @return array<string, mixed>
 */
final class CaPosiblesData
{
    public static function execute(array $post): array
    {
        $oPosiblesCa = new PosiblesCa();

        $objPau = (string)($post['obj_pau'] ?? '');
        $QgrupoEstudios = (string)($post['grupo_estudios'] ?? '');
        $Qtexto = (string)($post['texto'] ?? '');
        $Qref = (string)($post['ref'] ?? '');
        $Qidca = (string)($post['idca'] ?? '');
        $QcaEstudios = (string)($post['ca_estudios'] ?? '');
        $QcaRepaso = (string)($post['ca_repaso'] ?? '');
        $QcaTodos = (string)($post['ca_todos'] ?? '');

        $aSel = isset($post['sel']) && is_array($post['sel']) ? $post['sel'] : [];

        $QidCtrAgd = 0;
        $QidCtrN = 0;
        $Qna = '';
        $inicioIso = '';
        $finIso = '';

        if (!empty($aSel)) {
            $partsNa = explode('#', $aSel[0]);
            $Qna = (string)($partsNa[1] ?? '');
            $QgrupoEstudios = 'todos';
            $oHoy = new \src\shared\domain\value_objects\DateTimeLocal();
            $inicioIso = $oHoy->format('Y-m-d');
            $iniM = $_SESSION['oConfig']->getMesIniStgr();
            $year = date('Y');
            if (date('m') < $iniM) {
                $finIso = date('Y-m-t', strtotime("$year-$iniM-01"));
            } else {
                $nextYear = (int)$year + 1;
                $finIso = date('Y-m-t', strtotime("$nextYear-$iniM-01"));
            }
            $QidCtrAgd = 0;
            $QidCtrN = 0;
        } else {
            $QidCtrAgd = (int)($post['id_ctr_agd'] ?? 0);
            $QidCtrN = (int)($post['id_ctr_n'] ?? 0);
            $Qna = (string)($post['na'] ?? '');
            $Qyear = (int)($post['year'] ?? 0);
            $Qperiodo = (string)($post['periodo'] ?? '');
            $Qempiezamin = (string)($post['empiezamin'] ?? '');
            $Qempiezamax = (string)($post['empiezamax'] ?? '');

            if (empty($QidCtrAgd) && empty($QidCtrN)) {
                throw new \InvalidArgumentException(_('debe seleccionar un centro o grupo de centros'));
            }
            if ($Qperiodo === '') {
                $Qperiodo = 'curso_ca';
            }
            $oPeriodo = Periodo::conCalendarioDesdeBackend();
            $oPeriodo->setDefaultAny('next');
            $oPeriodo->setAny($Qyear);
            $oPeriodo->setEmpiezaMin($Qempiezamin);
            $oPeriodo->setEmpiezaMax($Qempiezamax);
            $oPeriodo->setPeriodo($Qperiodo);
            $inicioIso = $oPeriodo->getF_ini_iso();
            $finIso = $oPeriodo->getF_fin_iso();
        }

        $miSfsv = OrbixRuntime::miSfsv();
        $aWhereActividad = [];
        $aOperadorActividad = [];
        $idCtr = '';
        $idTablaPersona = '';

        switch ($Qna) {
            case 'agd':
            case 'a':
                $idCtr = ($QidCtrAgd === 1) ? '' : (string)$QidCtrAgd;
                $idTablaPersona = 'a';
                if (is_true($QcaTodos)) {
                    $idTipoActiv = '^' . $miSfsv . '33';
                } else {
                    $idTipoActiv = '^' . $miSfsv . '33';
                    if (is_true($QcaEstudios)) {
                        $idTipoActiv = '^' . $miSfsv . '332';
                    }
                    if (is_true($QcaRepaso)) {
                        $idTipoActiv = '^1' . $miSfsv . '334';
                    }
                }
                $aWhereActividad['id_tipo_activ'] = $idTipoActiv;
                $aOperadorActividad['id_tipo_activ'] = '~';
                break;
            case 'n':
                $idCtr = ($QidCtrN === 1) ? '' : (string)$QidCtrN;
                $idTablaPersona = 'n';
                if (is_true($QcaTodos)) {
                    $idTipoActiv = '^' . $miSfsv . '12';
                } else {
                    $idTipoActiv = '^' . $miSfsv . '12';
                    if (is_true($QcaEstudios)) {
                        $idTipoActiv = '^' . $miSfsv . '122';
                    }
                    if (is_true($QcaRepaso)) {
                        $idTipoActiv = '^' . $miSfsv . '124';
                    }
                }
                $aWhereActividad['id_tipo_activ'] = $idTipoActiv;
                $aOperadorActividad['id_tipo_activ'] = '~';
                break;
            default:
                throw new \InvalidArgumentException(_('Parámetro na no válido'));
        }

        $aWhere = [];
        $aOperador = [];
        $alum = 0;
        if (!empty($aSel)) {
            $idNomLst = '';
            foreach ($aSel as $selBox) {
                $partsSel = explode('#', $selBox);
                $idNom = (int)($partsSel[0] ?? 0);
                if ($alum > 0) {
                    $idNomLst .= '|';
                }
                if (!empty($idNom)) {
                    $idNomLst .= '^' . $idNom . '$';
                }
                $alum++;
            }
            $aWhere['id_nom'] = $idNomLst;
            $aOperador['id_nom'] = '~';
            $aWhere['_ordre'] = 'apellido1,apellido2,nom';
            $Qtexto = 'image';
            $personaRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        } else {
            switch ($idTablaPersona) {
                case 'n':
                    $nivelesStgrConEstudios = NivelStgrId::getArrayNivelStgrOn();
                    $aWhere['nivel_stgr'] = $nivelesStgrConEstudios;
                    $aOperador['nivel_stgr'] = 'IN';
                    $personaRepository = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
                    break;
                case 'a':
                    $personaRepository = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
                    break;
                default:
                    throw new \RuntimeException('id_tabla_persona');
            }
            $aWhere['situacion'] = 'A';
            $aWhere['sacd'] = 'f';
            $aWhere['id_tabla'] = $idTablaPersona;
            $aWhere['_ordre'] = 'id_ctr,apellido1,apellido2,nom';
            if ($idCtr !== '') {
                $aWhere['id_ctr'] = $idCtr;
            }
        }

        $cPersonas = $personaRepository->getPersonas($aWhere, $aOperador);

        $aWhereActividad['f_ini'] = "'$inicioIso','$finIso'";
        $aOperadorActividad['f_ini'] = 'BETWEEN';

        if ($QgrupoEstudios !== 'todos') {
            $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
            $cDelegaciones = $repoDelegacion->getDelegaciones(['grupo_estudios' => $QgrupoEstudios]);
            if (is_array($cDelegaciones) && count($cDelegaciones) > 1) {
                $aOperadorActividad['dl_org'] = 'OR';
            }
            $miGrupo = '';
            foreach ($cDelegaciones as $oDelegacion) {
                $miGrupo .= $miGrupo === '' ? '' : ',';
                $miGrupo .= "'" . $oDelegacion->getDlVo()->value() . "'";
            }
            $aWhereActividad['dl_org'] = $miGrupo;
        }

        $aWhereActividad['status'] = StatusId::ACTUAL;
        $aWhereActividad['_ordre'] = 'nivel_stgr,f_ini';

        $actividadPubRepository = $GLOBALS['container']->get(ActividadPubRepositoryInterface::class);
        $cActividades1 = $actividadPubRepository->getActividades($aWhereActividad, $aOperadorActividad);
        $actividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $aWhereActividad['publicado'] = 'f';
        $cActividades2 = $actividadDlRepository->getActividades($aWhereActividad, $aOperadorActividad);
        $cActividades = $cActividades1 + $cActividades2;

        $msgTxt = '';
        $aDatosCa = [];
        $maxLenActiv = 1;
        $ncBienio = 0;
        $ncCuadrienio1 = 0;
        $ncCuadrienio2 = 0;
        $ncRepaso = 0;
        $ncCe = 0;
        $ncOtros = 0;

        if ($Qidca === '') {
            $i = 0;
            foreach ($cActividades as $oActividad) {
                $aAsignaturasCa = [];
                $i++;
                $idActiv = $oActividad->getId_activ();
                $idTipoActiv = $oActividad->getId_tipo_activ();
                $nomActiv = $oActividad->getNom_activ();
                $nivelStgr = $oActividad->getNivel_stgr();
                if ($alum > 1) {
                    $nomActiv = str_replace('ca n', '', $nomActiv);
                    $nomActiv = str_replace('bienio', '', $nomActiv);
                    $nomActiv = str_replace('cuadrienio', '', $nomActiv);
                    $nomActiv = str_replace('repaso', '', $nomActiv);
                    $nomActiv = str_replace('semestre', '', $nomActiv);
                    $nomActiv = trim($nomActiv);
                }
                if (empty($nivelStgr)) {
                    $msgTxt .= sprintf(_('el ca: %s no tiene puesto el nivel de stgr.') . '<br>', $nomActiv);
                    $nivelStgr = NivelStgrId::generarNivelStgr($idTipoActiv);
                }
                if ($nivelStgr === 4 || $nivelStgr === 9 || $nivelStgr === 8 || $nivelStgr === 7) {
                    $aAsignaturasCa = ['dd'];
                } else {
                    $actividadAsignaturaRepository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
                    $aAsignaturasCa = $actividadAsignaturaRepository->getAsignaturasCa($idActiv);
                    if (count($aAsignaturasCa) === 0 && $nivelStgr) {
                        $msgTxt .= sprintf(_('el ca: %s no tiene puesta ninguna asignatura.') . '<br>', $nomActiv);
                        continue;
                    }
                }
                switch ($nivelStgr) {
                    case 1:
                        $ncBienio++;
                        break;
                    case 2:
                        $ncCuadrienio1++;
                        break;
                    case 3:
                        $ncCuadrienio2++;
                        break;
                    case 4:
                        $ncRepaso++;
                        break;
                    case 5:
                        $ncCe++;
                        break;
                    default:
                        $ncOtros++;
                        break;
                }
                $aDatosCa[$idActiv] = [
                    'nom_activ' => $nomActiv,
                    'nivel_stgr' => $nivelStgr,
                    'aAsignaturas' => $aAsignaturasCa,
                ];
                $len = strlen($nomActiv);
                $maxLenActiv = ($maxLenActiv < $len) ? $len : $maxLenActiv;
            }
        }
        $ncCuadrienio = $ncCuadrienio1 + $ncCuadrienio2;

        $cuadro = [];
        if (!empty($aSel)) {
            $cOrdPersonas = [];
            $centroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            foreach ($cPersonas as $oPersonaDl) {
                $idUbi = $oPersonaDl->getId_ctr();
                $oCentroDl = $centroDlRepository->findById($idUbi);
                $Ctr = $oCentroDl->getNombre_ubi();
                $ctr = strtolower((string)$Ctr);
                $cOrdPersonas[$ctr][] = ['Ctr' => $Ctr, 'oPersonaDl' => $oPersonaDl];
            }
        } else {
            $cOrdPersonas = [];
            $idUbiOld = '';
            $centroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            foreach ($cPersonas as $oPersonaDl) {
                $idUbi = $oPersonaDl->getId_ctr();
                if ($idUbi != $idUbiOld) {
                    $oCentroDl = $centroDlRepository->findById($idUbi);
                    $Ctr = $oCentroDl->getNombre_ubi();
                    $ctr = strtolower((string)$Ctr);
                }
                $cOrdPersonas[$ctr][] = ['Ctr' => $Ctr, 'oPersonaDl' => $oPersonaDl];
            }
        }
        ksort($cOrdPersonas);

        $stgr = '';
        foreach ($cOrdPersonas as $ctr => $ctrPersonas) {
            foreach ($ctrPersonas as $row) {
                $Ctr = $row['Ctr'];
                $oPersonaDl = $row['oPersonaDl'];
                $idNom = $oPersonaDl->getId_nom();
                $idTablaPersonaRow = $oPersonaDl->getId_tabla();
                $nomPersona = $oPersonaDl->getPrefApellidosNombre();
                $stgr = $oPersonaDl->getNivel_stgr();
                $ce = method_exists($oPersonaDl, 'getCe') ? $oPersonaDl->getCe() : '';

                $aActividades = [];
                foreach ($aDatosCa as $idActiv => $datosCa) {
                    $aLista = [];
                    $nomActiv = $datosCa['nom_activ'];
                    $nivelStgr = $datosCa['nivel_stgr'];
                    $aAsignaturas = $datosCa['aAsignaturas'];

                    if ($ce && $Qna === 'agd') {
                        $stgr = 'ce';
                    }

                    switch ($stgr) {
                        case 'n':
                            if (in_array($nivelStgr, [9, 8, 7], true)) {
                                $creditos = 'x';
                            } else {
                                $creditos = '-';
                            }
                            break;
                        case 'b':
                            if ($nivelStgr == 1) {
                                $result = $oPosiblesCa->contar_creditos($idNom, $aAsignaturas);
                                $creditos = $result['suma'];
                                $aLista = $result['lista'];
                            } else {
                                $creditos = '-';
                            }
                            break;
                        case 'c1':
                            if ($nivelStgr == 2) {
                                $result = $oPosiblesCa->contar_creditos($idNom, $aAsignaturas);
                                $creditos = $result['suma'];
                                $aLista = $result['lista'];
                            } elseif ($nivelStgr == 3) {
                                $result = $oPosiblesCa->contar_creditos($idNom, $aAsignaturas);
                                $creditos = $result['suma'];
                                $aLista = $result['lista'];
                            } else {
                                $creditos = '-';
                            }
                            break;
                        case 'c2':
                            if ($nivelStgr == 3) {
                                $result = $oPosiblesCa->contar_creditos($idNom, $aAsignaturas);
                                $creditos = $result['suma'];
                                $aLista = $result['lista'];
                            } else {
                                $creditos = '-';
                            }
                            break;
                        case 'r':
                            if ($idTablaPersonaRow === 'n') {
                                if ($nivelStgr == 4) {
                                    $creditos = 'x';
                                } else {
                                    $creditos = '-';
                                }
                            } else {
                                if (in_array($nivelStgr, [4, 9, 8, 7], true)) {
                                    $creditos = 'x';
                                } else {
                                    $creditos = '-';
                                }
                            }
                            break;
                        case 'ce':
                            if ($nivelStgr == 5) {
                                $result = $oPosiblesCa->contar_creditos($idNom, $aAsignaturas);
                                $creditos = $result['suma'];
                                $aLista = $result['lista'];
                            } else {
                                $creditos = '-';
                            }
                            break;
                        default:
                            $creditos = '-';
                            $aLista = [];
                    }

                    $aActividades[$idActiv] = [
                        'nom_activ' => $nomActiv,
                        'creditos' => $creditos,
                        'aLista' => $aLista,
                    ];
                }

                $cuadro[$Ctr][$nomPersona] = [
                    'stgr' => $stgr,
                    'aActividades' => $aActividades,
                ];
            }
        }

        if (!empty($aSel) && $alum == 1) {
            $idNomPagina = (int)explode('#', $aSel[0])[0];
            $paginaLinkSpec = [
                'path' => 'frontend/dossiers/controller/dossiers_ver.php',
                'query' => [
                    'que' => 'activ',
                    'pau' => 'p',
                    'id_pau' => $idNomPagina,
                    'obj_pau' => $objPau,
                    'id_dossier' => '1301y1302',
                ],
            ];

            if (count($cuadro) > 1) {
                throw new \RuntimeException(_('sólo debebería haber uno'));
            }
            $titulo = '';
            $aActividadesLista = [];
            foreach ($cuadro as $ctr => $datosPersona) {
                $nom = (string)array_key_first($datosPersona);
                $datos = $datosPersona[$nom];
                $titulo = sprintf(_('posibles ca de %s (%s)'), $nom, $ctr);
                $stgr = $datos['stgr'];
                $aActividadesLista = $datos['aActividades'];
            }

            return [
                'modo' => 'lista',
                'msg_txt' => $msgTxt,
                'titulo' => $titulo,
                'stgr' => $stgr,
                'aActividades' => $aActividadesLista,
                'pagina_link_spec' => $paginaLinkSpec,
            ];
        }

        $tablaFilas = [];
        foreach ($cuadro as $ctr => $datosPersona) {
            $aActividadesLast = [];
            foreach ($datosPersona as $datos) {
                $aActividadesLast = $datos['aActividades'];
            }
            $tablaFilas[] = [
                'msg_txt' => $msgTxt,
                'texto' => $Qtexto,
                'nc_bienio' => $ncBienio,
                'nc_cuadrienio1' => $ncCuadrienio1,
                'nc_cuadrienio2' => $ncCuadrienio2,
                'nc_cuadrienio' => $ncCuadrienio,
                'nc_repaso' => $ncRepaso,
                'nc_ce' => $ncCe,
                'nc_otros' => $ncOtros,
                'stgr' => $stgr,
                'ctr' => $ctr,
                'ref' => $Qref,
                'height' => $maxLenActiv,
                'cPersonas' => $datosPersona,
                'aActividades' => $aActividadesLast,
            ];
        }

        return [
            'modo' => 'tabla',
            'tabla_filas' => $tablaFilas,
        ];
    }
}
