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

<!-- PAGINA DE ESTILO: <?= __FILE__; ?> -->

<style>

     

     .cap  {

     	font-size : 26pt;

     	width : 10cm;

     }

     

     .mes  {

     	font-size : 15pt;

     	border-style : double;

     	border-color : Maroon;

     	border-width : medium;

     }

     

 	.diumenge, .diumengenum  {

     	background-color : #EE5522;

     }

	 

    .lletra  {

     	font-size : 9pt;

     	border-style : solid;

     	border-color : Maroon;

     	border-width : 1px;

     	border-bottom-width : 0px;

     }

     

     .num  {

     	font-size : 9pt;

     	border-style : solid;

     	border-color : Maroon;

     	border-width : 1px;

     	border-top-width : 0px;

     }

     

     .nom  {

     	font-size : 9pt;

     	font-weight : bold;

     	border-bottom-width : thin;

     	border-color : Navy;

     	background-color : #CCCCCC;

     }

     

     .actsv  {

     	border-color : navy;

     	border-width : 1px;

     	background-color : #C0F0F0;

     	font-size : 8pt;

     }

     .actsv_nomod  {

     	border-color : navy;

     	border-width : 1px;

     	background-color : #EEFFFF;

     	font-size : 8pt;

     }

     

	 .actpropio  {

     	border-color : green;

     	border-width : 1px;

     	background-color : #33F033;

     	font-size : 8pt;

     }

	 .actpropio_nomod  {

     	border-color : green;

     	border-width : 1px;

     	background-color : #33F033;

     	font-size : 8pt;

     }

	 .actpersonal  {

     	border-color : yellow;

     	border-width : 1px;

     	background-color : #FFFF00;

     	font-size : 8pt;

     }



     .actsf  {

     	border-color : red;

     	border-width : 1px;

     	background-color : #FF9999;

     	font-size : 8pt;

     }

     .actsf_nomod  {

     	border-color : red;

     	border-width : 1px;

     	background-color : #FFEEEE;

     	font-size : 8pt;

     }

     

     .actotras  {

     	border-color : lime;

     	border-width : 1px;

		background-color : #33FF99;

     	font-size : 8pt;

     }

     .actotras_nomod  {

     	border-color : lime;

     	border-width : 1px;

		background-color : #33FF99;

     	font-size : 8pt;

     }



 	 .diumenge1 {

	    border-style: solid;

		border-width: 0px 0px 1px 1px;

		border-bottom-color: #BBBBBB;

		border-left-color: #000000;

		}



	 .diumenge2 {

	    border-style: solid;

		border-width: 0px 0px 1px 0px;

		border-color: #BBBBBB;

		}



     .nada1  {

	    border-style: solid;

		border-width: 0px 0px 1px 1px;

		border-color: #BBBBBB;

     	font-size : 8pt;

     }

     

     .nada2  {

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

</style>

<?php

$colorColumnaUno				= "#EEEEEE";

$colorColumnaDos				= "#F6F6F6";

$table_border=" frame=below rules=groups CELLSPACING=0";

?>



