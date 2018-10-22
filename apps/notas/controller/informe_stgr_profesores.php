<?php
use notas\model as notas;
/**
* Esta página sirve para el resumen anual de los profesores
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		23/3/2007
 * @version september 2018
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
/* Pongo en la variable $curs el periodo del curso */
$any=date("Y");
$mes=date("m");
if ($mes>3) {
	$any1=$any-1; 
	$curso_txt="$any1-$any";
	$any_ini_curs = $any1;
} else { 
	$any1=$any-2;
	$any--;
	$curso_txt="$any1-$any";
	$any_ini_curs = $any1;
}

$Qlista = (string) \filter_input(INPUT_POST, 'lista');
$lista = empty($Qlista)? false : true;

/* tipos de profesor:
	1 Ordinario
	2 Extraordinario
	3 Adjunto
	4 Encargado
	5 Ayudante
	6 Asociado
*/
$Resumen = new notas\Resumen('profesores');
$Resumen->setAnyIniCurs($any_ini_curs);
$Resumen->setLista($lista);
$Resumen->nuevaTablaProfe();

//36. nº de profesores ordinarios:
$res[36] = $Resumen->profesorDeTipo(1);
$a_textos[36] = ucfirst(_("número de profesores ordinarios"));
//37. Número de profesores extraordinarios
$res[37] = $Resumen->profesorDeTipo(2);
$a_textos[37] = ucfirst(_("número de profesores extraordinarios"));
//38. Número de profesores adjuntos
$res[38] = $Resumen->profesorDeTipo(3);
$a_textos[38] = ucfirst(_("número de profesores adjuntos"));
//39. Número de profesores encargados
$res[39] = $Resumen->profesorDeTipo(4);
$a_textos[39] = ucfirst(_("número de profesores encargados"));
//40. Número de profesores asociados
$res[40] = $Resumen->profesorDeTipo(6);
$a_textos[40] = ucfirst(_("número de profesores asociados"));
//41. Número de profesores ayudantes
$res[41] = $Resumen->profesorDeTipo(5);
$a_textos[41] = ucfirst(_("número de profesores ayudantes"));
//42. Número de total de profesores
$res[42] = $Resumen->profesorDeTipo(0);
$a_textos[42] = ucfirst(_("número de total de profesores"));
//43. Número de profesores de latín
$res[43] = $Resumen->profesorDeLatin();
$a_textos[43] = ucfirst(_("número de profesores de latín"));

/*44. Número de profesores que dieron clase de su especialidad*/
$res[44] = $Resumen->profesorEspecialidad();
$a_textos[44] = ucfirst(_("número de profesores que dieron clase de su especialidad"));

/*45. Número de profesores que dieron clase otras asignaturas*/
$res[45] = $Resumen->profesorEspecialidad(TRUE);
$a_textos[45] = ucfirst(_("número de profesores que dieron clase de otras asignaturas"));

/*46. Número de profesores asistentes a congresos...*/
$res[46] = $Resumen->profesorCongreso();
$a_textos[46] = ucfirst(_("número de profesores asistentes a cve del stgr u otras reuniones"));

/*47. Número de departamentos con director*/
$res[47] = $Resumen->Departamentos();
$a_textos[47] = ucfirst(_("nº de departamentos"));


// ---------------------------------- html ----------------------------------------------------
?>
<script>
	fnjs_left_side_hide();
</script>
<p><?= \core\strtoupper_dlb(_("profesores stgr")) ?>   <?= $curso_txt ?></p>
<table border=1>
<?php
foreach ($res as $n => $datos) {
	$pos = strpos($n, ".");
	if ($pos !== false) {
		$tab = "<td></td><td>$n. ";
	} else {
		$tab = "<th>$n. </th><td>";
	}
	?>
	<tr><?= $tab ?><?= $a_textos[$n] ?></td><td><?= $datos['num'] ?></td></tr>
	<?php if (!empty($datos['lista'])){ ?>
	   <tr><td colspan=3>
		<?= $datos['lista']; ?>
	   </td></tr>
	<?php }
}
?>
</table>
