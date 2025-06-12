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
        float: left;
        margin-top: 0;
        padding-top: 6px;
        padding-bottom: 6px;
        background-color: <?= $GLOBALS['medio'] ?>;
    }

    #submenu {
        margin-top: 0;
        margin-left: -10px;
        padding-left: 0;
        clear: none;
    }

    .udm ul a, .udm ul a.nohref {
        font-family: Arial, serif;
        font-size: 10pt;
    }

    .udm a, .udm a.nohref {
        font-family: Arial, serif;
        font-size: 12pt;
    }

    #main {
        margin-left: 0;
        height: 75%;
        overflow-x: auto;
        overflow-y: auto;
        padding-bottom: 6em;
        padding-left: 1em;
        padding-right: 6em;
        padding-top: 1em;
    }

    #menu {
        font-family: Arial, sans-serif;
        font-size: 12pt;
        background-color: <?= $GLOBALS['claro'] ?>;
        border-style: none;
        border-bottom: 0.5em solid<?= $GLOBALS['medio'] ?>;
        height: auto;
        line-height: 2.5em;
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
        background-color: <?= $GLOBALS['claro'] ?>;
        color: <?= $GLOBALS['oscuro'] ?>;
        font-family: verdana, serif;
        font-weight: normal;
        padding-top: 0.2em;
        padding-right: 0.2em;
        padding-left: 0.2em;
        padding-bottom: 0.65em;
        border-style: solid;
        border-color: <?= $GLOBALS['medio'] ?>;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        border-width: 2px;
    }
}

</style>