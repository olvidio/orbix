<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mov = '';

$region = (string)  filter_input(INPUT_POST, 'region');
$dl = (string)  filter_input(INPUT_POST, 'dl');
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

$oSincroDB = new dbextern\model\sincroDB();
$oSincroDB->setTipo_persona($tipo_persona);
$oSincroDB->setRegion($region);
$oSincroDB->setDlListas($dl);

$id_nom_orbix = '';
if (empty($id)) {
	$id=1;
	$obj = 'personas\\model\\entity\\'.$obj_pau;
	$GesPersonas = new $obj();
	$cPersonasOrbix = $GesPersonas->getPersonasDl(array('situacion'=>'A','_ordre'=>'apellido1,apellido2,nom'));
	$i = 0;
	$a_lista = array();
	foreach ($cPersonasOrbix as $oPersonaOrbix) {
        $a_persona_orbix = [];
		$id_nom_orbix = $oPersonaOrbix->getId_nom();

		$oGesMatch = new dbextern\model\entity\GestorIdMatchPersona();
		$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_orbix'=>$id_nom_orbix));
		if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
			continue;
		}
		$a_persona_orbix['id_nom_orbix'] = $id_nom_orbix;
		$a_persona_orbix['ape_nom'] = $oPersonaOrbix->getPrefApellidosNombre();
		$a_persona_orbix['nombre'] = $oPersonaOrbix->getNom();
		$a_persona_orbix['apellido1'] = $oPersonaOrbix->getApellido1();
		$a_persona_orbix['nx1'] = $oPersonaOrbix->getNx1();
		$a_persona_orbix['apellido2'] = $oPersonaOrbix->getApellido2();
		$a_persona_orbix['nx2'] = $oPersonaOrbix->getNx2();
		$a_persona_orbix['f_nacimiento'] = $oPersonaOrbix->getF_nacimiento()->getFromLocal();

		// incremento antes para empezar en 1 y no en 0.
		$i++;
		$a_lista[$i] = $a_persona_orbix;
	}
	$_SESSION['DBOrbix'] = $a_lista;
}


$max = count($_SESSION['DBOrbix']);

$a_lista_bdu = [];
$a_lista_bdu_otradl = [];
$persona_orbix = [];
if ($max === 0) {
    $html_reg = _("No hay registros");
} else {
    $new_id = otro($id,$mov,$max);
    $persona_orbix = $_SESSION['DBOrbix'][$new_id];

    $id_nom_orbix = $persona_orbix['id_nom_orbix'];
    
    $a_lista_bdu = $oSincroDB->posiblesBDU($id_nom_orbix);
    
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
    	<tr><th><?= _("id aquinate") ?></th><th><?= _("ape_nom-calculado") ?></th><th><?= _("nombre") ?></th>
		<th><?= _("apellido1") ?></th><th><?= _("apellido2") ?></th><th><?= _("fecha nacimiento") ?></th></tr>
    <?php
        echo "<tr>";
        echo "<td>".$persona_orbix['id_nom_orbix'].'</td>';
        echo "<td class='titulo'>".$persona_orbix['ape_nom'].'</td>';
        echo "<td>".$persona_orbix['nombre'].'</td>';
        echo "<td>".$persona_orbix['apellido1'].'</td>';
        echo "<td>".$persona_orbix['apellido2'].'</td>';
        echo "<td class='titulo'>".$persona_orbix['f_nacimiento'].'</td>';
        echo '</tr>';
    ?>
    </table>
    <br>
    Por el momento estos botones no hacen nada.
    <input type="button" value="<?= _("borrar") ?>" onclick="">
    <input type="button" value="<?= _("trasladar") ?>" onclick="">
    <input type="button" value="<?= _("baja") ?>" onclick="">
    </form>
    <br>
    <?php if (!empty($a_lista_bdu)) { ?>
        <h3><?= _("posibles coincidencias con personas de la BDU en otras dl/r") ?>:</h3>
        <table>
            <tr><th><?= _("esquema") ?></th><th><?= _("id aquinate") ?></th><th><?= _("ape_nom-calculado") ?></th><th><?= _("nombre") ?></th>
                <th><?= _("apellido1") ?></th><th><?= _("apellido2") ?></th><th><?= _("fecha nacimiento") ?></th></tr>
        <?php
            foreach ($a_lista_bdu as $persona_bdu) {
                $id_bdu = $persona_bdu['id_nom'];
                echo "<tr>";
                echo "<td>".$persona_bdu['esquema'].'</td>';
                echo "<td>".$persona_bdu['id_nom'].'</td>';
                echo "<td class='contenido'>".$persona_bdu['ape_nom'].'</td>';
                echo "<td>".$persona_bdu['nombre'].'</td>';
                echo "<td>".$persona_bdu['apellido1'].'</td>';
                echo "<td>".$persona_bdu['apellido2'].'</td>';
                echo "<td class='contenido'>".$persona_bdu['f_nacimiento'].'</td>';
                echo "<td class='titulo'><span class=link onClick='fnjs_unir($id_bdu)'>" . _("unir") . '</span></td>';
                echo '</tr>';
            }
        ?>
        </table>
    <?php }
}