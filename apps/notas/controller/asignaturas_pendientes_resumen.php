<?php

use asignaturas\model\entity as asignaturas;
use function core\is_true;
use notas\model\entity as notas;
use personas\model\entity as personas;

/**
 * Esta página sirve para generar un cuadro con el numero de alumnos que tienen
 *  pendiente cada asignatura.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        13/1/17.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


/* Contadores
	
	id_asignatura => nombre, creditos, año
 * pendientes
 * 		id_asignatura
			nBienio
			nc1
			nc2
			ntotal
			agdBienio
 * 			agdc1
 * 			agdc2
 * 			agdtotal	
			
*/
// Asignaturas posibles:
$GesAsignaturas = new asignaturas\GestorAsignatura();
$aWhere = array();
$aOperador = array();
$aWhere['status'] = 't';
$aWhere['id_nivel'] = '1100,2500';
$aOperador['id_nivel'] = 'BETWEEN';
$aWhere['_ordre'] = 'id_nivel';
$cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere, $aOperador);


foreach ($cAsignaturas as $oAsignatura) {
    $id_nivel = $oAsignatura->getId_nivel();
    $id_asignatura = $oAsignatura->getId_asignatura();
    $nombre_corto = $oAsignatura->getNombre_corto();
    $creditos = $oAsignatura->getCreditos();
    $year = $oAsignatura->getYear();
    $aPendientes[$id_nivel] = array(
        'def' => array('nombre' => $nombre_corto,
            'creditos' => $creditos,
            'year' => $year),
        'nb' => 0,
        'nc1' => 0,
        'nc2' => 0,
        'ntotal' => 0,
        'ab' => 0,
        'ac1' => 0,
        'ac2' => 0,
        'atotal' => 0
    );
}


$a_cabeceras = array();
$a_cabeceras[0] = _("n/a");
$a_cabeceras[1] = _("stgr");
$a_cabeceras[2] = _("centro");
$a_cabeceras[3] = _("apellidos, nombre");
$a = 3;
foreach ($cAsignaturas as $oAsignatura) {
    $a++;
    $a_cabeceras[$a] = $oAsignatura->getNombre_corto();
}
//todas
$cAsignaturasTodas = $GesAsignaturas->getAsignaturas(array('_ordre' => 'id_asignatura'));
foreach ($cAsignaturasTodas as $oAsignatura) {
    $id_asignatura = $oAsignatura->getId_asignatura();
    $a_Asig_status[$id_asignatura] = $oAsignatura->getStatus();
    $a_Asig_nivel[$id_asignatura] = $oAsignatura->getId_nivel();
}
//print_r($a_Asig_nivel);

$aWhere = array();
$aOperador = array();
$aWhere['situacion'] = 'A';
$aWhere['stgr'] = 'b|c1|c2';
$aOperador['stgr'] = '~';
// Sólo n y agd
$aWhere['id_tabla'] = '^[na]';
$aOperador['id_tabla'] = '~';


$GesPersonas = new personas\GestorPersonaDl();
$cPersonas = $GesPersonas->getPersonasDl($aWhere, $aOperador);
$p = 0;
$GesNotas = new notas\GestorPersonaNotaDl();
foreach ($cPersonas as $oPersona) {
    $p++;
    $id_nom = $oPersona->getId_nom();
    $id_tabla = $oPersona->getId_tabla();
    //$ap_nom = $oPersona->getPrefApellidosNombre();
    $stgr = $oPersona->getStgr();

    $tipo = $id_tabla . $stgr;

    // Asignaturas cursadas:
    /*
    $aWhere=array();
    $aOperador=array();
    $aWhere['id_nom'] = $id_nom;
    $aWhere['id_nivel'] = '1100,2500';
    $aOperador['id_nivel']='BETWEEN';
    */
    $cNotas = $GesNotas->getPersonaNotasSuperadas($id_nom, 't');
    $aAprobadas = array();
    foreach ($cNotas as $oPersonaNota) {
        $id_asignatura = $oPersonaNota->getId_asignatura();
        $id_nivel = $oPersonaNota->getId_nivel();
        $id_situacion = $oPersonaNota->getId_situacion();

        //$oAsig = new asignaturas\Asignatura($id_asignatura);
        //if ($oAsig->getStatus() != 't') continue;
        if ($a_Asig_status[$id_asignatura] != 't') continue;


        if ($id_asignatura > 3000) {
            $id_nivel_asig = $id_nivel;
        } else {
            $id_nivel_asig = $a_Asig_nivel[$id_asignatura];
        }
        $n = $id_nivel_asig;
        $oNota = new notas\Nota($id_situacion);
        $aAprobadas[$n]['nota'] = is_true($oNota->getSuperada()) ? '' : 2;
    }


    foreach ($cAsignaturas as $oAsignatura) {
        $id_nivel = $oAsignatura->getId_nivel();
        if (empty($aAprobadas[$id_nivel])) {
            $aPendientes[$id_nivel][$tipo]++;
        }
    }
}
//---------------------- html ------------------------------------
?>

<?php
$html = '<table>';
$html .= '<tr><th>asignatura</th>';
$html .= '<th>nb</th>';
$html .= '<th>nc1</th>';
$html .= '<th>nc2</th>';
$html .= '<th>ntotal</th>';
$html .= '<th>ab</th>';
$html .= '<th>ac1</th>';
$html .= '<th>ac2</th>';
$html .= '<th>atotal</th>';
$html .= '</tr>';
foreach ($aPendientes as $id_nivel => $Pendientes) {
    $asignatura = $Pendientes['def'];
    //$html .= '<tr><td colspan=14>';
    $html .= '<tr><td>';
    $html .= $asignatura['nombre'] . " (" . $asignatura['creditos'] . " créditos, año " . $asignatura['year'] . ")";
    $html .= '</td>';
    //$html .= '</td></tr>';
    //$html .= '<tr>';
    $html .= '<td>' . $Pendientes['nb'] . '</td>';
    $html .= '<td>' . $Pendientes['nc1'] . '</td>';
    $html .= '<td>' . $Pendientes['nc2'] . '</td>';
    $html .= '<td>' . ($Pendientes['nb'] + $Pendientes['nc1'] + $Pendientes['nc2']) . '</td>';
    $html .= '<td>' . $Pendientes['ab'] . '</td>';
    $html .= '<td>' . $Pendientes['ac1'] . '</td>';
    $html .= '<td>' . $Pendientes['ac2'] . '</td>';
    $html .= '<td>' . ($Pendientes['ab'] + $Pendientes['ac1'] + $Pendientes['ac2']) . '</td>';
    $html .= '</td></tr>';

}
$html .= '</table>';

echo $html;
