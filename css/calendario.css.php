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
-->
<style>
	table  {
     	border-collapse : collapse;
     	border : 1px solid var(--planning-border);
    }

    th.cap  {
     	font-size : 26pt;
     	width : 10cm;
     	background-color : var(--planning-nom-bg);
     	color : var(--planning-cap-text);
     	border : 1px solid var(--planning-nom-border);
    }

    th.mes  {
     	font-size : 15pt;
     	border : 1px solid var(--planning-nom-border);
     	background-color : var(--planning-mes-bg);
     	color : var(--planning-mes-text);
    }

 	th.diumenge, th.diumengenum  {
     	background-color: var(--planning-diumenge-bg);
     	color: var(--planning-diumenge-text);
     	font-weight: bold;
    }

    th.lletra  {
     	font-size: 9pt;
     	border: 1px solid var(--planning-border-light);
     	border-bottom-width: 0px;
    }

    th.num  {
     	font-size : 9pt;
     	border: 1px solid var(--planning-border-light);
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
     	border : 1px solid var(--planning-border-light);
	}

    td.nom  {
     	font-size : 9pt;
     	font-weight : bold;
     	text-align : left;
     	vertical-align : middle;
     	padding : 2px 6px;
     	border : 1px solid var(--planning-nom-border);
     	background-color : var(--planning-nom-bg);
     	color : var(--planning-nom-text);
    }

    td.provisional  {
        text-decoration: line-through;
    }

td.proyecto  {
    background: repeating-linear-gradient( -45deg, #888, #888 1px, #eeffff 1px, #eeffff 5px );
}

td.proyectof  {
        background: repeating-linear-gradient( -45deg, #888, #888 1px, #ff9999 1px, #ff9999 5px );
    }

    td.proyecto span.texto  {
        background-color: #FFFFFF;
        padding-inline: 5px;
    }

	td.actpropio  {
     	border-style : solid;
     	border-color : green;
     	border-width : 1px;
     	background-color : #33F033;
    }

	td.actpropio_nomod  {
     	border-color : green;
     	border-width : 1px;
     	background-color : #33F033;
    }

	td.actpersonal  {
     	border-color : yellow;
     	border-width : 1px;
     	background-color : #FFFF00;
    }

	td.actpersonal_nomod  {
     	border-color : black;
     	border-width : 1px;
     	background-color : #FFFF00;
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

 	td.diumenge1,
	td.diumenge2 {
     	background-color: var(--planning-diumenge-cell-bg);
    }

 	td.diumenge1 {
	    border-style: solid;
		border-width: 0px 0px 1px 2px;
		border-bottom-color: var(--planning-border-light);
		border-left-color: var(--planning-diumenge-bg);
    }

	td.diumenge2 {
	    border-style: solid;
		border-width: 0px 0px 1px 0px;
		border-color: var(--planning-border-light);
    }

    td.nada1  {
	    border-style: solid;
		border-width: 0px 0px 1px 1px;
		border-color: var(--planning-border-light);
     	font-size : 8pt;
    }

    td.nada2  {
	    border-style: solid;
		border-width: 0px 0px 1px 0px;
		border-color: var(--planning-border-light);
     	font-size : 8pt;
    }

    /* Misma persona: separador fino en el grid; entre personas: linea suave (incl. columna nom) */
    tr.planning-fila-interna > td:not(.nom) {
     	border-bottom-width: 1px;
     	border-bottom-style: dotted;
     	border-bottom-color: var(--planning-border-fila-interna);
    }

    tbody.planning-persona > tr:first-child > td.nom,
    tr.planning-fila-persona-fin > td {
     	border-bottom-width: 1px;
     	border-bottom-style: solid;
     	border-bottom-color: var(--planning-persona-separator);
    }

    #exportar table tbody.planning-persona > tr:first-child > td.nom {
     	border-bottom: 1px solid var(--planning-persona-separator);
    }

    /* Anula estilos globales de la app (todo_en_uno) dentro del planning */
    #exportar table th {
     	background-color: var(--planning-th-bg);
     	color: var(--planning-th-text);
    }

    #exportar table th.cap,
    #exportar table td.nom {
     	background-color: var(--planning-nom-bg);
     	color: var(--planning-nom-text);
     	border-color: var(--planning-nom-border);
    }

    #exportar table th.cap {
     	color: var(--planning-cap-text);
    }

    #exportar table th.diumenge,
    #exportar table th.diumengenum {
     	background-color: var(--planning-diumenge-bg);
     	color: var(--planning-diumenge-text);
    }

    #exportar table th.mes {
     	background-color: var(--planning-mes-bg);
     	color: var(--planning-mes-text);
    }

    /* Contenedor con scroll propio: margen a la derecha y abajo para ver las barras */
    #exportar.planning-viewport {
     	overflow: auto;
     	box-sizing: border-box;
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
    /* div { page-break-after: always; } */
    table  {
        page-break-after: always;
        /* width: 27.0cm; */
        width: 100%;
		table-layout:fixed;
     }

    th {
	    height : 15pt;
	}

    th.cap  {     /* cabecera p. ej.:Planing de casas */
     	font-size : 14pt;
     	width : 0.10%;
    }

    th.mes  {     /* nombre del mes */
     	font-size : 12pt;
		vertical-align: middle;
		height:30pt;
    }

    th.diumenge,
    th.diumengenum  {
		border-width: 1pt;
		border-collapse: collapse;
     	border-style : solid;
		font-size : 7pt;
     	background-color: var(--planning-diumenge-bg);
     	color: var(--planning-diumenge-text);
     	font-weight: bold;
    }

    th.diumenge  {
		border-bottom-width: 0;
    }

    th.diumengenum  {
		border-top-width: 0;
    }

    td.diumenge1,
    td.diumenge2 {
     	background-color: var(--planning-diumenge-cell-bg);
    }

    th.lletra  {
     	font-size : 7pt;
     	font-weight : normal;
     	border-width : 1pt 0 0 1pt;
    }

    th.num  {
     	font-size : 6pt;
     	font-weight : normal;
     	border-width : 0 0 1pt 1pt;
    }

    tr  {
     	height : 12pt;
    }

    td.nom,
    th.cap  {
     	font-size : 8pt;
     	background-color : var(--planning-nom-bg);
     	color : var(--planning-nom-text);
     	border : 1pt solid var(--planning-nom-border);
    }

    th.cap  {
     	color : var(--planning-cap-text);
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
		border-width: 0 0 1pt 1pt;
		border-bottom-color: var(--planning-border-light);
		border-left-color: var(--planning-border-light);
	}

	td.diumenge2 {
	    border-style: solid;
		border-width: 0 0 1pt 0;
		border-color: var(--planning-border-light);
	}

    td.nada1  {
	    border-style: solid;
		border-width: 0 0 1pt 1pt;
		border-bottom-color: var(--planning-border-light);
		border-left-style: dotted;
		border-left-color: var(--planning-border-light);
     	font-size : 8pt;
    }

    td.nada2  {
	    border-style: solid;
		border-width: 0 0 1pt 0;
		border-color: #BBBBBB;
     	font-size : 8pt;
    }
}
</style>
