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
@page { size: 21cm 29.7cm; margin: 0cm }
@media print {
	div.A4 { 
         position:absolute;
         z-index:15;
         top:2%;
         left:2%;
         margin:10px 0 0 10px;
    }

	.no_print { visibility : hidden; }
   /* Uso la etiqueta <div> </div> para hacer un salto de página al imprimir */
	.salta_pag {page-break-after:always;}

	.vertical{
		writing-mode:sideways-lr;
		vertical-align: bottom;
		bottom: 0;
	}
	th.vertical2 {
		 vertical-align:bottom;
		 padding-bottom: 10px;
	}
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
	

	table.ca_posibles  {
		border-color: black;
		border-style: solid;
		border-width: thin;
		left : 0px;
		top : 0px;
	}
	th.ca_posibles  {
		color : black;
		font-weight : bold;
	}
	th.centrado  {
		color : black;
		font-weight : bold;
		border-color: black;
		border-style: solid;
		border-width: thin;
		vertical-align: bottom;
	}
	td.ca_posibles_nom  {
		text-align : left;
		border-bottom-style: solid;
		border-width: thin;
	}
	td.ca_posibles  {
		text-align : center;
		border-style: solid;
		border-width: thin;
	}
	th.nom,td.nom { width: 12em; }
	th  {
		font-family : Arial;
		font-size : 10pt;
		color : black;
		font-weight : bold;
		background : white;
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
	.derecha {
	   text-align : right;
	}
	.centro {
	   text-align : center;
	}
	.izquierda {
	   text-align : left;
	}
	tr.impar  {
 		background-color : White;
 	}
 
 	tr.par  {
 		background-color : White;
 	}
	/*	### e43 ### */
	table.calif {
		font-size : 12pt;
		border-style: solid;
		border-width: 3px 3px 3px 3px;

	}
	td.calif {
		font-size : 12pt;
		border-style: solid;
		border-width: 1px 1px 1px 1px;
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
	/* Help Tip in line */
	/*-------------------------
		Inline help tip
	--------------------------*/


	.help-tip{
		position: relative;
		display: inline-block;
		/*top: 18px;
		 right: 18px;
  		*/
		text-align: center;
		/* background-color: #BCDBEA; */
		background-color : <?php echo $fondo_claro; ?>;
		border-radius: 50%;
		width: 24px;
		height: 24px;
		font-size: 14px;
		line-height: 26px;
		cursor: default;
		z-index: 10;
	}

	.help-tip:before{
		content:'?';
		font-weight: bold;
		color:<?php echo $letras; ?>;
	}

	.help-tip:hover p{
		display:block;
		transform-origin: 100% 0%;

		-webkit-animation: fadeIn 0.3s ease-in-out;
		animation: fadeIn 0.3s ease-in-out;

	}

	.help-tip p{
		display: none;
		text-align: left;
		/* background-color: #1E2021; */
		background-color: <?= $fondo_oscuro ?>;
		padding: 20px;
		width: 300px;
		position: absolute;
		border-radius: 3px;
		box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
		right: -4px;
		color: #FFF;
		font-size: 13px;
		line-height: 1.4;
	}

	.help-tip p:before{
		position: absolute;
		content: '';
		width:0;
		height: 0;
		border:6px solid transparent;
		border-bottom-color:#1E2021;
		right:10px;
		top:-12px;
	}

	.help-tip p:after{
		width:100%;
		height:40px;
		content:'';
		position: absolute;
		top:-40px;
		left:0;
	}

	@-webkit-keyframes fadeIn {
		0% { 
			opacity:0; 
			transform: scale(0.6);
		}

		100% {
			opacity:100%;
			transform: scale(1);
		}
	}

	@keyframes fadeIn {
		0% { opacity:0; }
		100% { opacity:100%; }
	}

	/* tonos de color */
	.tono1 {
		background-color : <?php echo $tono1; ?> !important;
	}
	.tono2 {
		background-color : <?php echo $tono2; ?> !important;
	}
	.tono3 {
		background-color : <?php echo $tono3; ?> !important;
	}
	.tono4 {
		background-color : <?php echo $tono4; ?> !important;
	}
	.tono5 {
		background-color : <?php echo $tono5; ?> !important;
	}
	.tono6 {
		background-color : <?php echo $tono6; ?> !important;
	}
	.tono7 {
		background-color : <?php echo $tono7; ?> !important;
	}
	/* plazas */
	/* pedida */
	.plaza1 {
		background: #FFFFFF !important;
	}
	/* en espera */
	.plaza2 {
		background: #F792C0 !important;
	}
	/* denegada */
	.plaza3 {
		background: #0EB4D3 !important;
	}
	/* asignada */
	.plaza4 {
		background: #99F5A4 !important;
	}
	/* confirmada */
	.plaza5 {
		background: #9C9485 !important;
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

	.vertical{
		writing-mode:sideways-lr;
		vertical-align: bottom;
		bottom: 0;
	}
	th.vertical2 {
		 vertical-align:bottom;
		 padding-bottom: 10px;
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
	table.ca_posibles  {
		border-color: black;
		border-style: solid;
		border-width: thin;
		left : 0px;
		top : 0px;
		background : <?php echo $cru; ?>;
	}
	th.ca_posibles  {
		color : black;
		font-weight : bold;
		background : <?php echo $fondo_claro; ?>;
	}
	th.centrado  {
		color : black;
		background : <?php echo $fondo_claro; ?>;
		font-weight : bold;
		border-color: black;
		border-style: solid;
		border-width: thin;
		vertical-align: bottom;
	}
	td.ca_posibles_nom  {
		text-align : left;
		border-bottom-style: solid;
		border-width: thin;
	}
	td.ca_posibles  {
		text-align : center;
		border-style: solid;
		border-width: thin;
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
	/*	### e43 ### */
	table.calif {
		font-size : 12pt;
		border-style: solid;
		border-width: 3px 3px 3px 3px;

	}
	td.calif {
		font-size : 12pt;
		border-style: solid;
		border-width: 1px 1px 1px 1px;
	}
}
</style> 
