<?php

use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargoHorario;
use web\Hash;
use web\Lista;
use encargossacd\model\entity\GestorEncargoTipo;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($Qid_sel)) { //vengo de un checkbox
    $Qid_enc = (integer)strtok($Qid_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $Qid_sel, 1);
    $Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $Qscroll_id, 1);
} else {
    $Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
}

$Qmod = (string)filter_input(INPUT_POST, 'mod');

$oEncargo = new Encargo($Qid_enc);
$desc_enc = $oEncargo->getDesc_enc();

$titulo = $desc_enc;

$GesEncargoTipo = new GestorEncargoTipo();

$GesEncargoHorario = new GestorEncargoHorario();
$cEncargoHorarios = $GesEncargoHorario->getEncargoHorarios(array('id_enc' => $Qid_enc));


$a_botones = array(array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")"),
    array('txt' => _("eliminar"), 'click' => "fnjs_borrar(\"#seleccionados\")")
);
$a_botones = [];

$a_cabeceras = array(array('name' => ucfirst(_("id")), 'formatter' => 'clickFormatter'),
    _("ord."),
    _("dia ref"),
    _("signo"),
    _("variación"),
    _("hora ini"),
    _("hora fin"),
    _("nº sacd"),
    _("mes"),
    array('name' => ucfirst(_("f ini")), 'class' => 'fecha'),
    array('name' => ucfirst(_("f fin")), 'class' => 'fecha'),
    _("excepciones"),
    _("texto")
);

$a_valores = array();
$i = 0;
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}
foreach ($cEncargoHorarios as $oEncargoHorario) {
    $i++;
    $id_enc = $oEncargoHorario->getId_enc();
    $id_item_h = $oEncargoHorario->getId_item_h();
    $dia_num = $oEncargoHorario->getDia_num();
    $dia_ref = $oEncargoHorario->getDia_ref();
    $mas_menos = $oEncargoHorario->getMas_menos();
    $dia_inc = $oEncargoHorario->getDia_inc();
    $h_ini = $oEncargoHorario->getH_ini();
    $h_fin = $oEncargoHorario->getH_fin();
    $n_sacd = $oEncargoHorario->getN_sacd();
    $mes = $oEncargoHorario->getMes();
    $f_ini = $oEncargoHorario->getF_ini()->getFromLocal();
    $f_fin = $oEncargoHorario->getF_fin()->getFromLocal();

    $aQuery = ['mod' => 'editar',
        'id_enc' => $id_enc,
        'id_item_h' => $id_item_h,
        'desc_enc' => $desc_enc,
    ];
    if (is_array($aQuery)) {
        array_walk($aQuery, 'core\poner_empty_on_null');
    }
    $pagina = Hash::link('apps/encargossacd/controller/horario_ver.php?' . http_build_query($aQuery));
    // miro si tinen excepciones:
    /*
    $sql_ex="SELECT * FROM t_horario_excepcion WHERE id_item_h=$id_item_h";
    //echo "query: $sql_ex<br>";
    $oDBSt_q2=$oDB->query($sql_ex);
    if ($oDBSt_q2->rowCount()>0) { $excep=_("si"); } else { $excep=""; }
    */
    $excep = '';

    $a_valores[$i]['sel'] = $id_item_h;
    $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $id_enc);
    //$a_valores[$i][2]=$row["nombre_ubi"];
    $a_valores[$i][3] = $dia_num;
    $a_valores[$i][4] = $dia_ref;
    $a_valores[$i][5] = $mas_menos;
    $a_valores[$i][6] = $dia_inc;
    $a_valores[$i][7] = $h_ini;
    $a_valores[$i][8] = $h_fin;
    $a_valores[$i][9] = $n_sacd;
    $a_valores[$i][10] = $mes;
    $a_valores[$i][11] = $f_ini;
    $a_valores[$i][12] = $f_fin;
    $a_valores[$i][13] = $excep;
    // Pruebo de poner el horario en texto, por si aclara.
    $texto_horario = $GesEncargoTipo->texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd);
    $a_valores[$i][14] = $texto_horario;

}

$aQuery = ['mod' => 'nuevo',
    'id_enc' => $Qid_enc,
    'desc_enc' => $desc_enc,
];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$pagina_nuevo = Hash::link('apps/encargossacd/controller/horario_ver.php?' . http_build_query($aQuery));

$oTabla = new Lista();
$oTabla->setId_tabla('encargo_horario_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$url_actualizar = 'apps/encargossacd/controller/horario_ver.php';
$oHash = new Hash();
$aCamposHidden = [
    'mod' => $Qmod,
    'desc_enc' => $desc_enc,
];
$oHash->setUrl($url_actualizar);
$oHash->setArrayCamposHidden($aCamposHidden);

$txt_eliminar = _("¿Esta Seguro que desea borrar este horario?");

$a_campos = ['oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'txt_eliminar' => $txt_eliminar,
    'pagina_nuevo' => $pagina_nuevo,
];

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('encargo_horario_select.html.twig', $a_campos);