<?php

/**
* Esta página sirve para calcular los créditos cursables para cada alumno en cada ca.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		5/3/03.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	use asignaturas\model\entity\GestorAsignatura;
use asistentes\model\entity\GestorAsistente;
use notas\model\AsignaturasPendientes;
use notas\model\entity\GestorPersonaNota;
use personas\model\entity\Persona;
use personas\model\entity\PersonaGlobal;
use ubis\model\entity\CentroDl;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
	
$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_activ = (integer) strtok($a_sel[0],"#");
    $nom_activ=strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel',$a_sel,1);
    $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id',$scroll_id,1);
}

//posibles valores de stgr
$aTipos_stgr = PersonaGlobal::$stgr_posibles;
/*  "n"=> _("no cursa est."),
    "b"=> _("bienio"),
    "c1"=>  _("cuadrienio año I"),
    "c2"=> _("cuadrienio año II-IV"),
    "r"=> _("repaso"),
*/
// Quito los que no hacen estudios:
unset ($aTipos_stgr["n"]);
unset ($aTipos_stgr["r"]);

// ----------------------- Selección de Alumnos -----------------
$a_alumnos_fin_c = [];
$a_alumnos = [];
$gesAsistentes = new GestorAsistente();
foreach($gesAsistentes->getAsistentes(array('id_activ'=>$id_activ)) as $oAsistente) {
    $id_nom=$oAsistente->getId_nom();
    $oPersona = Persona::NewPersona($id_nom);
    $stgr = $oPersona->getStgr();
    // sólo los que hacen estudios:
    if (!array_key_exists($stgr, $aTipos_stgr)) { continue; }
    $ap_nom = $oPersona->getApellidosNombre();
    // miro si son de los qeu sólo les faltan 4 para terminar el cuadrienio.
    $curso = 'cuadrienio';
    $Pendientes = new AsignaturasPendientes();
    $aNomAsignaturasFaltan = $Pendientes->asignaturasQueFaltanPersona($id_nom,$curso);
    if (count ($aNomAsignaturasFaltan) < 5 ) {
        $a_alumnos_fin_c[] = ['apellidos_nombre' => $ap_nom, 'asignaturas' => $aNomAsignaturasFaltan];
    }
    // busco las asignaturas aprobadas
    $GesNotas = new GestorPersonaNota();
    $aWhere=array();
    $aOperador=array();
    $aWhere['id_nom'] = $id_nom;
    $aWhere['id_nivel'] = '1100,2500';
    $aOperador['id_nivel']='BETWEEN';
    $cNotas = $GesNotas->getPersonaNotas($aWhere,$aOperador);
    $aAprobadas = [];
    foreach ($cNotas as $oPersonaNota) {
        $id_asignatura = $oPersonaNota->getId_asignatura();
        $id_nivel = $oPersonaNota->getId_nivel();
        $oF_acta = $oPersonaNota->getF_acta();
        $id_situacion = $oPersonaNota->getId_situacion();
        
        $aAprobadas[$id_asignatura] = $id_situacion;
    }
    $datos =  ['id_nom' => $id_nom, 'oPersona' => $oPersona, 'aprobadas' => $aAprobadas];
    $a_alumnos[] = ['apelidos_nombre' => $ap_nom, 'datos' => $datos];
    
}
// por orden alfabético.
sort($a_alumnos);

// ----------------------- Selección Asignaturas -----------------

$aWhereAsig = [ 'id_tipo' => 8,
                '_ordre'  => 'id_nivel', 
];
$aOperadorAsig = ['id_tipo' => '!='];
$gesAsignaturas = new GestorAsignatura();
$cAsignaturas = $gesAsignaturas->getAsignaturas($aWhereAsig,$aOperadorAsig);

$aAsignaturas_alumnos = []; 
foreach($cAsignaturas as $oAsignatura) {
    $nom_asignatura = $oAsignatura->getNombre_asignatura();
    $id_asignatura = $oAsignatura->getId_asignatura();
    $posibles_alumnos = 0;
    $aNombresAlumnos = [];
    foreach ($a_alumnos as $aAlumno) {
        $datos = $aAlumno['datos'];
        $id_nom = $datos['id_nom'];
        $aprobadas = $datos['aprobadas'];
        if (array_key_exists($id_asignatura, $aprobadas)) {
            $posibles_alumnos++;
            $aNombresAlumnos[] = $datos['oPersona']->getApellidosNombre();
        }
    }
    if ($posibles_alumnos == 0) { continue; }
    $aAsignaturas_alumnos[] = ['nom_asignatura' => $nom_asignatura,
                            'id_asignatura' => $id_asignatura,
                            'posibles_alumnos' => $posibles_alumnos,
                            'aNombresAlumnos' => $aNombresAlumnos,
                            ];
}

$a_campos = ['oPosicion' => $oPosicion,
            'nom_activ' => $nom_activ,
            'aAsignaturas_alumnos' => $aAsignaturas_alumnos,
            'a_alumnos_fin_c' => $a_alumnos_fin_c,
            ];

$oView = new core\ViewTwig('actividadestudios/controller');
echo $oView->render('posibles_asignaturas_ca.html.twig',$a_campos);