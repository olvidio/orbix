<!--
/* Propiamente no es un página de estilo.
Debe estar con un include al principio de la página que llame a la función calendario.

Se definen unas variables en php:
	$colorColumnaUno
	$colorColumnaDos
	$table_border

Se define un estilo, para la tabla.
 th.cap		- la primera celda (cabecera)
 th.mes		- las celdas que pone el nombre del mes
 th.lletra	- las celdas que pone el nombre del día
 th.num		- las celdas que pone el número del día
 td.nom		- las celdas que pone el nombre de la persona o casa (columna izquierda)
 td.act		- las celdas que pone el nombre de la actividad
 td.nada	- las celdas que están vacías. (contienen un espacio)
*/

bla,bla...
El atributo FRAME controla las líneas perimetrales de la tabla y puede recibir los siguientes valores: 

VOID - ABOVE - BELOW - HSIDES - LHS - RHS - VSIDES - BOX - BORDER 

VOID: Ninguna línea. Este es el valor por omisión (default).
ABOVE: La línea superior únicamente.
BELOW: La línea inferior únicamente.
LHS: La línea izquierda únicamente.
RHS: La línea derecha únicamente.
HSIDES: Las líneas superior e inferior únicamente.
VSIDES: Las líneas derecha e izquierda únicamente.
BOX: Las cuatro líneas.
BORDER: Las cuatro líneas.

El atributo RULES controla las líneas que aparecen entre celdas. Sus valores son: 
NONE - GROUPS - ROWS - COLS - ALL 

NONE: Ninguna línea. Este es el valor por omisión.
GROUPS: Las líneas entre grupos de líneas y grupos de columnas.
ROWS: Serán visibles las líneas que separan líneas (elemento TR).
COLS: Serán visibles las líneas que separan columnas (elemento TD).
ALL: Todas las líneas. 
El atributo BORDER indica en pixels el ancho del reborde de las tablas. BORDER = 3 dibujará un reborde de tres pixels de ancho. 
-->
<!-- PAGINA DE ESTILO: <?php echo __FILE__; ?> -->
<style>
	table  {
     	border : solid;
     	border-color : red;
     }
     
     th.cap  {
     	font-size : 26pt;
     	width : 10cm;
     }
     
     th.mes  {
     	font-size : 15pt;
     	border-style : double;
     	border-color : Maroon;
     	border-width : medium;
     }
     
 	th.diumenge, th.diumengenum  {
     	background-color : #EE5522;
     }
	 
    th.lletra  {
     	font-size : 9pt;
     	border-style : solid;
     	border-color : Maroon;
     	border-width : 1px;
     	border-bottom-width : 0px;
     }
     
     th.num  {
     	font-size : 9pt;
     	border-style : solid;
     	border-color : Maroon;
     	border-width : 1px;
     	border-top-width : 0px;
     }
     
     tr  {
     	height : 12pt;
     }
    td {
     	font-size : 8pt;
     	text-align : center;
     	text-decoration : none;
     	color : black;
     	font-family : arial, helvetica, sans-serif;
     	border : solid;
	}
     td.nom  {
     	font-size : 9pt;
     	font-weight : bold;
     	border-bottom-width : thin;
     	border-color : Navy;
     	background-color : #CCCCCC;
     }
     
     td.actsv  {
     	border-color : navy;
     	border-width : 1px;
     	background-color : #C0F0F0;
     }
     td.actsv_nomod  {
     	border-color : navy;
     	border-width : 1px;
     	background-color : #EEFFFF;
     }
     
	 td.actpropio  {
     	border-color : green;
     	border-width : 1px;
     	background-color : #33F033;
     }
	 td.actpersonal  {
     	border-color : yellow;
     	border-width : 1px;
     	background-color : #FFFF00;
     }

     td.actsf  {
     	border-color : red;
     	border-width : 1px;
     	background-color : #FF9999;
     }
     td.actsf_nomod  {
     	border-color : red;
     	border-width : 1px;
     	background-color : #FFEEEE;
     }
     
     td.actotras  {
     	border-color : lime;
     	border-width : 1px;
		background-color : #33FF99;
     }
     td.actotras_nomod  {
     	border-color : lime;
     	border-width : 1px;
		background-color : #33FF99;
     }

 	 td.diumenge1 {
	    border-style: solid;
		border-width: 0px 0px 1px 1px;
		border-bottom-color: #BBBBBB;
		border-left-color: #000000;
		}

	 td.diumenge2 {
	    border-style: solid;
		border-width: 0px 0px 1px 0px;
		border-color: #BBBBBB;
		}

     td.nada1  {
	    border-style: solid;
		border-width: 0px 0px 1px 1px;
		border-color: #BBBBBB;
     	font-size : 8pt;
     }
     
     td.nada2  {
	    border-style: solid;
		border-width: 0px 0px 1px 0px;
		border-color: #BBBBBB;
     	font-size : 8pt;
     }

     A.link  {
     	color : navy;
     	text-decoration : none;
     }
     
     /* link no visitado*/
     A.visited  {
     	color : navy;
     	text-decoration : none;
     }
     
     /* link visitado*/
     A.hover  {
     	color : white;
     	text-decoration : none;
     }
     
	 .link_red  {
		cursor: pointer;
     	color : black;
     	background-color : #FFFFAA;
     	text-decoration : none;
     }
     
	 
