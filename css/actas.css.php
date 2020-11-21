<?php 
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once (core\ConfigGlobal::$dir_estilos.'/colores.php'); 
?>
<!-- PAGINA DE ESTILO: <?= __FILE__; ?> -->
<style>
 
@page { 
		size: auto;
	 	margin: 1cm;
	 	margin-bottom: 1.5cm;
	}
@media print {	
 .no_print {
		visibility : hidden;
		}
}

div.A4 {  
	display: block;
	margin-left:	0.8cm;
	top: -2cm;
	width: 90% ;
	height: 750pt;
	border-width : 1pt;
	border-color : Black;
	border-style : solid;
	padding:	10pt;
	}
cabecera { 
	display: block;
	margin-top:	3pt;
	font-size: 24pt; 
	text-align : left;
	letter-spacing: -0.1em;
	word-spacing: +0.2em;
	font-weight: bold;
	}
region { 
	display: block;
	margin-top: 5pt;
	font-weight: bold; 
	font-size: 18pt; 
	text-align : left;
	font-weight: normal;
	line-height: 24pt ;
	}
curso {
	display: block;
	margin-top: 5pt;
	font-weight: normal;
	font-size: 16pt; 
	text-align : left;
	line-height: 20pt ;
	}
intro {	
	display: block;
	margin-top: 24pt;
	text-align: justify;
	text-indent: 1cm;
	line-height: 18pt ;
	font-size: 14pt; 
	font-weight: normal;
	}
tribunal {	
	display: block;
	margin-top: 24pt;
	margin-left: 6cm;
	text-align: left;
	line-height: 14pt ;
	font-weight: normal;
	font-size: 14pt; 
	}
examinador {	
	display: block;
	margin-top: 5pt;
	margin-left: 8cm;
	text-align: left;
	line-height: 14pt ;
	font-weight: normal;
	font-size: 14pt; 
	}
sello {	
	display: block;
	margin-top: 1cm;
	margin-left: 2cm;
	width: 5cm;
	text-align: center;
	line-height: 14pt ;
	}
pie {	
	display: block;
	position: relative;
	margin-left:	1.3cm;
	margin-right:	1cm;
	top: -.6cm;
	line-height: 14pt ;
	}
libro {	
	display: block;
	float: left;
	}
fecha {	
	display: block;
	float: right;
	margin-top:	1cm;
	text-align: right;
	}
acta {	
	display: block;
	float: right;
	text-align: right;
	}
f7 {	
	display: block;
	position: relative;
	left: -6.6cm;
	text-align: left;
	font-size: 6pt; 
	}
table.alumni {  
	margin-top: 1cm;
	width: 99% ;
	height: 12pt;
	border: 0pt;
	font-family: 'Times new Roman';
	font-size: 14pt; 
}
td.alumni {
	text-decoration : none;
	text-align: center;
	border-bottom-color : Black;
	border-bottom-style : solid;
	border-bottom-width : thin;
	}
td.alumno {	
	text-align: left;
	text-indent: 0cm;
	line-height: 14pt ;
	}
td.nota {	
	text-align: left;
	text-indent: 0cm;
	line-height: 14pt ;
	}
td.linea {
	text-decoration : none;
	text-align: center;
	}

</style> 
