<?php 
// INICIO Cabecera global de URL de controlador *********************************
	use core\ConfigGlobal;

    require_once ("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once (ConfigGlobal::$dir_estilos.'/colores.php'); 
?>
<style>
@media print {

    #otro, #main {
        width: auto;
        height: auto;
        clear: both;
        overflow: auto;
        padding-bottom: 0em;
        padding-left: 0em;
        padding-right: 0em;
        padding-top: 0em;
    }

    .no_print {
        display: none;
    }

    table.A4 {
        width: 98%;
        height: 98%;
        border-style: solid;
        border-width: 1pt;
        border-color: Black;
    }


    .border {
        border: 1px solid black;
    }

    .semi {
        width = 50%;
    }

    td {
        color: black;
        font-size: 9pt;
        font-family: serif;
        text-decoration: none;
        height: 9pt;
    }

    td.dato {
        color: black;
        font-size: 8pt;
        font-family: arial;
        text-decoration: none;
        text-align: center;

        border-color: Gray;
        border-style: solid;
        border-left-width: 0pt;
        border-right-width: 0pt;
        border-top-width: 0pt;
        border-bottom-width: 1pt;
    }

    td.curso {
        color: black;
        font-size: 12pt;
        font-family: serif;
        text-align: center;
        font-weight: bold;
    }

    td.cabecera {
        color: black;
        font-size: 10pt;
        font-family: serif;
        text-align: center;
        font-weight: bold;
    }

    td.any {
        color: black;
        font-size: 10pt;
        font-family: serif;
        text-align: left;
        font-weight: bold;
    }

    td.titulo {
        font-family: serif;
        font-size: 22pt
    }

    td.subtitulo {
        font-family: serif;
        font-size: 16pt
    }

    tr.impar {
        background-color: White
    }

    tr.par {
        background-color: White
    }
}

@media screen {

    #otro, #main {
        width: auto;
        height: auto;
        clear: both;
        overflow: auto;
        padding-bottom: 0em;
        padding-left: 0em;
        padding-right: 0em;
        padding-top: 0em;
    }

    table.A4 {
        width: 98%;
        height: 98%;
        border-style: solid;
        border-width: 1pt;
        border-color: Black;
    }

    .border {
        border: 1px solid black;
    }

    .semi {
        width = 50%;
    }

    td {
        color: black;
        font-size: 9pt;
        font-family: serif;
        text-decoration: none;
        height: 9pt;
    }

    td.dato {
        color: black;
        font-size: 8pt;
        font-family: arial;
        text-decoration: none;
        text-align: center;

        border-color: Gray;
        border-style: solid;
        border-left-width: 0pt;
        border-right-width: 0pt;
        border-top-width: 0pt;
        border-bottom-width: 1pt;
    }

    td.curso {
        color: black;
        font-size: 10pt;
        font-family: serif;
        text-align: center;
        font-weight: bold;
    }

    td.cabecera {
        color: black;
        font-size: 10pt;
        font-family: serif;
        text-align: center;
        font-weight: bold;
    }

    td.any {
        color: black;
        font-size: 10pt;
        font-family: serif;
        text-align: left;
        font-weight: bold;
    }

    td.titulo {
        font-family: serif;
        font-size: 22pt
    }

    td.subtitulo {
        font-family: serif;
        font-size: 16pt
    }

    tr.impar {
        background-color: White
    }

    tr.par {
        background-color: White
    }
}

</style>
