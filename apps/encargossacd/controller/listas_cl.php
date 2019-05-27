<?php
/* OJO es una copia de lista_cr en /sm-agd/cargos/
* Es para sólo los ctrs de la sss+
* Se ha cambiado la fecha a 'aamm' y sin punto.
* Se añade si es agd o s de la sss+
*/
// INICIO Cabecera global de URL de controlador *********************************
	use encargossacd\model\EncargoFunciones;
use web\DateTimeLocal;
use core\ConfigGlobal;

require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once ("func_tareas.php");


$any=date("Y");

$cabecera_left = sprintf(_("Curso %s"),$curso);
$cabecera_right = ConfigGlobal::mi_delef();
$cabecera_right_2 = _("ref. cr 1/14, 10,c)");

// ciudad de la dl
$oEncargoFunciones = new EncargoFunciones();
$poblacion = $oEncargoFunciones->getLugar_dl();
$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.');
$lugar_fecha= "$poblacion, $hoy_local";

$sql = "SELECT u.nombre_ubi, t.cargo, to_char(t.f_ult_nombramiento,'yymm') AS fecha, t.orden_cargo, t.id_nom,
		t.elencum, t.f_cese, t.nombrado_por, t.f_ult_nombramiento, t.renovaciones
		FROM u_centros_dl u, d_cargos t
		WHERE t.id_ubi=u.id_ubi AND t.f_cese ISNULL AND u.tipo_ctr='ss'
		ORDER BY t.elencum, t.orden_cargo, t.cargo ";

$oDBSt_prop=$oDB->query($sql);
 
$Centre="res";
/* Para sacar una lista  */
?>
<html>
<head>
<!-- FICHERO: <?php echo __FILE__; ?> -->
	<title>Listado de cl para cr</title>
<style>
	div.salta_pag { page-break-after:always;}
	div.centro { page-break-inside: avoid;}
	.suplente { text-decoration : underline; } 
	table { width: 680; }
	td.derecha { text-align: right; }
	td.grupo { text-align: left;
	    font-weight : bold;
		text-decoration : underline; }
	td.centro { font-weight : bold; }
	td.suplente { text-decoration : underline; } 
</style>
</head>
<body>
<table><tr><td class=izquierda><?= $cabecera_left ?></td><td class=derecha><?= $cabecera_right ?></td></tr>
<tr><td></td><td class=derecha><?= $cabecera_right_2 ?></td></tr>
</table>
<table>
<?php
$i=0;
foreach ($oDBSt_prop->fetchAll() as $cargos) {
	$i++;
	
	$nombre_ubi=$cargos["nombre_ubi"];
	$cargo=$cargos["cargo"];
	$data=$cargos["fecha"];
	$id_nom=$cargos["id_nom"];
	$elencum=$cargos["elencum"];
	$nombrado_por=$cargos["nombrado_por"];
	$f_ult_nombramiento=$cargos["f_ult_nombramiento"];
	$renovaciones=$cargos["renovaciones"];
	
	$sql_nom= "SELECT p.id_nom, " . ap_nom_cr_1_05() ." as nom, p.id_tabla, s.socio 
			FROM personas p LEFT JOIN p_sssc s USING (id_nom)
			WHERE p.id_nom='$id_nom' ";
	$oDBSt_pdbgent=$oDB->query($sql_nom);
	$noms_p=$oDBSt_pdbgent->fetch(PDO::FETCH_ASSOC);
	$n_agd=$noms_p["id_tabla"];
	switch ($n_agd) {
		case "a":
			$agd=" (agd)";
			break;
		case "sss":
			$socio=$noms_p["socio"];
			$agd=" ($socio sss+)";
			break;	
		default:
			$agd="";
	}
	$cognom=$noms_p["nom"].$agd;
	
	//en vez de subrayar, pongo en negrita, porque al pasar a texto (rtf) se pierde, y al imprimir se ve
	// los nuevos nombramientos por la dl (no cr)
	$ini=date("U", mktime(0,0,0,11,1,$any-1));	// nº de segundos desde 'epoch' hasta el 1/11/AÑO-PASADO
	$fi=date("U", mktime(0,0,0,10,31,$any));		// nº de segundos desde 'epoch' hasta el 31/10/AÑO-ACTUAL
	list($d,$m,$a) = preg_split('/[\.\/-]/', $f_ult_nombramiento );	//separo la fecha en dia, mes, año
	$nombr=date("U", mktime(0,0,0,$m,$d,$a));	// nº de segundos desde 'epoch' hasta el nombramiento
	if ($nombrado_por=="d" AND ($nombr>$ini AND $nombr<$fi)) { //antes excluia las renovaciones:  AND $renovaciones==""
		$flag_s=1;
	} else {$flag_s=0; }
	
	if ($Centre != $cargos["nombre_ubi"]){
		?>
		<tr><td><br></td></tr>
		<tr style="font-weight : bold;"><td height="30" colspan="5" valign="TOP"><?php echo "$nombre_ubi";?></td><td></td><td align="LEFT"><?php echo "$elencum";?></td></tr>
	<?php } ?>
	<tr><td width='40' nowrap><?php echo "$cargo";?></td><td></td><!-- Añado una celda para que al pasarlo a sólo texto haya 2 espacios según cr 16/90,6 -->
		<td width='50' nowrap><?php
			 if ($flag_s==1){
			 		echo "<b>$data</b>";
			 }
			 else {echo "$data";}  ?></td><td></td>
	    <td colspan=2 width='250'><?php echo "$cognom";?></td>
	</tr>
	<?php
	if ($cargos["nombre_ubi"]){$Centre = $cargos["nombre_ubi"];}
}
?>
</table>
<table><tr><td class=izquierda></td><td class=derecha><?= $lugar_fecha ?></td></tr>
</table>

