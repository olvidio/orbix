<?php
/**
 *
 *Página que pregunta dónde está la foto, y la copia en la base de datos
 *
 */

use core\ConfigGlobal;

// para que funcione bien la seguridad
$_POST = $_REQUEST;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qact = (string)filter_input(INPUT_POST, 'act');
$Qid_direccion = (integer)filter_input(INPUT_POST, 'id_direccion');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
// Cuando abro una nueva ventana, los parametros están en $_GET
if (empty($Qact)) {
    $Qact = (string)filter_input(INPUT_GET, 'act');
    $Qid_direccion = (integer)filter_input(INPUT_GET, 'id_direccion');
    $Qobj_dir = (string)filter_input(INPUT_GET, 'obj_dir');
}
$obj = 'ubis\\model\\entity\\' . $Qobj_dir;

switch ($Qact) {
    case "eliminar":
        $oDireccion = new $obj($Qid_direccion);
        $aDatosPlano = $oDireccion->planoBorrar();
        echo "<body onload=\"window.close();\" ></body>";
        break;
    case "comprobar":
        // compruebo si existe:
        $oDireccion = new $obj($Qid_direccion);
        $aDatosPlano = $oDireccion->planoDownload();

        $plano_nom = $aDatosPlano['plano_nom'];
        $plano_extension = $aDatosPlano['plano_extension'];
        $plano_doc = $aDatosPlano['plano_doc'];

        if (empty($plano_doc)) {
            $rta = 'no';
        } else {
            $rta = 'si';
        }
        echo "$rta";
        break;
    case "upload":
        if ($_FILES["userfile"]["error"] > 0) {
            echo "Error: " . $_FILES["userfile"]["error"] . "<br />";
        } else {
            $path_parts = pathinfo($_FILES["userfile"]["name"]);

            $nom = $path_parts['filename'];
            $extension = $path_parts['extension'];
            $userfile = $_FILES["userfile"]["tmp_name"];

            $fichero = file_get_contents($userfile);

            $oDireccion = new $obj($Qid_direccion);
            $oDireccion->planoUpload($nom, $extension, $fichero);
            //echo "sql: $sql_update<br>";
            //echo "<body onload='window.opener.fnjs_buscar(1); window.close();' ></body>";
            echo "<body onload='window.close();' ></body>";
        }
        break;
    case "download":
        $oDireccion = new $obj($Qid_direccion);
        $aDatosPlano = $oDireccion->planoDownload();

        $plano_nom = $aDatosPlano['plano_nom'];
        $plano_extension = $aDatosPlano['plano_extension'];
        $plano_doc = $aDatosPlano['plano_doc'];

        if (empty($plano_doc)) {
            exit(_("no existe un plano"));
        }

        $nom_ext = $plano_nom . "." . $plano_extension;

        // Determine Content Type
        switch ($plano_extension) {
            case "odt":
                $ctype = "application/vnd.oasis.opendocument.text";
                break;
            case "pdf":
                $ctype = "application/pdf";
                break;
            case "exe":
                $ctype = "application/octet-stream";
                break;
            case "zip":
                $ctype = "application/zip";
                break;
            case "rtf":
                $ctype = "application/msword";
                break;
            case "doc":
                $ctype = "application/msword";
                break;
            case "xls":
                $ctype = "application/vnd.ms-excel";
                break;
            case "ppt":
                $ctype = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpg";
                break;
            default:
                $ctype = "application/force-download";
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); // required for certain browsers
        header("Content-Type: $ctype");
        //header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
        header("Content-Disposition: attachment; filename=\"" . $nom_ext . "\";");
        header("Content-Transfer-Encoding: binary");
        //header("Content-Length: ".$fsize);
        ob_clean();
        flush();
        echo fpassthru($plano_doc);
        die();
        break;
    case 'adjuntar':
        $url = ConfigGlobal::getWeb() . '/apps/ubis/controller/plano_bytea.php';
        $oHashComprobar = new web\Hash();
        $oHashComprobar->setUrl($url);
        /*
        $a_camposHidden = array(
                'id_direccion' => $Qid_direccion,
                'obj_dir' => $Qobj_dir,
                'act' => 'comprobar',
                );
        $oHashComprobar->setArraycamposHidden($a_camposHidden);
        */
        $oHashComprobar->setcamposForm('id_direccion!obj_dir!act');
        $h = $oHashComprobar->linkSinVal();

        $oHash = new web\Hash();
        $a_camposHidden = array(
            'id_direccion' => $Qid_direccion,
            'obj_dir' => $Qobj_dir,
            'act' => 'upload',
        );
        $camposForm = 'name_file!userfile';
        $oHash->setcamposForm($camposForm);
        $oHash->setCamposNo('userfile');
        $oHash->setArraycamposHidden($a_camposHidden);

        $titulo = _("introducir documento");
        $txt_btn = ucfirst(_("introducir"));
        $act = "upload";
        ?>
        <!-- jQuery -->
        <script type="text/javascript"
                src='<?= ConfigGlobal::getWeb_NodeScripts() . '/jquery/dist/jquery.min.js'; ?>'></script>
        <script>
            fnjs_introducir = function () {
                var id_direccion = $('#id_direccion').val();

                var url = '<?= ConfigGlobal::getWeb() ?>/apps/ubis/controller/plano_bytea.php';
                var parametros = 'act=comprobar&obj_dir=<?= $Qobj_dir?><?= $h ?>&id_direccion=' + id_direccion;

                $.ajax({
                    url: url,
                    type: 'post',
                    data: parametros
                })
                    .done(function (rta_txt) {
                        if (rta_txt == 'si') {
                            seguro = confirm("<?= _("ya existe un escrito. ¿Desea reemplazarlo?"); ?>");
                        } else {
                            seguro = 1;
                        }
                        if (seguro) {
                            $('#name_file').val($('#userfile').val());
                            $('#frm_doc1').trigger("submit");
                        } else {
                            //$(siguiente).trigger("focus");
                        }
                    });
            }
        </script>
        <h2><?= $titulo ?></h2>
        <form id="frm_doc1" name="frm_doc1" ENCTYPE="multipart/form-data" method="POST" action="plano_bytea.php">
            <?= $oHash->getCamposHtml(); ?>
            <input type="hidden" id="name_file" name="name_file" value="">
            <?= ucfirst(_("ubicación del fichero")) ?>
            <input type='file' id='userfile' name='userfile' size='30'><br><br>
            <br><input type='button' value="<?= $txt_btn ?>" id='B1' name='B1' onclick="fnjs_introducir();">
        </form>
        <?php
        break;
}
