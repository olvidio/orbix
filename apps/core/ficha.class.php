<?php
namespace core;
/**
* Esta página sólo contiene la función ficha. Por tanto debe incluirse en otra página.
*
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/
class Ficha {
/**
* Funciones más comunes de la aplicación
*/

/**
*	Esta funcion crea un formulario tipo ficha para los campos de una tabla.
*	Los parámetros que se pasan pueden ser arrays, de manera que cada tabla puede mostrar unos
*	campos distintos y en condiciones distintas.
*
*	$campos: - una lista de campos separada por comas
*			 - un '*' para todos los campos.
*
*	$condicion:  es la condicion Where con el Where incluido.
*
*	$presentacion:	Si no se pasa el parámetro de presentación, se crea una presentación estándar, con todos los campos
*	
*	$boton:  0 indica no poner botones
*			 1 indica poner boton "guardar"
*			 2 indica poner boton "eliminar" pero en fichas individuales
*			 3 indica poner boton "eliminar"
*			 4 indica poner boton "quitar direccion"
*			 5 indica poner boton "nuevo"
*
*
*	nreg:	-5 indica seleccionar todos los registros
*			-1 indica que debe ser un registro nuevo --> en blanco
*			0  indica seleccionar un registro empezando en el registro número 0
*			n  indica seleccionar un registro empezando en el registro número n
*
*	Funciones requeridas:
*		- datos_campo($oDB,$tabla,$camp,$que) incluida en "func_tablas.php"
*		- primaryKey($tabla) incluida en "func_tablas.php"
*/
function dibujar_ficha ($obj,$presentacion) {
//no sirve la de la página porque estoy dentro de una funcion: variables locales
// pruebo así
/* global $web,$idioma,$a_campos; */

$a_campos = $obj->getTot();
?>
<form action="programas/ficha_ver.php" method="POST" target="ficha" name="frm2" id="frm2">
<input type="hidden" id="onanar" name="onanar" value="">
<input type="hidden" id="b" name="b" value="">
	<?php
		// para el caso de nueva ficha cuando no existe ninguna
		if ($n==0){
			echo "<input type=\"Hidden\" name=\"apelo\" value=\"1\">";	
			//para el caso que haga falta (telecos) paso el id_ubi.
			echo "<input type=\"Hidden\" name=\"id_ubi\" value=\"${_POST['id_ubi']}\">";
		}

	//------------- Presentacion ----------------------------	
	//
	// Si existe presentacion la incluyo, y sino hago la genérica
	//

	if ($presentacion){
			include ($presentacion[$f]) ;
	} else {	
	//presentacion genérica -------------------------------------------------------------------
		//voy a recorrer todos los campos de $campos[$f], ver el tipo, 
		// la longitud y el orden en que se debe mostrar según la tabla aux_orden
		$i=0;
		//para el caso de las herencias, hay que quitar el '*'
		$tabla[$f] = str_replace('*','',$tabla[$f]);
		//cojo el primer campo.
		$camp=strtok($campos[$f],",");
		while ($camp) {
		  $sql_orden="select * from aux_help_campos where nom_tabla='$tabla[$f]' and campo='$camp' ";
		  $oDBSt_rta=$oDB->query($sql_orden);
		  
		  $llarg=datos_campo($oDB,$tabla[$f],$camp,"longitud");
		  //por si no encuentra la longitud teórica del campo:
		  ///if ($llarg==0) {$llarg=pg_field_prtlen($q,0,$camp);}
		  //para el caso de que no exista orden en la tabla: aux_orden:
		  if ($oDBSt_rta->rowCount()!=0){	  
			  $row=$oDBSt_rta->fetch(\PDO::FETCH_ASSOC,0);
			  $orden=$row['orden'];
			  //para que al ordenar: 3 sea menor que 17:
			  if (strlen($orden)==1) {$orden="0".$orden;}
			  $etiqueta=$row['etiqueta'];
			  //si no tiene etiqueta pongo el nombre del campo
			  if (empty($etiqueta)) {$etiqueta=$camp;}
		  } else { $etiqueta=$camp;}
		  $valor=$a_row[$r][$camp];
		  if ($nuevoreg==-1) { $valor=""; }
		  $ordre[$i]="$grupo#$orden#$valor#$etiqueta#$llarg#$camp";
		  $i++;
		  $camp=strtok(",");
		}
		
		sort($ordre);
		
		$nc=$i;
		$ncar=0;
		$nlin=0;
		$grupoanterior=0;
		for ($i=0; $i<$nc; $i++) {
		  $grupo=strtok($ordre[$i],"#");
		  $orden=strtok("#");
		  //si tiene orden = 0 no lo muestro.
		  if ($orden=="0") { continue;}
		  $valor=strtok("#");
		  if ($nuevoreg==1) {$valor="";} //para mostrar todas las casillas en blanco en el caso de nuevo registro
		  $etiqueta=strtok("#");
		  $llarg=strtok("#");
		  $camp=strtok("#");
		  if ($llarg==0) { $llarg=2; }
		  //miro si me cabe en esta linea, o tengo que saltar a la siguiente:
		  if ($ncar>50) { $ncar=0; echo "<br>"; $nlin++; }
		  echo "$etiqueta <input name='$camp"."_"."$tabla[$f]$r' value=\"$valor\" size='$llarg'>&nbsp;";
		  $ncar=2+$ncar+strlen($etiqueta)+$llarg;
		} 
		//for ($nl=$nlin; $nl<20; $nl++) {echo "<br>"; } //relleno para el último grupo
		echo "</form>";
	}
	//
	// --------------------fin de Presentación
	//

	// -----------------------------------  Botones  ----------------------
	if ($boton[$f]){
            echo "<table><tr class=botones>";
            $b=strtok($boton[$f],",");
            while ($b) {
		if ($b==1){
                    $txt_g=ucwords(_("guardar cambios"));
                    echo "<th class='link_inv' onclick='fnjs_guardar($f,\"$go_toG[$f]\")'>$txt_g</th>"; }
		if ($b==2){
                    $txt_e=ucwords(_("eliminar"));
                    echo "<th class='link_inv' onclick='fnjs_eliminar($f,$r,\"$go_toE[$f]\")'>$txt_e</th>"; }
		if ($b==3){
                    $txt_e=ucwords(_("eliminar"));
                    echo "<th class='link_inv' onclick='fnjs_eliminar($f,\"t\",\"$go_toE[$f]\")'>$txt_e</th>"; }
		if ($b==4){
                    $txt_q=ucwords(_("quitar dirección"));
                    echo "<th class='link_inv' onclick='fnjs_quitar_dir($f,\"$go_toQ[$f]\")'>$txt_q</th>"; }
                    $b=strtok(",");
		} 
		echo "</tr></table><br>";
        } // fin ----------------------------  Botones  ----------------------
    echo '</form>';
} // ------------------- fin de funcion ficha ----------------------
}