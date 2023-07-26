<?php 
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once (core\ConfigGlobal::$dir_estilos.'/colores.php'); 
?>
<style>
@media print {
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
    position: relative;
	display: block;
	margin-top:	0.8cm;
	/* margin-left:	0.8cm; */
	top: 0cm;
	width: 19cm ;
	height: 27cm;
	border-width : 1pt;
	border-color : Black;
	border-style : solid;
	padding-top: 10pt;
    padding-right: 10pt;
    padding-bottom: 14pt;
    padding-left: 10pt;
	}
	
table {
	width: 95%;
}

td {
	color : black;
	font-size : 8pt;
	font-family : serif;
	text-decoration : none;
	height : 9pt;
	vertical-align: middle;
	padding: 0pt;
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
}
td.curso { color : black;
	font-size : 10pt;
	font-family : serif;
	text-align : center;
	font-weight : bold;
	}
td.cabecera { color : black;
	font-family : serif;
	text-align : center;
	font-weight : bold;
	}
td.any { color : black;
	font-family : serif;
	text-align : left;
	font-weight : bold;
	}
td.titulo1 { font-family : serif;
	font-size : 18pt;
	text-align : center;
	font-weight : bold;
	}
td.titulo2 { font-family : serif;
	font-size : 16pt;
	text-align : center;
	}
td.subtitulo1 { font-family : serif;
	font-size : 14pt;
	text-align : center;
	font-weight : bold;
	padding: 20px;
	}
td.subtitulo2 { font-family : serif;
	font-size : 10pt;
	text-align: justify;
	}
	
tr.opcional {
    background-color : White;
	line-height: 10pt;
 	}
 
div.pie {	
    height: 6cm;
	display: block;
	position: absolute;
	bottom: 0pt;
	margin-left: 1cm;
	line-height: 14pt;
	width: 90%;
	}
div.fecha {	
	display: inline-block;
	margin-top:	0.5cm;
	text-align: right;
	font-size: 10pt;
    font-weight: normal;
    width: 100%;
	}
div.g_sello {	
    display: block;
    margin-left: 1cm;
    font-size: 10pt;
	position: relative;
	top: 0cm;
	width: 18cm;
	height: 1cm;
	}
div.sello {	
	display: inline-block;
	margin-top: 0cm;
	}
div.firma {	
	margin-top: 0.5cm;
	display: inline-block;
	margin-left: 6cm;
	}
	
div.g_libro {	
    display: block;
    margin-left: 0cm;
    font-size: 10pt;
	position: absolute;
	bottom: 1cm;
	width: 17cm ;
	}
div.libro {	
	display: inline-block;
	float: left;
    margin-left: 0cm;
    vertical-align: bottom;
    position: absolute;
	bottom: 0px;
    white-space: nowrap;
	}
div.secretario {	
	display: inline-block;
    position: absolute;
	bottom: 0px;
    margin-right: 1cm;
    text-align: right;
    width: 100%;
}
	
div.ects {	
	font-size: 8pt; 
	display: block;
	float: left;
	text-align: left;
	margin-left: 0.5cm;
	position: absolute;
	bottom: 0px;
	}
div.piepagina {	
    display: block;
    margin-left: 1cm;
    font-size: 6pt;
	position: relative;
	bottom: 0px;
	width: 18cm ;
	}
div.f7 {	
	display: inline-block;
	text-align: left;
	}
div.dir {	
	display: inline-block;
	text-align: center;
    align-content: center;
    width: 95%;
	}
</style> 
