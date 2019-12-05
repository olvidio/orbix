<?php 
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once (core\ConfigGlobal::$dir_estilos.'/colores.php'); 
?>
<style>
@media print{
    .no_print { display :none; }
}

#otro, #main { 
    width: auto; 
    height: auto;
    clear:both;
    overflow:auto;
    padding-bottom:0em;
    padding-left:0em;
    padding-right:0em;
    padding-top:0em;
}

div.A4 {  
	display: block;
	margin-top:	0.8cm;
	/* margin-left:	0.8cm; */
	top: 0cm;
	width: 90% ;
	height: 780pt;
	border-width : 1pt;
	border-color : Black;
	border-style : solid;
	padding:	10pt;
	}
	
td {
	color : black;
	font-size : 8pt;
	font-family : serif;
	text-decoration : none;
	height : 8pt;
	vertical-align: middle;
}
td.space {
	height : 2pt;
}
td.space_doble {
	height : 8pt;
}
td.dato {
	color : black;
	font-size : 8pt;
	font-family : arial;
	text-decoration : none;
	text-align : center;
	
	border-color : Gray;
	border-style : solid;
	border-left-width : 0pt;
	border-right-width : 0pt;
	border-top-width : 0pt;
	border-bottom-width : 1pt;
}
TD.curso { color : black;
	font-size : 10pt;
	font-family : serif;
	text-align : center;
	font-weight : bold;
	}
TD.cabecera { color : black;
	font-size : 8pt;
	font-family : serif;
	text-align : center;
	font-weight : bold;
	}
TD.any { color : black;
	font-size : 8pt;
	font-family : serif;
	text-align : left;
	font-weight : bold;
	}
TD.titulo1 { font-family : serif;
	font-size : 18pt;
	text-align : center;
	font-weight : bold;
	}
TD.titulo2 { font-family : serif;
	font-size : 16pt;
	text-align : center;
	}
TD.subtitulo1 { font-family : serif;
	font-size : 14pt;
	text-align : center;
	font-weight : bold;
	padding: 20px;
	}
TD.subtitulo2 { font-family : serif;
	font-size : 10pt;
	text-align: justify;
	}
	
tr.impar  {
 	background-color : White;
	line-height:  2pt;
 	}
 
tr.par  {
    background-color : White;
	line-height: 2pt;
 	}
tr.opcional {
    background-color : White;
	line-height: 8pt;
 	}
 
pie {	
    height: 8cm;
	display: block;
	position: relative;
	margin-left:	1.3cm;
	margin-right:	1cm;
	line-height: 14pt ;
	}
fecha {	
	display: block;
	float: right;
	margin-top:	0.5cm;
	text-align: right;
	}
sello {	
	display: block;
	float: left;
	margin-top: 1cm;
	}
libro {	
	display: block;
	float: left;
	margin-top: 7cm;
    vertical-align: bottom;
    position: inherit;
    margin-left: -4cm;
	}
firma {	
	margin-top: 1.5cm;
	display: block;
	float: right;
	}
secretario {	
	margin-top: 5cm;
	display: block;
	float: right;
	}
ects {	
	font-size: 8pt; 
	display: block;
	float: left;
	text-align: left;
	}
piepagina{	
    display: block;
    font-size: 6pt;
    position: absolute;
	}
f7{	
	display: inline-block;
	text-align: left;
	}
dir{	
	display: inline-block;
	text-align: center;
    align-content: center;
    width: 25cm;
	}
</style> 
