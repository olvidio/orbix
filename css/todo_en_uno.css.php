<?php
namespace core;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once (ConfigGlobal::$dir_estilos.'/colores.php'); 
?>
<style>
/*  ################ IMPRESORA #################### */
@media print {
	@page { size: 21cm 29.7cm; margin: 0cm }

	.no_print { visibility : hidden; }
   /* Uso la etiqueta <div> </div> para hacer un salto de página al imprimir */
	.salta_pag {page-break-after:always;}

	BODY {text-align : left;
			margin: 0pt;
			background: white; 
		}
	h2 {
		font-size : 14pt;
	}
	TABLE { border-color : Black; }
	TABLE.A4 { width: 680pt; 
				border-style : solid;
				border-color : Black;
			}
	
	td { color : black;
		font-size : 11pt;
		font-family : Arial;
		padding-left: 1em;
		padding-right: 1em;
		}

	td.line-top  {
		border-top-style: solid;
		border-top-width: thin;
		vertical-align: top;
	}
		
	 .titulo  {
		font-family : Arial;
		font-size : 14pt;
		text-align : left;
		font-weight : bold;
		color : black;
	 }
	 .titulo_inv  {
		font-family : Arial;
		font-size : 14pt;
		text-align : left;
		font-weight : bold;
		color : black;
	 }
	.etiqueta  {
      	font-family : Verdana;
		font-weight : bold;	
      	font-size : 10pt;
		}

	.contenido  {
      	font-family : Arial;
      	font-size : 9pt;
		}
	.contenido_especial  {
      	font-family : Arial;
      	font-size : 8pt;
		}
	tr.impar  {
 		background-color : White;
 	}
 
 	tr.par  {
 		background-color : White;
 	}

}

