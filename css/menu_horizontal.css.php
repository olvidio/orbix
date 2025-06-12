<style>

@media print {

    #menu, #submenu, #cargando {
        display: none;
    }

    #main {
        clear: both;
        height: auto;
        overflow: visible;
    }

}

@media screen, projection {

    #udm {
        margin-top: 0px;
        padding-top: 6px;
        padding-bottom: 6px;
        background-color: <?= $GLOBALS['medio'] ?>;
    }

    #submenu {
        margin-top: 0px;
        margin-left: -10px;
        padding-left: 0px;
        clear: both;
    }

    .udm ul a, .udm ul a.nohref {
        font-family: Arial;
        font-size: 10pt;
    }

    .udm a, .udm a.nohref {
        font-family: Arial;
        font-size: 12pt;
    }

    #main {
        clear: both;
        height: 78%;
        overflow-x: auto;
        overflow-y: auto;
        padding-bottom: 2em;
        padding-left: 2em;
        padding-right: 1em;
        padding-top: 1em;
    }

    #menu {
        font-family: Arial;
        font-size: 12pt;
        background-color: <?= $GLOBALS['fondo_claro'] ?>;
        border-style: none;
        height: auto;
        line-height: 2em;
        margin: -10px -10px 0;
    }

    #menu li:hover, #menu li:active, #menu li.selec {
        border-style: none;
        cursor: pointer;
        background-color: <?= $GLOBALS['medio'] ?>;
        color: <?= $GLOBALS['claro'] ?>;
    }

    #menu li {
        display: inline;
        background-color: <?= $GLOBALS['fondo_claro'] ?>;
        color: <?= $GLOBALS['oscuro'] ?>;
        font-family: verdana, serif;
        font-weight: bold;
        font-size: 12pt;
        padding-top: 0.2em;
        padding-right: 0.4em;
        padding-left: 0.4em;
        padding-bottom: 1em;

        border-color: <?= $GLOBALS['medio'] ?>;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;

        border-top-style: none;
        border-left-style: solid;
        border-left-width: 1px;
        border-right-style: none;

        margin-bottom: -4px;
    }

}

</style>