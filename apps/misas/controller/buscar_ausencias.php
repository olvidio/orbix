<?php

// INICIO Cabecera global de URL de controlador *********************************

use web\Hash;
use function core\is_true;
use actividades\model\entity\ActividadAll;
use actividadcargos\model\entity\GestorActividadCargo;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use actividades\model\entity\GestorActividad;
use zonassacd\model\entity\Zona;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;
use personas\model\entity\PersonaSacd;
use web\Desplegable;
use web\TiposActividades;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_zona = 3;

$a_iniciales = [];

$inicio_iso =  "2024/01/01";
$fin_iso = "2024/01/31";

$oIniPlanning = web\DateTimeLocal::createFromFormat('Y/m/d', $inicio_iso);
$oFinPlanning = web\DateTimeLocal::createFromFormat('Y/m/d', $fin_iso);
$inicio_local = $oIniPlanning->getFromLocal();
//$fin_local = $oFinPlanning->getFromLocal();

$oZona = new Zona($Qid_zona);
$nombre_zona = $oZona->getNombre_zona();
$aa_zonas[0] = array('id_zona' => $Qid_zona, 'nombre_zona' => $nombre_zona);

$prova='Prova:';

$z = 0;
$GesZonasSacd = new GestorZonaSacd();
$aWhereZ = [];
$actividades_por_zona = [];
$cabeceras_por_zona = [];
foreach ($aa_zonas as $a_zonas) {
    $z++;
    $id_zona = $Qid_zona;
    $nombre_zona = "Nombre Zona";
    $aWhereZ['id_zona'] = $id_zona;
    $cZonasSacd = $GesZonasSacd->getZonasSacds($aWhereZ);
    $p = 0;
    $actividades = [];
    $persona = [];
    foreach ($cZonasSacd as $oZonaSacd) {
        $aActivPersona = array(); //inicializo el vector para la siguiente persona
        $id_nom = $oZonaSacd->getId_nom();

        $oSacd = new PersonaSacd($id_nom);
        if ($oSacd->getSituacion() != 'A') continue;

        $ap_nom = $oSacd->getPrefApellidosNombre();
        $persona[$p] = "p#$id_nom#$ap_nom";
$prova.=$ap_nom.'<br>';
//        if ($Qactividad == 'si') {
            $a = 0;
            $aWhereAct = [];
            $aOperadorAct = [];
            $aWhereAct['f_ini'] = "'$fin_iso'";
            $aOperadorAct['f_ini'] = '<=';
            $aWhereAct['f_fin'] = "'$inicio_iso'";
            $aOperadorAct['f_fin'] = '>=';
//            if (!is_true($Qpropuesta)) {
                $aWhereAct['status'] = ActividadAll::STATUS_ACTUAL;
//            } else {
//                $aWhereAct['status'] = ActividadAll::STATUS_BORRABLE;
//                $aOperadorAct['status'] = '!=';
//            }
            /*			
			$aWhere = ['id_nom' => $id_nom, 'plaza' => Asistente::PLAZA_PEDIDA];
			$aOperador = ['plaza' => '>='];
			*/
            $aWhere = ['id_nom' => $id_nom];
            $aOperador = [];

            $oGesActividadCargo = new GestorActividadCargo();
            $cAsistentes = $oGesActividadCargo->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);

            foreach ($cAsistentes as $aAsistente) {
                $id_activ = $aAsistente['id_activ'];
$prova.=' A: '.$id_activ;
                $propio = $aAsistente['propio'];
                $plaza = $aAsistente['plaza'];
                $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];

                // Seleccionar sólo las del periodo
                $aWhereAct['id_activ'] = $id_activ;
                $GesActividades = new GestorActividad();
                $cActividades = $GesActividades->getActividades($aWhereAct, $aOperadorAct);
                if (is_array($cActividades) && count($cActividades) == 0) continue;

                $oActividad = $cActividades[0]; // sólo debería haber una.
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $oF_ini = $oActividad->getF_ini();
                $oF_fin = $oActividad->getF_fin();
                $h_ini = $oActividad->getH_ini();
                $h_fin = $oActividad->getH_fin();
                $dl_org = $oActividad->getDl_org();
                $nom_activ = $oActividad->getNom_activ();

                $oTipoActividad = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActividad->getSfsvText();

                //para el caso de que la actividad comience antes
                //del periodo de inicio obligo a que tome una hora de inicio
                //en el entorno de las primeras del día (a efectos del planning
                //ya es suficiente con la 1:16 de la madrugada)
                if ($oIniPlanning > $oF_ini) {
                    $ini = $inicio_local;
                    $hini = "1:16";
                } else {
                    $ini = (string)$oF_ini->getFromLocal();
                    $hini = (string)$h_ini;
                }
                $fi = (string)$oF_fin->getFromLocal();
                $hfi = (string)$h_fin;


//                if (is_true($Qpropuesta)) {
                    $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                    $nom_llarg = $nom_activ;
$prova.=$nom_llarg;
                    /*                } else {
                    if (core\ConfigGlobal::is_app_installed('procesos')) {
                        // Si tiene cargo sacd (se supone que planing_zonas sólo es para los sacd), que la fase 'ok_sacd' esté completada
                        // Si es asistente, que la fase ok_asistente esté completada.
                        $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                        $permiso_ver = $_SESSION['oPermActividades']->havePermisoSacd($id_cargo, $propio);
                    } else {
                        $permiso_ver = TRUE;
                    }

                    if ($permiso_ver === FALSE) {
                        continue;
                    }

                    // mirar permisos en la actividad.
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                    if ($oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
                        $nom_curt = $ssfsv;
                        $nom_llarg = "$ssfsv ($ini-$fi)";
                    } else {
                        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                        $nom_llarg = $nom_activ;
                    }
                }
*/
                $aActivPersona[] = array(
                    'nom_curt' => $nom_curt,
                    'nom_llarg' => $nom_llarg,
                    'f_ini' => $ini,
                    'h_ini' => $hini,
                    'f_fi' => $fi,
                    'h_fi' => $hfi,
                    'id_tipo_activ' => $id_tipo_activ,
                    'pagina' => '',
                    'id_activ' => $id_activ,
                    'propio' => $propio,
                    'plaza' => $plaza,
                );
                $a++;
            }
            // ++++++++++++++ Añado las ausencias +++++++++++++++
            $aWhereE = [];
            $aOperadorE = [];
            $aWhereE['id_nom'] = $id_nom;
            $aWhereE['f_ini'] = "'$fin_iso'";
            $aOperadorE['f_ini'] = '<=';
            $aWhereE['f_fin'] = "'$inicio_iso'";
            $aOperadorE['f_fin'] = '>=';
            $GesAusencias = new GestorEncargoSacdHorario();
            $cAusencias = $GesAusencias->getEncargoSacdHorarios($aWhereE, $aOperadorE);
            foreach ($cAusencias as $oTareaHorarioSacd) {
                $id_enc = $oTareaHorarioSacd->getId_enc();
                $oF_ini = $oTareaHorarioSacd->getF_ini();
                $oF_fin = $oTareaHorarioSacd->getF_fin();
                $h_ini = '';
                $h_fi = '';

                $oEncargo = new Encargo($id_enc);
                $id_tipo_enc = $oEncargo->getId_tipo_enc();
                $id = (string)$id_tipo_enc;
                if ($id[0] != 7 && $id[0] != 4) continue;

                //para el caso de que la actividad comience antes
                //del periodo de inicio obligo a que tome una hora de inicio
                if ($oIniPlanning > $oF_ini) {
                    $ini = $inicio_local;
                    $hini = '5:00';
                } else {
                    $ini = (string)$oF_ini->getFromLocal();
                    $hini = empty($h_ini) ? '5:00' : (string)$h_ini;
                }
                $fi = (string)$oF_fin->getFromLocal();
                $hfi = empty($h_fin) ? '22:00' : (string)$h_fin;

                $propio = "p";
                $nom_llarg = $oEncargo->getDesc_enc();
                $nom_curt = ($nom_llarg[0] == 'A') ? 'a' : 'x';
                if ($ini != $fi) {
                    $nom_llarg .= " ($ini-$fi)";
                } else {
                    $nom_llarg .= " ($ini)";
                }

                $aActivPersona[] = array(
                    'nom_curt' => $nom_curt,
                    'nom_llarg' => $nom_llarg,
                    'f_ini' => $ini,
                    'h_ini' => $hini,
                    'f_fi' => $fi,
                    'h_fi' => $hfi,
                    'id_tipo_activ' => null,
                    'pagina' => '',
                    'id_activ' => $id_enc,
                    'propio' => $propio
                );
                $a++;

            }
            // ++++++++++++++++++++++++++++++++++++++++++++++++++
            // pongo el apellido como índice para poder ordenar por apellido.
            $actividades[$ap_nom] = array($persona[$p] => $aActivPersona);
            $p++;
