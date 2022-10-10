<?php

use pasarela\model\ContribucionNoDuerme;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');

$url_ajax = "apps/pasarela/controller/contribucion_no_duerme_ajax.php";

switch ($Qque) {
    case 'eliminar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        $oContribucionNoDuerme->delContribucionNoDuerme($Qid_tipo_activ);
        break;
    case 'update':
    case 'nuevo':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qcontribucion = (string)filter_input(INPUT_POST, 'contribucion');
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        $oContribucionNoDuerme->addContribucionNoDuerme($Qid_tipo_activ, $Qcontribucion);
        break;
    case 'update_default':
        $Qdefault = (string)filter_input(INPUT_POST, 'default');
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        $oContribucionNoDuerme->setDefault($Qdefault);
        break;
    case 'lista':
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        echo $oContribucionNoDuerme->getLista();
        break;
    case 'form_default':
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        $default = $oContribucionNoDuerme->getDefault();
        $oHash = new Hash();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('default');
        $oHash->setArrayCamposHidden(['que' => 'update_default']);
        $a_campos = ['oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'default' => $default,
        ];

        $oView = new core\ViewTwig('pasarela/controller');
        echo $oView->render('contribucion_no_duerme_default_form.html.twig', $a_campos);
        break;
    case 'form_modificar':
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qcontribucion = (string)filter_input(INPUT_POST, 'contribucion');

        $oActividadTipo = new \web\TiposActividades($Qid_tipo_activ);
        $svsf = $oActividadTipo->getSfsvText();
        $asistentes = $oActividadTipo->getAsistentesText();
        $actividad = $oActividadTipo->getActividadText();
        $tipo_txt = "$svsf $asistentes $actividad";

        $oHash = new Hash();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('id_tipo_activ!contribucion');
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
            'contribucion' => $Qcontribucion,
        ];

        $oView = new core\ViewTwig('pasarela/controller');
        echo $oView->render('contribucion_no_duerme_form.html.twig', $a_campos);
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
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!contribucion');
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
        echo $oView->render('contribucion_no_duerme_form_nuevo.html.twig', $a_campos);
        break;
}
