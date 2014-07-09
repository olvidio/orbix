<?php
/**
*
*Página que pregunta dónde está la foto, y la copia en la base de datos
*
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$act = empty($_REQUEST['act'])? '' : $_REQUEST['act'];
$id_direccion = empty($_REQUEST['id_direccion'])? '' : $_REQUEST['id_direccion'];

switch ($act) {
case "eliminar":
	// compruebo si existe:
	$sql="SELECT id_direccion FROM public.u_direcciones_global WHERE id_direccion=$id_direccion";
	$oDBSt_q=$GLOBALS['oDBPC']->query($sql);
	if ($id_direccion=$oDBSt_q->fetchColumn()) {
		$sql_update="UPDATE public.u_direcciones_global SET plano_nom=:plano_nom,plano_extension=:plano_extension,plano_doc=:plano_doc WHERE id_direccion=$id_direccion";

		$nom=NULL;
		$extension=NULL;
		$fichero=NULL;

		$oDBSt_a=$GLOBALS['oDBPC']->prepare($sql_update);
		$oDBSt_a->bindParam(":plano_nom", $nom, PDO::PARAM_STR);
		$oDBSt_a->bindParam(":plano_extension", $extension, PDO::PARAM_STR);
		$oDBSt_a->bindParam(":plano_doc", $fichero, PDO::PARAM_LOB);

		$oDBSt_a->execute();
	}
	echo "<body onload=\"window.close();\" ></body>";
break;
case "comprobar":
	// compruebo si existe:
	$sql="SELECT plano_nom,plano_extension,plano_doc FROM public.u_direcciones_global WHERE id_direccion=?";
	//echo "sql: $sql_update<br>";
	$stmt = $GLOBALS['oDBPC']->prepare($sql);
	$stmt->execute(array($id_direccion));
	$stmt->bindColumn(1, $plano_nom, PDO::PARAM_STR, 256);
	$stmt->bindColumn(2, $plano_extension, PDO::PARAM_STR, 256);
	$stmt->bindColumn(3, $plano_doc, PDO::PARAM_LOB);
	$stmt->fetch(PDO::FETCH_BOUND);

	if (empty($plano_doc)) {
		$rta='no';
	} else {
		$rta='si';
	}
	echo "$rta";
break;
case "upload":
	if ($_FILES["userfile"]["error"] > 0) {
		echo "Error: " . $_FILES["userfile"]["error"] . "<br />";
	} else {
		$path_parts = pathinfo($_FILES["userfile"]["name"]);

		$nom=$path_parts['filename'];
		$extension=$path_parts['extension'];
		$userfile= $_FILES["userfile"]["tmp_name"];

		$fichero=file_get_contents($userfile);

		// compruebo si existe:
		$sql="SELECT id_direccion FROM public.u_direcciones_global WHERE id_direccion=$id_direccion";
		$oDBSt_q=$GLOBALS['oDBPC']->query($sql);
		if ($id_direccion=$oDBSt_q->fetchColumn()) {
			$sql_update="UPDATE public.u_direcciones_global SET plano_nom=:plano_nom,plano_extension=:plano_extension,plano_doc=:plano_doc WHERE id_direccion=$id_direccion";

			$nom=empty($nom)? '' : $nom;
			$extension=empty($extension)? '' : $extension;
			$fichero=empty($fichero)? '' : $fichero;

			$oDBSt_a=$GLOBALS['oDBPC']->prepare($sql_update);
			$oDBSt_a->bindParam(":plano_nom", $nom, PDO::PARAM_STR);
			$oDBSt_a->bindParam(":plano_extension", $extension, PDO::PARAM_STR);
			$oDBSt_a->bindParam(":plano_doc", $fichero, PDO::PARAM_LOB);

			$oDBSt_a->execute();
		} else {

			echo _("ERROR: No debería darse este caso");
		}
		//echo "sql: $sql_update<br>";
		//echo "<body onload='window.opener.fnjs_buscar(1); window.close();' ></body>";
		echo "<body onload='window.close();' ></body>";
	}
break;
case "download":
	$sql="SELECT plano_nom,plano_extension,plano_doc FROM public.u_direcciones_global WHERE id_direccion=?";
	//echo "sql: $sql_update<br>";
	$stmt = $GLOBALS['oDBPC']->prepare($sql);
	$stmt->execute(array($id_direccion));
	$stmt->bindColumn(1, $plano_nom, PDO::PARAM_STR, 256);
	$stmt->bindColumn(2, $plano_extension, PDO::PARAM_STR, 256);
	$stmt->bindColumn(3, $plano_doc, PDO::PARAM_LOB);
	$stmt->fetch(PDO::FETCH_BOUND);

	if (empty($plano_doc)) {
		exit( _("No existe un plano."));
	}

	$nom_ext=$plano_nom.".".$plano_extension;

	// Determine Content Type
    switch ($plano_extension) {
      case "odt": $ctype="application/vnd.oasis.opendocument.text"; break;
      case "pdf": $ctype="application/pdf"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "rtf": $ctype="application/msword"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      default: $ctype="application/force-download";
    }

	header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false); // required for certain browsers
    header("Content-Type: $ctype");
    //header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
    header("Content-Disposition: attachment; filename=\"".$nom_ext."\";" );
    header("Content-Transfer-Encoding: binary");
    //header("Content-Length: ".$fsize);
    ob_clean();
    flush();
	echo fpassthru($plano_doc);
	exit;
break;
case 'adjuntar': 
	$titulo=_("introducir documento");
	$txt_btn=ucfirst(_("introducir"));
	$act="upload";
	?>
	<!-- jQuery -->
	<script type="text/javascript" src='<?php echo core\ConfigGlobal::$web_scripts.'/jquery-ui-latest/js/jquery-1.7.1.min.js'; ?>'></script>

	<script>
	fnjs_introducir=function(){
		var id_direccion=$('#id_direccion').val();

		var url='<?= core\ConfigGlobal::getWeb() ?>/apps/ubis/controller/plano_bytea.php';
		var parametros='act=comprobar&id_direccion='+id_direccion+'&PHPSESSID=<?php echo session_id(); ?>';
			 
		$.ajax({
			url: url,
			type: 'post',
			data: parametros,
			success: function (rta) {
					//alert ('respuesta: '+rta);
					//rta_txt=rta.responseText;
					if (rta=='si') { seguro=confirm("<?php echo _("Ya existe un escrito. ¿Desea reemplazarlo?"); ?>"); } else { seguro=1; }
				},
			complete: function() { 
				if (seguro) { 
					$('#name_file').val($('#userfile').val());
					$('#frm_doc1').submit();
				} else {
					//$(siguiente).focus();
				}
			}
		});
	}
	</script>
	<h2><?= $titulo ?></h2>
	<form id="frm_doc1" name="frm_doc1" ENCTYPE="multipart/form-data" method="POST" action="plano_bytea.php">
	<input type="hidden" id="id_direccion" name="id_direccion" value="<?= $id_direccion ?>" >
	<input type="hidden" id="name_file" name="name_file" value="" >
	<input type="hidden" id="act" name="act" value="<?= $act ?>">
	<?= ucfirst(_("ubicación del fichero")) ?>
	<input type='file' id='userfile' name='userfile' size='30'><br><br>
	<br><input type='button' value='<?= $txt_btn ?>' id='B1' name='B1' onclick="fnjs_introducir();">
	</form>
	<?php
break;
}
?>
