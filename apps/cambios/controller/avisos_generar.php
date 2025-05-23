<?php

use cambios\model\entity\Cambio;
use cambios\model\entity\CambioDl;
use cambios\model\entity\CambioUsuario;
use cambios\model\entity\CambioUsuarioObjetoPref;
use cambios\model\entity\GestorCambioUsuario;
use core\ConfigGlobal;
use core\ViewTwig;
use src\usuarios\application\repositories\PreferenciaRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\Desplegable;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);
$QGstack = (integer)filter_input(INPUT_POST, 'Gstack');

// Tipos de avisos
$aTipos_aviso = CambioUsuarioObjetoPref::getTipos_aviso();
$dele = ConfigGlobal::mi_dele();
$delef = $dele . 'f';
$aSecciones = array(1 => $dele, 2 => $delef);

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($aTipos_aviso);

$UsuarioRepository = new UsuarioRepository();
$aUsuarios = $UsuarioRepository->getArrayUsuarios();

$oDesplUsuarios = new Desplegable();
$oDesplUsuarios->setNombre('id_usuario');
$oDesplUsuarios->setBlanco('false');
$oDesplUsuarios->setOpciones($aUsuarios);

if ($_SESSION['oPerm']->only_perm('admin_sf') || $_SESSION['oPerm']->only_perm('admin_sv')) {
    // sino en $Posicion. Le paso la referencia del stack donde está la información.
    if (!empty($Qrefresh) && !empty($QGstack)) {
        $oPosicion->goStack($QGstack);
        $Qid_usuario = $oPosicion->getParametro('id_usuario');
        $Qaviso_tipo = $oPosicion->getParametro('aviso_tipo');
    } else {
        $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
        $Qaviso_tipo = (integer)filter_input(INPUT_POST, 'aviso_tipo');
    }
} else {
    $Qid_usuario = ConfigGlobal::mi_id_usuario();
    $Qaviso_tipo = CambioUsuario::TIPO_LISTA; // de moment nomes "anotar en lista".
}

$aGoBack = [
    'id_usuario' => $Qid_usuario,
    'aviso_tipo' => $Qaviso_tipo,
];
$oPosicion->setParametros($aGoBack, 1);


$a_campos = [];

if (!empty($Qid_usuario)) {
    // buscar la zona horaria
    $PreferenciaRepository = new PreferenciaRepository();
    $oPreferencia = $PreferenciaRepository->findById($Qid_usuario, 'zona_horaria');
    if ($oPreferencia !== null) {
        $zona_horaria = $oPreferencia->getPreferencia();
    } else {
        $zona_horaria = '';
    }

    $DateTimeZone = new DateTimeZone('UTC');
    if (!empty($zona_horaria)) {
        try {
            $DateTimeZone = new DateTimeZone($zona_horaria);
        } catch (DateInvalidTimeZoneException $e) {
            $DateTimeZone = new DateTimeZone('UTC');
        }
    }

    // seleccionar por usuario
    $mi_sfsv = ConfigGlobal::mi_sfsv();

    $aWhere = [];
    $aWhere['id_usuario'] = $Qid_usuario;
    $aWhere['sfsv'] = $mi_sfsv;
    $aWhere['aviso_tipo'] = $Qaviso_tipo;
    $aWhere['avisado'] = 'false';
    $GesCambiosUsuario = new GestorCambioUsuario();
    $cCambiosUsuario = $GesCambiosUsuario->getCambiosUsuario($aWhere);
    $a_valores = [];
    $i = 0;
    foreach ($cCambiosUsuario as $oCambioUsuario) {
        $id_item_cmb = $oCambioUsuario->getId_item_cambio();
        $id_schema_cmb = $oCambioUsuario->getId_schema_cambio();
        if ($id_schema_cmb === 3000) {
            $oCambio = new Cambio($id_item_cmb);
        } else {
            $oCambio = new CambioDl($id_item_cmb);
        }
        $quien_cambia = $oCambio->getQuien_cambia();
        $sfsv_quien_cambia = $oCambio->getSfsv_quien_cambia();
        $oTimestamp_cambio_GMT = $oCambio->getTimestamp_cambio();
        $timestamp_cambio = $oTimestamp_cambio_GMT->setTimezone($DateTimeZone)->getFromLocalHora();
        $timestamp_orden = $oCambio->getTimestamp_cambio()->format('YmdHis');

        $aviso_txt = $oCambio->getAvisoTxt();
        if ($aviso_txt === false) {
            continue;
        }
        $i++;
        if ($sfsv_quien_cambia === $mi_sfsv) {
            $oUsuarioCmb = $UsuarioRepository->findById($quien_cambia);
            $quien = $oUsuarioCmb->getUsuario();
        } else {
            $quien = $aSecciones[$sfsv_quien_cambia];
        }
        $num_orden = $timestamp_orden . (1000 + $i); // añado $i por que si hay dos iguales, se sobreescribe.

        $a_valores[$num_orden]['sel'] = "$id_item_cmb#$Qid_usuario#$mi_sfsv#$Qaviso_tipo";
        $a_valores[$num_orden][1] = $timestamp_cambio;
        $a_valores[$num_orden][2] = $quien;
        $a_valores[$num_orden][3] = $aviso_txt;
    }
    ksort($a_valores, SORT_STRING);

    $a_cabeceras = [['name' => ucfirst(_("fecha cambio")), 'class' => 'fecha_hora'],
        ucfirst(_("quien")),
        ucfirst(_("cambio"))
    ];
    $a_botones = [
        array('txt' => _("borrar"), 'click' => "fnjs_borrar(\"#seleccionados\")"),
        array('txt' => _("todos"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\",0)"),
        array('txt' => _("ninguno"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\",0)"),
        // de momento no funciona para la slcikGrid
        // array( 'txt' => _("invertir"), 'click' =>"fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"toggle\",0)" ),
    ];

    $oTabla = new Lista();
    $oTabla->setId_tabla('avisos_tabla');
    $oTabla->setCabeceras($a_cabeceras);
    //$oTabla->setSortCol(ucfirst(_("fecha cambio"))); // Tiene que ser el nombre de la cabecera (mayusculas).
    $oTabla->setBotones($a_botones);
    $oTabla->setDatos($a_valores);

    $stack = $oPosicion->getStack();
    $oHash = new Hash();
    $oHash->setArrayCamposHidden(['que' => 'eliminar',
        'id_usuario' => $Qid_usuario,
        'aviso_tipo' => $Qaviso_tipo,
        'Gstack' => $stack,
    ]);
    $oHash->setCamposNo('f_fin!que!scroll_id!sel!refresh');

    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'oTabla' => $oTabla,
    ];

} else {
    $stack = $oPosicion->getStack();
    $oHashCond = new Hash();
    $oHashCond->setArrayCamposHidden(['Gstack' => $stack]);
    $oHashCond->setCamposForm("id_usuario!aviso_tipo");

    $a_camposCond = [
        'oPosicion' => $oPosicion,
        'oHashCond' => $oHashCond,
        'oDesplUsuarios' => $oDesplUsuarios,
        'oDesplTiposAviso' => $oDesplTiposAviso,
    ];

    $oView = new ViewTwig('cambios/controller');
    $oView->renderizar('avisos_generar_condicion.html.twig', $a_camposCond);
}

$oView = new ViewTwig('cambios/controller');
$oView->renderizar('avisos_generar_lista.html.twig', $a_campos);