//        } else {
//            $a_nom = array();
            // pongo el apellido como índice para poder ordenar por apellido.
//            $actividades[$ap_nom] = array($persona[$p] => $a_nom);
//            $p++;
//        }
    }
    // oredenar las personas
    uksort($actividades, "strnatcasecmp"); // case insensitive
    /*
    lo que sigue es para que nos represente una linea en blanco al final:
    esto permite visualizar correctamente las 3 divisiones en los días
    en que todas las casas están ocupadas.
    */
    $actividades[] = array('###' => array());

    $actividades_por_zona[$z] = $actividades;
    $cabeceras_por_zona[$z] = $nombre_zona;
}


    $gesZonaSacd = new GestorZonaSacd();
    $a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);
    
    foreach ($a_Id_nom as $id_nom) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $sacd = $PersonaSacd->getNombreApellidos();
        // iniciales
        $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
        $iniciales = strtoupper($nom . $ap1 . $ap2);
    
        $a_iniciales[$id_nom] = $iniciales;
    
        $key = $id_nom . '#' . $iniciales;
    
        $a_sacd[$key] = $sacd ?? '?';
    }



$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(TRUE);

$oHash = new Hash();
$oHash->setCamposForm('key');
$array_h = $oHash->getParamAjaxEnArray();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplSacd' => $oDesplSacd,
    'array_h' => $array_h,
    'prova' => $prova,
];

$oView = new core\ViewTwig('misas/controller');
//echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);
echo $oView->render('desplegable_sacd.html.twig', $a_campos);