@media print {

DIV { page-break-after: always; }

     table  {
	    width: 27.0cm;
		table-layout:fixed;
     }
	 
     th {
	    height : 15pt;
	}

     th.cap  {               /* cabecera p. ej.:Planing de casas */
     	font-size : 16pt;
     	width : 10%;
     }
     
     th.mes  {              /* nombre del mes */
     	font-size : 12pt;
		vertical-align: middle;
		height:30pt;
     }
	 
    th.diumenge  {
		border-width: 1px;
		border-bottom-width: 0px;
		border-collapse: collapse;
     	border-style : solid;
		font-size : 7pt;
     } 
	 
    th.diumengenum  {
		border-width: 1px;
		border-top-width: 0px;
		border-collapse: collapse;
     	border-style: solid;
		font-size : 7pt;
     } 

     th.lletra  {
     	font-size : 7pt;
     	font-weight : normal;
     	border-width : 1px 0px 0px 1px;
     }

     th.num  {
     	font-size : 7pt;
     	font-weight : normal;
     	border-width : 0px 0px 1px 1px;
     }
     
     tr  {
     	height : 12pt;
     }
	 
     td.nom  {
     	font-size : 8pt;
     	border-bottom-width : 1px;
     	border-top-width : 1px;
		border-right-width : 0px;
		border-left-width : 0px;
		border-style : solid;
     }
     
     td.actsv  {
     	font-size : 7pt;
     	font-family : arial, helvetica, sans-serif;
     	border-width : 2px;
		border-color : navy;
     }
     
     td.propio  {
     	font-size : 7pt;
     	font-family : arial, helvetica, sans-serif;
     	border-width : 2px;
		border-color : green;
     }

     td.actsf  {
     	font-size : 7pt;
     	font-family : arial, helvetica, sans-serif;
     	border-width : 2px;
		border-color : red;
     }

     td.actotras  {
     	font-size : 7pt;
     	font-family : arial, helvetica, sans-serif;
     	border-width : 2px;
		border-color : lime;
     }

	 td.diumenge1 {
	    border-style: solid;
		border-width: 0px 0px 1px 1px;
		border-bottom-color: #BBBBBB;
		border-left-color: #000000;
		}

	 td.diumenge2 {
	    border-style: solid;
		border-width: 0px 0px 1px 0px;
		border-color: #BBBBBB;
		}

     td.nada1  {
	    border-style: solid;
		border-width: 0px 0px 1px 1px;
		border-bottom-color: #BBBBBB;
		border-left-style: dotted;
		border-left-color: #BBBBBB;
     	font-size : 8pt;
     }
     
     td.nada2  {
	    border-style: solid;
		border-width: 0px 0px 1px 0px;
		border-color: #BBBBBB;
     	font-size : 8pt;
     }
}

</style>
<?php
$colorColumnaUno				= "#EEEEEE";
$colorColumnaDos				= "#F6F6F6";
$table_border=" frame=below rules=groups CELLSPACING=0";
?>

