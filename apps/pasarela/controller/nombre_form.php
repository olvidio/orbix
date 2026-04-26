<?php

use src\shared\config\ConfigGlobal;
use core\ViewTwig;
use web\Hash;

/**
 * Esta página muestra un formulario para asociar un nombre a un tipo de actividad.
 * Si es nueva se puede escoger el tipo de actividad.
 * Si ya existe, sólo se puede modificar el nombre
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        24/2/09.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

$miSfsv = 0;
// -------------- MODIFICAR  --------------------
if ($Qid_item !== 'nuevo') {
    $txt_eliminar = _("¿Está seguro que desea quitar esta id_tarifa?");

    $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
    $oTipoActiv = new src\actividades\domain\entity\TiposActividades($id_tipo_activ);
    $isfsv = $oTipoActiv->getSfsvId();

    // NOTA: `nombre_form.php` esta muerto (solo referenciado por
    // ficheros .po). Se actualizan las URLs a los endpoints migrados
    // para que `grep` no quede apuntando a rutas `apps/` borradas.
    $oHash = new Hash();
    $oHash->setUrl(ConfigGlobal::getWeb() . '/src/actividadtarifas/relacion_tarifa_update');
    $oHash->setCamposForm('id_item!id_tarifa!id_tipo_activ');
    $a_camposHidden = array(
        'id_tipo_activ' => $Qid_tipo_activ,
        'id_item' => $Qid_item,
    );
    $oHash->setArraycamposHidden($a_camposHidden);

    $oHash1 = new Hash();
    $oHash1->setUrl(ConfigGlobal::getWeb() . '/src/actividades/actividad_tipo_get');
    $oHash1->setCamposForm('extendida!modo!salida!entrada!opcion_sel!isfsv');
    $h = $oHash1->linkSinVal();

    $url_ajax = ConfigGlobal::getWeb() . '/src/actividadtarifas/relacion_tarifa_update';

    $a_campos = ['oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'h' => $h,
        'oTipoActiv' => $oTipoActiv,
        'txt_eliminar' => $txt_eliminar,
        'url_ajax' => $url_ajax,
    ];

    $oView = new ViewTwig('pasarela/controller');
    $oView->renderizar('nombre_form.html.twig', $a_campos);

} else {
    // -------------- NUEVA  --------------------
    //para una actividad nueva, sólo mi sección.
    $miSfsv = ConfigGlobal::mi_sfsv();

    $txt_eliminar = _("¿Está seguro que desea borrar este nombre?");

    $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
    //$Qisfsv = (integer) filter_input(INPUT_POST, 'isfsv');
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');

    $oActividadTipo = new \src\actividades\application\ActividadTipo();
    $oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
    $oActividadTipo->setAsistentes($Qsasistentes);
    $oActividadTipo->setActividad($Qsactividad);
    $oActividadTipo->setNom_tipo($Qsnom_tipo);
    $oActividadTipo->setPara('tipoactiv-tarifas');


    $oHash = new Hash();
    $oHash->setUrl(ConfigGlobal::getWeb() . '/src/actividadtarifas/relacion_tarifa_update');
    $oHash->setCamposForm('iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!nombre_actividad');
    $oHash->setCamposNo('id_tipo_activ');
    $a_camposHidden = array(
        'id_tipo_activ' => '',
    );
    $oHash->setArraycamposHidden($a_camposHidden);

    $oHash1 = new Hash();
    $oHash1->setUrl(ConfigGlobal::getWeb() . '/src/actividades/actividad_tipo_get');
    $oHash1->setCamposForm('extendida!modo!salida!entrada!opcion_sel!isfsv');
    $h = $oHash1->linkSinVal();


    $a_campos = ['oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'h' => $h,
        'oActividadTipo' => $oActividadTipo,
    ];

    $oView = new ViewTwig('pasarela/controller');
    $oView->renderizar('nombre_form_nuevo.html.twig', $a_campos);
}
