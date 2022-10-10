<?php

use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');

$url_ajax = "apps/pasarela/controller/activacion_ajax.php";

switch ($Qque) {
    case 'eliminar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $oActivacion = new \pasarela\model\Activacion();
        $oActivacion->delActivacion($Qid_tipo_activ);
        break;
    case 'update':
    case 'nuevo':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qactivacion = (string)filter_input(INPUT_POST, 'activacion');
        $oActivacion = new \pasarela\model\Activacion();
        $oActivacion->addActivacion($Qid_tipo_activ, $Qactivacion);
        break;
    case 'update_default':
        $Qdefault = (string)filter_input(INPUT_POST, 'default');
        $oActivacion = new \pasarela\model\Activacion();
        $oActivacion->setDefault($Qdefault);
        break;
    case 'lista':
        $oActivacion = new \pasarela\model\Activacion();
        echo $oActivacion->getLista();
        break;
    case 'form_default':
        $oActivacion = new \pasarela\model\Activacion();
        $default = $oActivacion->getDefault();
        $oHash = new Hash();
        $oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/pasarela/controller/activacion_ajax.php');
        $oHash->setCamposForm('default');
        $oHash->setArrayCamposHidden(['que' => 'update_default']);
        $a_campos = ['oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'default' => $default,
        ];

        $oView = new core\ViewTwig('pasarela/controller');
        echo $oView->render('activacion_default_form.html.twig', $a_campos);
        break;
    case 'form_modificar':
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qactivacion = (string)filter_input(INPUT_POST, 'activacion');

        $oActividadTipo = new \web\TiposActividades($Qid_tipo_activ);
        $svsf = $oActividadTipo->getSfsvText();
        $asistentes = $oActividadTipo->getAsistentesText();
        $actividad = $oActividadTipo->getActividadText();
        $tipo_txt = "$svsf $asistentes $actividad";

        $oHash = new Hash();
        $oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/pasarela/controller/activacion_ajax.php');
        $oHash->setCamposForm('id_tipo_activ!activacion');
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
            'activacion' => $Qactivacion,
        ];

        $oView = new core\ViewTwig('pasarela/controller');
        echo $oView->render('activacion_form.html.twig', $a_campos);
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
        $oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/pasarela/controller/activacion_ajax.php');
        $oHash->setCamposForm('iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!activacion');
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
        echo $oView->render('activacion_form_nuevo.html.twig', $a_campos);
        break;
}
