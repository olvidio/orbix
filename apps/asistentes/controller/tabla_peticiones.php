<?php

use actividades\model\entity\Actividad;
use actividadplazas\model\entity\GestorActividadPlazas;
use actividadplazas\model\entity\GestorPlazaPeticion;
use asistentes\model\entity\Asistente;
use asistentes\model\entity\GestorAsistente;
use core\ConfigGlobal;
use core\ViewTwig;
use personas\model\entity\PersonaDl;
use web\Hash;
use web\Lista;
use web\TiposActividades;
use ubis\model\entity\GestorDelegacion;

//probar github

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_activ_old = (integer)strtok($a_sel[0], "#");
    $nom_activ = strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $id_activ_old = (integer)filter_input(INPUT_POST, 'id_activ_old');
    $oActividad = new Actividad($id_activ_old);
    $nom_activ = $oActividad->getNom_activ();
}

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

/*
 * Defino un array con los datos actuales, para saber volver después de navegar un rato
 */
$aGoBack = array(
    'id_activ_old' => $id_activ_old,
);
$oPosicion->setParametros($aGoBack, 1);


$queSel = (string)filter_input(INPUT_POST, 'queSel');

$a_cabeceras = [_("nombre"),
    _("peticiones (libres/concedidas)"),
];

$a_botones = [];

$gesAsistentes = new GestorAsistente();
$cAsistentes = $gesAsistentes->getAsistentesDeActividad($id_activ_old);

$oActividad = new Actividad($id_activ_old);
$id_tipo_activ = $oActividad->getId_tipo_activ();

$oTipoActividad = new TiposActividades($id_tipo_activ);
$sactividad = $oTipoActividad->getActividadText();

$mi_dele = ConfigGlobal::mi_delef();
$gesDelegacion = new GestorDelegacion();
$cDelegaciones = $gesDelegacion->getDelegaciones(array('dl' => $mi_dele));
$oDelegacion = $cDelegaciones[0];
$id_dl = $oDelegacion->getId_dl();

$a_valores = [];
$i = 0;
foreach ($cAsistentes as $oAsistente) {
    $i++;
    $id_nom = $oAsistente->getId_nom();
    // buscar otras opciones de ca
    $gesPlazasPeticion = new GestorPlazaPeticion();
    $aWhere = ['id_nom' => $id_nom, 'tipo' => $sactividad, '_ordre' => 'orden'];
    $aOperador ['tipo'] = '~';
    $cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion($aWhere, $aOperador);
    $posibles_activ = '';
    $gesActividadPlazas = new GestorActividadPlazas();
    foreach ($cPlazasPeticion as $key => $oPlazaPeticion) {
        $id_activ = $oPlazaPeticion->getId_activ();
        $nom_activ_i = '';
        if (!empty($id_activ)) {
            $oActividadPosible = new Actividad($id_activ);
            $nom_activ_i = $oActividadPosible->getNom_activ();
            $dl_org = $oActividad->getDl_org();
            // añadir plazas libres sobre totales

            $txt_plazas = '';
            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $concedidas = 0;
                $cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_dl' => $id_dl, 'id_activ' => $id_activ));
                foreach ($cActividadPlazas as $oActividadPlazas) {
                    $dl_tabla = $oActividadPlazas->getDl_tabla();
                    if ($dl_org == $dl_tabla) {
                        $concedidas = $oActividadPlazas->getPlazas();
                    }
                }
                $ocupadas = $gesAsistentes->getPlazasOcupadasPorDl($id_activ, $mi_dele);
                if ($ocupadas < 0) { // No se sabe
                    $libres = '-';
                } else {
                    $libres = $concedidas - $ocupadas;
                }
                if (!empty($concedidas)) {
                    $txt_plazas = " ($libres/$concedidas)";
                }
                $nom_activ_i .= $txt_plazas;
            }
            // link
            if ($id_activ !== $id_activ_old) {

                $aCamposHidden = ['mod' => 'mover',
                    'id_nom' => $id_nom,
                    'id_activ_old' => $id_activ_old,
                    'id_activ' => $id_activ,
                    'plaza' => Asistente::PLAZA_ASIGNADA,
                ];

                $oHash = new Hash();
                $oHash->setUrl(ConfigGlobal::getWeb() . '/apps/asistentes/controller/update_3101.php');
                $oHash->setArrayCamposHidden($aCamposHidden);
                $param_mover = $oHash->getParamAjax();

                $nom_activ_i = "<span class=\"link\" onClick=\"fnjs_cambiar_actividad('$param_mover')\">" . $nom_activ_i . "</span>";
            }

            $posibles_activ .= empty($posibles_activ) ? '' : ', ';
            $posibles_activ .= $nom_activ_i;
        }
    }
    $oPersona = new PersonaDl($id_nom);
    $nom_ap = $oPersona->getApellidosNombre();

    $a_valores[$i][1] = $nom_ap;
    $a_valores[$i][2] = $posibles_activ;
}

if (!empty($a_valores)) {
    if (isset($Qid_sel) && !empty($Qid_sel)) {
        $a_valores['select'] = $Qid_sel;
    }
    if (isset($Qscroll_id) && !empty($Qscroll_id)) {
        $a_valores['scroll_id'] = $Qscroll_id;
    }
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

$oView = new ViewTwig('asistentes/controller');
$oView->renderizar('tabla_peticiones.html.twig', $a_campos);