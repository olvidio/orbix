<?php

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qclase_info = (string)filter_input(INPUT_POST, 'clase_info');
$Qdatos_buscar = (string)filter_input(INPUT_POST, 'datos_buscar');
$QaSerieBuscar = (string)filter_input(INPUT_POST, 'aSerieBuscar');
$Qk_buscar = (string)filter_input(INPUT_POST, 'k_buscar');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qid_pau = (string)filter_input(INPUT_POST, 'id_pau'); // necesario para nuevo.

$aQuery = array(
    'clase_info' => $Qclase_info,
    'datos_buscar' => $Qdatos_buscar,
    'aSerieBuscar' => $QaSerieBuscar,
    "k_buscar" => $Qk_buscar,
    'id_pau' => $Qid_pau,
    'mod' => $Qmod,
    'permiso' => $Qpermiso,
);
// las claves primarias se usan para crear el objeto en el include $dir_datos.
// También se pasan por formulario al update.
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$stack = '';
if (!empty($a_sel) && ($Qmod !== 'nuevo')) { //vengo de un checkbox (para el caso de nuevo no hay que guardar el check)
    $Qs_pkey = explode('#', $a_sel[0]);
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $Qs_pkey = str_replace("'", '"', $Qs_pkey[0]);
    //$a_pkey = unserialize(core\urlsafe_b64decode($Qs_pkey), ['allowed_classes' => false]);
    $a_pkey = json_decode(core\urlsafe_b64decode($Qs_pkey));
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    $aQuery['sel'] = $a_sel;
    $aQuery['scroll_id'] = $scroll_id;
    // add stack:
    $stack = $oPosicion->getStack(1);
    $aQuery['stack'] = $stack;
} else { // si es nuevo
    $Qs_pkey = '';
    $a_pkey = [];
}

$web_depende = ConfigGlobal::getWeb() . "/src/shared/repo_tabla_depende.php";
/* generar url go_to para volver a la tabla */
$aQuery['s_pkey'] = $Qs_pkey;
// para los dossiers
if (!empty($Qobj_pau)) {
    $aQuery['obj_pau'] = $Qobj_pau;
    $sQuery = http_build_query($aQuery);
    $Qgo_to = Hash::link(ConfigGlobal::getWeb() . "/apps/dossiers/controller/dossiers_ver.php?$sQuery");
} else {
    $sQuery = http_build_query($aQuery);
    $Qgo_to = Hash::link(ConfigGlobal::getWeb() . "/src/shared/repo_tabla_sql.php?$sQuery");
}

// Tiene que ser en dos pasos.
$obj = $Qclase_info;
$oInfoClase = new $obj();

$oInfoClase->setMod($Qmod);
$oInfoClase->setA_pkey($a_pkey);
$oInfoClase->setObj_pau($Qobj_pau);
$oFicha = $oInfoClase->getFicha();
$aCamposDepende = $oInfoClase->getArrayCamposDepende();
$aOpciones_txt = [];
foreach ($aCamposDepende as $pKeyRepository => $campoDepende) {
    $bbb = 'get' . ucfirst($pKeyRepository);
    $id_pau = $oFicha->$bbb();
    $aaa = 'get' . ucfirst($campoDepende);
    $valor_campo_depende = $oFicha->$aaa();
    $aOpciones_txt[$campoDepende] = $oInfoClase->getOpcionesParaCondicion($campoDepende,$id_pau,$valor_campo_depende);
}

$oDatosForm = new \src\shared\DatosFormRepo();
$oDatosForm->setFicha($oFicha);
$oDatosForm->setArrayOpcionesTxt($aOpciones_txt);
$oDatosForm->setMod($Qmod);

$tit_txt = $oInfoClase->getTxtTitulo();
$explicacion_txt = $oInfoClase->getTxtExplicacion();

$camposForm = $oDatosForm->getCamposForm();
$camposNo = $oDatosForm->getCamposNo();

$oHashSelect = new Hash();
$oHashSelect->setCamposForm($camposForm);
$oHashSelect->setCamposNo('sel!' . $camposNo);
$a_camposHidden = array(
    'clase_info' => $Qclase_info,
    'datos_buscar' => $Qdatos_buscar,
    'aSerieBuscar' => $QaSerieBuscar,
    "k_buscar" => $Qk_buscar,
    's_pkey' => $Qs_pkey,
    'id_pau' => $Qid_pau,
    'obj_pau' => $Qobj_pau,
    'mod' => $Qmod,
    'go_to' => $Qgo_to
);
$oHashSelect->setArraycamposHidden($a_camposHidden);


$clase_info = urlencode($Qclase_info);
$oHash1 = new Hash();
$oHash1->setUrl($web_depende);
$oHash1->setCamposForm('clase_info!accion!valor_depende');
$h = $oHash1->linkSinVal();

echo $oPosicion->mostrar_left_slide(1);
?>
<script>
    fnjs_grabar = function (formulario) {
        let url = "src/shared/repo_tabla_update.php";
        let parametros = $(formulario).serialize();

        let request = $.ajax({
            url: url,
            data: parametros,
            type: 'post',
            dataType: 'html'
        });
        request.done(function (rta_txt) {
            if (rta_txt !== '' && rta_txt !== '\\n') {
                alert('<?= _("respuesta") ?>: ' + rta_txt);
            } else {
                <?= $oPosicion->js_atras(1) ?>
            }
        });
    }

    fnjs_cancelar = function () {
        <?= $oPosicion->js_atras(1) ?>
    }

    fnjs_actualizar_depende = function (camp, accion) {
        let valor_depende = $('#' + camp).val();
        let parametros = 'clase_info=<?= $clase_info?>&accion=' + accion + '&valor_depende=' + valor_depende + '<?= $h ?>';
        let url = '<?= $web_depende ?>';
        $.ajax({
            url: url,
            type: 'post',
            data: parametros,
            dataType: 'html'
        })
            .done(function (rta_txt) {
                $('#' + accion).html(rta_txt);
            });
        return false;
    }

    $('#seleccionados').ready(function () {
        $('#seleccionados .fecha').each(function () {
            $(this).datepicker();
        });
    });
</script>
<form id="seleccionados" action="" method="POST" name="seleccionados">
    <?= $oHashSelect->getCamposHtml() ?>

    <h3 class=subtitulo><?= ucfirst($tit_txt) ?></h3>
    <h4><?= ucfirst($explicacion_txt) ?></h4>
    <table>
        <?= $oDatosForm->getFormulario() ?>
    </table>
    <br>
    <table>
        <tr>
            <td><input type="button" name="guardar" value="<?= ucfirst(_("guardar")) ?>"
                       onclick="fnjs_grabar('#seleccionados')"></td>
            <td><input type="button" name="atras" value="<?= ucfirst(_("cancelar")) ?>"
                       onclick="fnjs_cancelar()"></td>
        </tr>
    </table>
</form>