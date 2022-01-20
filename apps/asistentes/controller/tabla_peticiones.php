<?php 

use actividades\model\entity\Actividad;
use actividadplazas\model\entity\GestorPlazaPeticion;
use asistentes\model\entity\GestorAsistente;
use personas\model\entity\PersonaDl;
use web\Lista;
use actividades\model\entity\TipoDeActividad;
use web\TiposActividades;

/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_pau = (integer) strtok($a_sel[0],"#");
    $nom_activ=strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel',$a_sel,1);
    $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
    $id_pau = (integer) \filter_input(INPUT_POST, 'id_pau');
    $oActividad = new Actividad($id_pau);
    $nom_activ = $oActividad->getNom_activ();
}

$queSel = (string) \filter_input(INPUT_POST, 'queSel');

$a_cabeceras = [ _("nombre"),
                _("observaciones"),
    ];

$a_botones = [];

$gesAsistentes = new GestorAsistente();
$cAsistentes = $gesAsistentes->getAsistentesDeActividad($id_pau);

$oActividad = new Actividad($id_pau);
$id_tipo_activ = $oActividad->getId_tipo_activ();

$oTipoActividad = new TiposActividades($id_tipo_activ);
$sactividad = $oTipoActividad->getActividadText();

$a_valores = [];
$i = 0;
foreach ($cAsistentes as $oAsistente) {
    $i++;
    $id_nom = $oAsistente->getId_nom();
    // buscar otras opciones de ca
    $gesPlazasPeticion = new GestorPlazaPeticion();
    $aWhere = ['id_nom'=>$id_nom, 'tipo'=>$sactividad, '_ordre'=>'orden'];
    $aOperador ['tipo'] = '~';
    $cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion($aWhere,$aOperador);
    $posibles_activ = '';
    foreach ($cPlazasPeticion as $key => $oPlazaPeticion) {
        $id_activ = $oPlazaPeticion->getId_activ();
        $nom_activ_i = '';
        if (!empty($id_activ)) {
            $oActividadPosible = new Actividad($id_activ);
            $nom_activ_i = $oActividadPosible->getNom_activ();
            // añadir plazas libres sobre totales
            
            // link
            if ($id_activ !== $id_pau) {
                $link = 'fnjs_cambiar_actividad';
                $nom_activ_i = "<span onClick=\"fnjs_cambiar_actividad($id_nom,$id_pau,$id_activ)\">" . $nom_activ_i ."</span>";
            }
            
            
            $posibles_activ .= empty($posibles_activ)? '' : ', ';
            $posibles_activ .= $nom_activ_i;
        }
    }
    
    
    $observaciones = $oAsistente->getObserv();
    
    $oPersona = new PersonaDl($id_nom);
    
    $nom_ap = $oPersona->getApellidosNombre();
    
    $a_valores[$i][1] = $nom_ap;
    $a_valores[$i][2] = $posibles_activ;
   
}




$oTabla = new Lista();
$oTabla->setId_tabla('tabla_peticiones');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'oTabla' => $oTabla,
];

$oView = new core\ViewTwig('asistentes/controller');
echo $oView->render('tabla_peticiones.html.twig',$a_campos);