/*  ################      PANTALLA    #################### */
@media screen {
	.d_visible { visibility : visible; }
	.d_novisible { visibility : hidden; }

	p { margin-top: 0; margin-bottom:0; }
	h2 {
		font-size : 14pt;
	}
	/* logout */
	#logout {
		position:absolute;
		cursor: pointer;
		z-index: 22000;
		<?php if (ConfigGlobal::$ubicacion == 'int') { ?>
			top:5;
			right:9;
			color : <?php echo $letras_hover; ?>;
		<?php } else { ?>
			top:14;
			right:14;
			color : <?php echo $fondo_claro; ?>;
		<?php } ?>
	}
	/* ventanas pop up */
	div.sombra {
		background: #000000;
		position: absolute;
		left: 0; top: 0;
		width: 100%;
		height: 100%;
		z-index: 1001;
		opacity: .75; /* opacidad para Firefox */
	}
	div.ventana {
		background: #FFFFFF;
		position: absolute;
		overflow: auto;
		z-index:1002;
		padding: 25;
		left: 200;  top: 200; /* la posición de la ventana modal */
		width: 800; height: 300; /* el tamaño de la ventana modal */
		/* cualquier otra propeidad, colores, márgenes, fondo */
	}
	/* ## INCORPORACIONES ## */
	table.incorporaciones { width: 85em; }
	th.nom,td.nom { width: 12em; }
	th.ctr,td.ctr { width: 6em; }
	th.inc,td.inc { width: 1em; }
	th.fecha,td.fecha { width: 4em; }
	th.chk,td.chk { width: 1em; }
	th.prot,td.prot { width: 4em; }
	th.acc,td.acc { width: 4em; }

	/* dossiers */
	img.dossier { vertical-align: bottom; width: 1.5em; }

	input.btn_ok  {
		border-style:ridge;
		color:navy;
		font-weight:bold;
	 }

	body.otro  {
		background-color : <?php echo $fondo_claro; ?>;
	 }
	 /* link al ponerse encima*/
	td.link:hover  {
		text-decoration : none;
		color : <?php echo $letras_hover; ?>;
		cursor: pointer;
	}
	span.link:hover  {
		text-decoration : none;
		color : <?php echo $letras_hover; ?>;
		cursor: pointer;
	 }
	.link:hover  {
		text-decoration : none;
		color : <?php echo $letras_hover; ?>;
		cursor: pointer;
	 }
	.link  {
		text-decoration : none;
		color : <?php echo $letras_link; ?>;
		cursor: pointer;
	 }
	.link_inv  {
		text-decoration : none;
		color :  <?php echo $fondo_claro; ?>;
		cursor: pointer;
	}
	.tachado {
		text-decoration: line-through;
	}
	.derecha {
	   text-align : right;
	}
	.centro {
	   text-align : center;
	}
	.izquierda {
	   text-align : left;
	}
	.titulo  {
	   font-family : Arial;
	   font-size : 14pt;
	   text-align : left;
	   font-weight : bold;
	   color : <?php echo $fondo_oscuro; ?>;
	}
	.titulo_inv  {
	   font-family : Arial;
	   font-size : 14pt;
	   text-align : left;
	   font-weight : bold;
	   color : <?php echo $fondo_claro; ?>;
	}
	.subtitulo {
		font-family : Arial;
		font-size : 10pt;
		color: <?php echo $fondo_oscuro; ?>;
		font-weight : bold;
	}
	.etiqueta  {
		font-family : Arial;
		font-size : 10pt;
		color : <?php echo $letras;?>;
		text-align: left;
	}
	.alerta  {
		font-family : Arial;
		font-size : 10pt;
		background-color : red;
	}
	.gris  {
		font-family : Arial;
		font-size : 10pt;
		background-color : <?php echo $gris_claro; ?>;
	}
	.contenido  {
		font-family : Arial;
		font-size : 10pt;
		font-weight : bold;		
		color : <?php echo $letras;?>;
	}
	.fecha {
		font-family : Arial;
		font-size : 10pt;
		font-weight : bold;
		color : <?php echo $letras;?>;
	}
	.fecha_hora {
		font-family : Arial;
		font-size : 10pt;
		font-weight : bold;
		color : <?php echo $letras;?>;
	}
	.slick-cell  {
		font-family : Arial;
		font-size : 10pt;
		color : <?php echo $letras;?>;
		text-align: left;
	}
	/* ### menu ### */
	#menu li {
		font-size : 11pt;
	}
	A:hover  {
		text-decoration : none;
		color : <?php echo $letras_hover; ?>;
	 }
	A  {
		text-decoration : none;
		color : navy;
	 }
	A.cabecera  {
		text-decoration : none;
		color : <?php echo $fondo_claro; ?>;
	 }

	A.tab  {
		text-decoration : none;
		color : <?php echo $fondo_claro;?>;
	}
	table  {
		width : 900px;
		left : 0px;
		top : 0px;
		border-color : <?php echo $lineas; ?>;
		background : <?php echo $cru; ?>;
		empty-cells: show;
	 }
	table.semi  {
		width : 450px;
	}
	table.ficha {
		border-width : thin;
		border-style : groove;
	}
	table.botones  {
		left : 0px;
		top : 0px;
		border-color : <?php echo $lineas; ?>;
		background-color : <?php echo $lineas; ?>;
	 }
	table.calendario  {
		width : 80px;
		left : 0px;
		top : 0px;
		border-color : black;
		background : <?php echo $cru; ?>;
	}
	th.calendario  {
		color : black;
		font-weight : bold;
		background : <?php echo $fondo_claro; ?>;
	}
	th  {
		font-family : Arial;
		font-size : 10pt;
		color : <?php echo $fondo_claro; ?>;
		font-weight : bold;
		background : <?php echo $fondo_oscuro; ?>;
	}
	tr:hover  {
	   background-color : <?php echo $lineas; ?>;
	}
	tr.impar  {
	   background-color : <?php echo $fondo_uno; ?>;
	}
	tr.imp  {
	   background-color : <?php echo $fondo_uno; ?>;
	}
	tr.par  {
	   background-color : <?php echo $fondo_dos; ?>;
	} 
	tr.sf  {
	   background-color : <?php echo $fondo_tres; ?>;
	} 
	tr.botones  {
	   text-align : center;
	   background-color : <?php echo $fondo_uno; ?>;
	}
	tr.tab  {
	   height : 15;
	   font-weight : bold;
	   font-family : Arial;
	   font-size : 12pt;
	   background : <?php echo $fondo_oscuro;?>;
	}
	tr.tab td {
		color : <?php echo $fondo_claro; ?>;
		cursor: pointer;
	}
	tr.delgada {
		height: 1pt;
		line-height: 1pt;
	   	font-size : 1pt;
	}
	tr.delgada td {
		height: 1pt;
		line-height: 1pt;
	   	font-size : 1pt;
	}
	td  {
		font-family : Arial;
		font-size : 10pt;
		color : <?php echo $letras;	?>;
		height : 15;
		vertical-align: top;
	}
	td.line-top  {
		border-top-style: solid;
		border-top-width: thin;
	}
	td.botones  {
		font-family : Arial;
		font-size : 10pt;
		color : <?php echo $fondo_claro; ?>;
		background-color : <?php echo $fondo_oscuro;?>;
		height : 15;
	}
	td.inactivo  {
		font-family : Arial;
		font-size : 10pt;
		color : <?php echo $letras; ?>;
		height : 15;
		background : <?php echo $fondo_uno; ?>;
	}
	/* ## listas ## */
	div.lista {
		font-family : Arial;
		font-size : 9pt;
		color : <?php echo $letras;?>;
	}
	div.lista *.etiqueta {
		font-size : 12pt;
	}
	div.lista *.datos {
		font-size : 12pt;
		font-weight : bold;
	}
	table.lista {
		border-width : thin;
		border-style : solid;
		border-collapse : collapse;
	}
	td.lista {
		border-style : solid;
		border-width : thin;
	}
	img {
		border: 0;
	}

	div#ir_atras {
		display: none;
	}
	div.left-slide {
		display: none;
		line-height: 1;
		position: fixed;
		height: 286px;
		top: 200px;
		left: -3px;
		background-color: <?= $fondo_claro ?>;
		background-position: center;
		background-repeat: no-repeat no-repeat;
		width: 40px;
		height: 230px;
	}
	.handle {
		width: 0px;
		height: 0px;
		border-style: solid;
		border-width: 80px 35px 80px 0;
		border-color: transparent <?= $medio ?> transparent transparent;

		opacity: .75; /* opacidad para Firefox */
		display: inherit;
		text-indent: -99999px;
		outline: none;
		position: relative;
		top: 33px;
		right: -3px;
	}
}
</style> 
