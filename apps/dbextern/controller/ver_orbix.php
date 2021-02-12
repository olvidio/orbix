<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mov = '';

//$dl = (string)  filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo_persona');

$id = (string)  filter_input(INPUT_POST, 'id');
$mov = (string)  filter_input(INPUT_POST, 'mov');
//$tipo_persona = 'a';

switch ($tipo_persona) {
	case 'n':
		if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
			$obj_pau = 'GestorPersonaN';
		}
		break;
	case 'a':
		if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
			$obj_pau = 'GestorPersonaAgd';
		}
		break;
	case 's':
		if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
			$obj_pau = 'GestorPersonaS';
		}
		break;
}

function otro($id,$mov,$max) {
    $id = (integer)$id;
    if ($max == 0) {
        return FALSE;
    }
	switch($mov) {
		case '-':
			$id--;
			if ($id < 1) {
				return 1;
			}
			break;
		case '+':
			$id++;
			if ($id > $max) {
				return $max;
			}
			break;
		default:
			$id = 1;
	}
	if (isset($_SESSION['DBOrbix'][$id])) {
		return $id;
	} else {
		return otro($id,$mov,$max);
	}
}


if (empty($id)) {
	$id=1;
	$obj = 'personas\\model\\entity\\'.$obj_pau;
	$GesPersonas = new $obj();
	$cPersonasOrbix = $GesPersonas->getPersonasDl(array('situacion'=>'A','_ordre'=>'apellido1,apellido2,nom'));
	$i = 0;
	$a_lista = array();
	foreach ($cPersonasOrbix as $oPersonaListas) {
		$id_nom_orbix = $oPersonaListas->getId_nom();

		$oGesMatch = new dbextern\model\entity\GestorIdMatchPersona();
		$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_orbix'=>$id_nom_orbix));
		if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
			continue;
		}
		$a_persona_listas['id_nom_orbix'] = $id_nom_orbix;
		$a_persona_listas['ape_nom'] = $oPersonaListas->getPrefApellidosNombre();
		$a_persona_listas['nombre'] = $oPersonaListas->getNom();
		$a_persona_listas['apellido1'] = $oPersonaListas->getApellido1();
		$a_persona_listas['nx1'] = $oPersonaListas->getNx1();
		$a_persona_listas['apellido2'] = $oPersonaListas->getApellido2();
		$a_persona_listas['nx2'] = $oPersonaListas->getNx2();
		$a_persona_listas['f_nacimiento'] = $oPersonaListas->getF_nacimiento()->getFromLocal();

		// incremento antes para empezar en 1 y no en 0.
		$i++;
		$a_lista[$i] = $a_persona_listas;
	}
	$_SESSION['DBOrbix'] = $a_lista;
}


$max = count($_SESSION['DBOrbix']);

if ($max === 0) {
    $html_reg = _("No hay registros");
} else {
    $new_id = otro($id,$mov,$max);
    $persona_listas = $_SESSION['DBOrbix'][$new_id];

    $url_sincro_ver = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/ver_orbix.php';
    $oHash = new web\Hash();
    $oHash->setUrl($url_sincro_ver);
    $oHash->setcamposNo('mov');
    $a_camposHidden = array(
            //'dl' => $dl,
            'tipo_persona' => $tipo_persona,
            'id' => $new_id,
            );
    $oHash->setArraycamposHidden($a_camposHidden);

    $url_sincro_ajax = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ajax.php';
    $oHash1 = new web\Hash();
    $oHash1->setUrl($url_sincro_ajax);
    $oHash1->setCamposForm('que!id_nom_listas!id!id_orbix'); 
    $h1 = $oHash1->linkSinVal();

    $html_reg = sprintf(_("registro %s de %s"),$new_id,$max);
}
// ------------------ html ----------------------------------


if ($max === 0) {
   echo $html_reg; 
} else {
?>
    <script>

    fnjs_submit=function(formulario,mov){

        $('#mov').val(mov);
        
        $(formulario).attr('action',"<?= $url_sincro_ver ?>");
          fnjs_enviar_formulario(formulario);
    }
    </script>

    <h3><?= _("personas en aquinate sin unir a la BDU") ?></h3>

    <form id="movimiento" name="movimiento" action="">
        <?= $oHash->getCamposHtml(); ?>
        <input type="hidden" id="mov" name="mov" value="">
        <input type="button" value="< <?= _("anterior") ?>" onclick="fnjs_submit(this.form,'-')" />
        <?= $html_reg ?>
        <input type="button" value="<?= _("siguiente") ?> >" onclick="fnjs_submit(this.form,'+')" />
        <br>
        <br>
        
    <table>
    <?php
        echo "<tr>";
        echo "<td>".$persona_listas['id_nom_orbix'].'</td>';
        echo "<td class='titulo'>".$persona_listas['ape_nom'].'</td>';
        echo "<td>".$persona_listas['nombre'].'</td>';
        echo "<td>".$persona_listas['apellido1'].'</td>';
        echo "<td>".$persona_listas['apellido2'].'</td>';
        echo "<td class='titulo'>".$persona_listas['f_nacimiento'].'</td>';
        echo '</tr>';
    ?>
    </table>
    <br>
    Por el momento estos botones no hacen nada.
    <input type="button" value="<?= _("borrar") ?>" onclick="">
    <input type="button" value="<?= _("trasladar") ?>" onclick="">
    <input type="button" value="<?= _("baja") ?>" onclick="">
    </form>
<?php 
}
?>