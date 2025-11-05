<?php

use core\ConfigGlobal;
use personas\model\entity\GestorTelecoPersonaDl;
use personas\model\entity\PersonaDl;
use profesores\model\entity\GestorProfesor;
use profesores\model\entity\GestorProfesorDocenciaStgr;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');

$GesProfesores = new GestorProfesor();
$cProfesores = $GesProfesores->getListaProfesoresAsignatura($Qid_asignatura);
/* $cProfesores es un array amb dos llistes:
	$Opciones['departamento']
	$Opciones['ampliacion']
 * 
 */

// De momento no se hace ninguna accion
//$a_botones=array( array( 'txt' => _("modificar"), 'click' =>"fnjs_modificar(this.form)" ) );
$a_botones = [];

$a_cabeceras = [];
$a_cabeceras[] = array('name' => ucfirst(_("apellidos, nombre")), 'formatter' => 'clickFormatter');
$a_cabeceras[] = ucfirst(_("centro"));
$a_cabeceras[] = ucfirst(_("docencia"));
$a_cabeceras[] = ucfirst(_("teléfono"));
$a_cabeceras[] = ucfirst(_("mail"));

$i = 0;
$a_valores = [];
foreach ($cProfesores['departamento'] as $id_nom => $ap_nom) {
    $i++;
    $oPersonaDl = new PersonaDl($id_nom);
    // Para el caso de ser region del stgr, poner la dl
    if (ConfigGlobal::mi_ambito() === 'rstgr') {
        $centro = $oPersonaDl->getDl() . " - " . $oPersonaDl->getCentro_o_dl();
    } else {
        $centro = $oPersonaDl->getCentro_o_dl();
    }
    // Actividad docente
    $oGesDocencia = new GestorProfesorDocenciaStgr();
    $aWhere = [];
    $aWhere['id_nom'] = $id_nom;
    $aWhere['id_asignatura'] = $Qid_asignatura;
    $aWhere['_ordre'] = 'curso_inicio DESC';
    $cDocencia = $oGesDocencia->getProfesorDocenciasStgr($aWhere);
    $txt_docencia = '';
    foreach ($cDocencia as $oProfesorDacendiaStgr) {
        $inicio_curso = $oProfesorDacendiaStgr->getCurso_inicio();
        $fin_curso = $inicio_curso + 1;
        $curso = "$inicio_curso-$fin_curso";
        $txt_docencia .= !empty($txt_docencia) ? '; ' : '';
        $txt_docencia .= $curso;
    }
    // Telecos
    $gesTelecoPersona = new GestorTelecoPersonaDl();
    $cTelecoPersona = $gesTelecoPersona->getTelecos(array('id_nom' => $id_nom));
    $telfs = '';
    $mails = '';
    foreach ($cTelecoPersona as $oTelecoPersona) {
        $tipo = $oTelecoPersona->getTipo_teleco();
        switch ($tipo) {
            case 'mail':
            case 'e-mail':
                $mails .= $oTelecoPersona->getNum_teleco();
                break;
            case 'móvil':
            case 'movil':
            case 'telf':
                $telfs .= $oTelecoPersona->getNum_teleco();
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit ($err_switch);
        }
    }

    $pagina = '';

    $a_valores[$i]['sel'] = "$id_nom";
    $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $ap_nom);
    $a_valores[$i][2] = $centro;
    $a_valores[$i][3] = $txt_docencia;
    $a_valores[$i][4] = $telfs;
    $a_valores[$i][5] = $mails;
}
// Para añadir los de apmpliación
foreach ($cProfesores['ampliacion'] as $id_nom => $ap_nom) {
    $i++;
    $oPersonaDl = new PersonaDl($id_nom);
    $centro = $oPersonaDl->getCentro_o_dl();
    // Actividad docente
    $oGesDocencia = new GestorProfesorDocenciaStgr();
    $aWhere = [];
    $aWhere['id_nom'] = $id_nom;
    $aWhere['id_asignatura'] = $Qid_asignatura;
    $aWhere['_ordre'] = 'curso_inicio DESC';
    $cDocencia = $oGesDocencia->getProfesorDocenciasStgr($aWhere);
    $txt_docencia = '';
    foreach ($cDocencia as $oProfesorDacendiaStgr) {
        $inicio_curso = $oProfesorDacendiaStgr->getCurso_inicio();
        $fin_curso = $inicio_curso + 1;
        $curso = "$inicio_curso-$fin_curso";
        $txt_docencia .= !empty($txt_docencia) ? '; ' : '';
        $txt_docencia .= $curso;
    }
    // Telecos
    $gesTelecoPersona = new GestorTelecoPersonaDl();
    $cTelecoPersona = $gesTelecoPersona->getTelecos(array('id_nom' => $id_nom));
    $telfs = '';
    $mails = '';
    foreach ($cTelecoPersona as $oTelecoPersona) {
        $tipo = $oTelecoPersona->getTipo_teleco();
        switch ($tipo) {
            case 'mail':
            case 'e-mail':
                $mails .= $oTelecoPersona->getNum_teleco();
                break;
            case 'móvil':
            case 'movil':
            case 'telf':
                $telfs .= $oTelecoPersona->getNum_teleco();
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit ($err_switch);
        }
    }

    $pagina = '';

    $a_valores[$i]['sel'] = "$id_nom";
    $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $ap_nom);
    $a_valores[$i][2] = $centro;
    $a_valores[$i][3] = $txt_docencia;
    $a_valores[$i][4] = $telfs;
    $a_valores[$i][5] = $mails;

}

$oTabla = new Lista();
$oTabla->setId_tabla('list_profe_asig');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);


echo $oTabla->mostrar_tabla();