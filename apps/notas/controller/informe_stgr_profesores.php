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
	
/* Pongo en la variable $curs el periodo del curso */
$any=date("Y");
$mes=date("m");

if ($mes>9) {
	$any_ini=$any; 
	$any_fi=$any+1; 
} else { 
	$any_ini=$any-1;
	$any_fi=$any; 
}
$curso_txt="$any_ini-$any_fi";
$inicurs= date("d/m/Y", mktime(0,0,0,10,1,$any_ini)) ;
$fincurs= date("d/m/Y", mktime(0,0,0,9,30,$any_fi)) ;
$curs="BETWEEN '$inicurs' AND '$fincurs' ";

$lista = empty($_POST['lista'])? false : true;

/* tipos de profesor:
	1 Ordinario
	2 Extraordinario
	3 Adjunto
	4 Encargado
	5 Ayudante
	6 Asociado
*/
$Resumen = new notas\Resumen('profesores');
$Resumen->setLista($lista);
$Resumen->setAny($any_ini);
$Resumen->nuevaTablaProfe();

//32. nº de profesores ordinarios:
$res[32] = $Resumen->profesorDeTipo(1);
$a_textos[32] = _("Número de profesores ordinarios");
//33. Número de profesores extraordinarios
$res[33] = $Resumen->profesorDeTipo(2);
$a_textos[33] = _("Número de profesores extraordinarios");
//34. Número de profesores adjuntos
$res[34] = $Resumen->profesorDeTipo(3);
$a_textos[34] = _("Número de profesores adjuntos");
//35. Número de profesores encargados
$res[35] = $Resumen->profesorDeTipo(4);
$a_textos[35] = _("Número de profesores encargados");
//36. Número de profesores asociados
$res[36] = $Resumen->profesorDeTipo(6);
$a_textos[36] = _("Número de profesores asociados");
//37. Número de profesores ayudantes
$res[37] = $Resumen->profesorDeTipo(5);
$a_textos[37] = _("Número de profesores ayudantes");
//38. Número de total de profesores
$res[38] = $Resumen->profesorDeTipo(0);
$a_textos[38] = _("Número de total de profesores");
//39. Número de profesores de latín
$res[39] = $Resumen->profesorDeLatin();
$a_textos[39] = _("Número de profesores de latín");

/*40. Número de profesores que dieron clase de su especialidad*/
$res[40] = $Resumen->profesorEspecialidad();
$a_textos[40] = _("Número de profesores que dieron clase de su especialidad");

/*41. Número de profesores que dieron clase otras asignaturas*/
$res[41] = $Resumen->profesorEspecialidad(TRUE);
$a_textos[41] = _("Número de profesores que dieron clase de otras asignaturas");

/*42. Número de profesores asistentes a congresos...*/
$res[42] = $Resumen->profesorCongreso();
$a_textos[42] = _("Número de profesores asistentes a cve del stgr u otras reuniones");

/*43. Ratio alumno/profesor en bienio*/
$a_profB = $Resumen->profesoresEnBienio();
$profB = $a_profB['num'];
$ResumenN = new notas\Resumen('numerarios');
$ResumenN->setLista($lista);
$ResumenN->nuevaTabla();
$Resumen->setCe_lugar('bm');
$a_numB = $ResumenN->enBienio();
$numB = $a_numB['num'];
$ResumenA = new notas\Resumen('agregados');
$ResumenA->setLista($lista);
$ResumenA->nuevaTabla();
$a_agdB = $ResumenA->enBienio();
$agdB = $a_agdB['num'];
$ratioB=($numB+$agdB)/$profB;
$res[43]['num'] = round($ratioB,2);
$a_textos[43] = _("Ratio alumno/profesor en bienio");

/*44. Ratio alumno/profesor en cuadrienio*/
$a_profC = $Resumen->profesoresEnCuadrienio();
$profC =$a_profC['num'];
$a_numC = $ResumenN->enBienio();
$numC = $a_numC['num'];
$a_agdC = $ResumenA->enBienio();
$agdC = $a_agdC['num'];
$ratioC=($numC+$agdC)/$profC;
$res[44]['num'] = round($ratioC,2);
$a_textos[44] = _("Ratio alumno/profesor en cuadrienio");


/*45. Número de departamentos con director*/
$res[45] = $Resumen->Departamentos();
$a_textos[45] = _("Nº de departamentos");


// ---------------------------------- html ----------------------------------------------------
?>
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
