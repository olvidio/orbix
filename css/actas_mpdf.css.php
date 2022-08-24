<?php 
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

?>
<style>

div.A4 {  
	display: block;
	margin-left:	0.8cm;
	top: -2cm;
	width: 98% ;
	height: 95%;
	border-width : 1pt;
	border-color : Black;
	border-style : solid;
	padding:	10pt;
	}
div.cabecera { 
	display: block;
	margin-top:	3pt;
	font-size: 22pt; 
	text-align : left;
	letter-spacing: -0.1em;
	/* por alguna razón no va bien con 0.3em pongo 10pt */
	word-spacing: 10pt;
	font-weight: bold;
	}
div.region { 
	display: block;
	margin-top: 5pt;
	font-weight: bold; 
	font-size: 18pt; 
	text-align : left;
	font-weight: normal;
	line-height: 24pt ;
	}
div.curso {
	display: block;
	margin-top: 5pt;
	font-weight: normal;
	font-size: 16pt; 
	text-align : left;
	line-height: 20pt ;
	}
div.intro {	
	display: block;
	margin-top: 10pt;
	text-align: justify;
	text-indent: 1cm;
	line-height: 16pt ;
	font-size: 12pt; 
	font-weight: normal;
	}
div.tribunal {	
	display: block;
	margin-top: 10pt;
	margin-left: 5cm;
	text-align: left;
	line-height: 12pt ;
	font-weight: normal;
	font-size: 12pt; 
	}
div.examinador {	
	display: block;
	margin-top: 5pt;
	margin-left: 7cm;
	text-align: left;
	line-height: 12pt ;
	font-weight: normal;
	font-size: 12pt; 
	}
div.sello {	
	display: block;
	margin-top: 1cm;
	margin-left: 2cm;
	width: 5cm;
	text-align: center;
	line-height: 12pt ;
	}
div.pie {	
	display: block;
	position: relative;
	margin-top:	-6mm;
	margin-left:	1.3cm;
	margin-right:	1cm;
	line-height: 12pt ;
	}
div.libro {	
	display: block;
	float: left;
	}
div.fecha {	
	display: block;
	float: right;
	margin-top:	1cm;
	text-align: right;
	}
div.acta {	
	display: block;
	position: relative;
	margin-top:	-6mm;
	float: right;
	text-align: right;
	margin-right: 0cm;
	}
div.f7 {	
	display: block;
	position: relative;
	margin-top:	3mm;
	margin-left:	1cm;
	text-align: left;
	font-size: 6pt; 
	}
table.alumni {  
	margin-top: 1cm;
	width: 99% ;
	height: 12pt;
	border: 0pt;
	font-family: 'Times new Roman';
	font-size: 12pt; 
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
	line-height: 12pt ;
	}
td.linea {
	text-decoration : none;
	text-align: center;
	}

</style> 
