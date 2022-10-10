<?php

use pasarela\model\entity\PasarelaConfig;
use pasarela\model\Nombre;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');

$url_ajax = "apps/pasarela/controller/nombre_ajax.php";

switch ($Qque) {
    case 'eliminar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');

        $oNombre = new Nombre();
        $oNombre->delNombre($Qid_tipo_activ);

        break;
    case 'update':
    case 'nuevo':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qnombre_actividad = (string)filter_input(INPUT_POST, 'nombre_actividad');

        $oNombre = new Nombre();
        $oNombre->addNombre($Qid_tipo_activ, $Qnombre_actividad);

        break;
    case 'lista':
        $oNombre = new Nombre();
        echo $oNombre->getLista();
        break;
    case 'form_modificar':
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qnombre_actividad = (string)filter_input(INPUT_POST, 'nombre_actividad');

        $oActividadTipo = new \web\TiposActividades($Qid_tipo_activ);
        $svsf = $oActividadTipo->getSfsvText();
        $asistentes = $oActividadTipo->getAsistentesText();
        $actividad = $oActividadTipo->getActividadText();
        $tipo_txt = "$svsf $asistentes $actividad";

        $oHash = new Hash();
        $oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/pasarela/controller/nombre_ajax.php');
        $oHash->setCamposForm('id_tipo_activ!nombre_actividad');
        $oHash->setCamposNo('id_tipo_activ!que');
        $a_camposHidden = array(
            'id_tipo_activ' => $Qid_tipo_activ,
            'que' => '',
        );
        $oHash->setArraycamposHidden($a_camposHidden);

        $a_campos = ['oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'tipo_txt' => $tipo_txt,
            'nombre_actividad' => $Qnombre_actividad,
        ];

        $oView = new core\ViewTwig('pasarela/controller');
        echo $oView->render('nombre_form.html.twig', $a_campos);
        break;
    case 'form_nuevo':
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        //$Qisfsv = (integer) filter_input(INPUT_POST, 'isfsv');
        $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
        $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
        $Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');

        $oActividadTipo = new actividades\model\ActividadTipo();
        $oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
        $oActividadTipo->setAsistentes($Qsasistentes);
        $oActividadTipo->setActividad($Qsactividad);
        $oActividadTipo->setNom_tipo($Qsnom_tipo);
        $oActividadTipo->setPara('tipoactiv-tarifas');


        $oHash = new Hash();
        $oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/pasarela/controller/nombre_ajax.php');
        $oHash->setCamposForm('iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!nombre_actividad');
        $oHash->setCamposNo('id_tipo_activ!que');
        $a_camposHidden = array(
            'id_tipo_activ' => '',
            'que' => '',
        );
        $oHash->setArraycamposHidden($a_camposHidden);

        $a_campos = ['oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'oActividadTipo' => $oActividadTipo,
        ];

        $oView = new core\ViewTwig('pasarela/controller');
        echo $oView->render('nombre_form_nuevo.html.twig', $a_campos);
        break;
}
