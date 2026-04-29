<?php

namespace core;


use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;

if (empty($estilo_color)) {
    // Sin global_object.inc: no reabrir sesión ni montar DI (rompe si ya se envió salida, p.ej. tras echo del controller).
    require_once __DIR__ . '/../src/shared/global_header.inc';
    require_once __DIR__ . '/colores_estilo_desde_sesion.php';
    [$estilo_color, $tipo_menu] = \css_colores_estilo_desde_sesion();
}

$gris_claro = "#EEEEEE";
switch ($estilo_color) {
    case  "azul":
        $border = 0;
        $fondo_menu = "#AAAAFF";

        $claro = "#FFFFFF";
        $medio = "#5482D4";
        $oscuro = "#325081";
        $muy_oscuro1 = "#021c46";
        $muy_oscuro2 = "#032766";

        $fondo_oscuro = "#000066";
        $fondo_claro = "beige";
        $letras = "black";
        $letras_link = "navy";
        $letras_hover = "#00CCFF";
        $lineas = "#CCCCCC";
        $cru = "#FFFCF2";
        $fondo_uno = "#CCCCCC";
        $fondo_dos = "#DDDDDD";
        $fondo_tres = "#EECCCC";

        $udm_flecha = "right-navblue.gif";
        $tono1 = "#ebf0fa";
        $tono2 = "#d6e1f5";
        $tono3 = "#c2d3f0";
        $tono4 = "#adc4eb";
        $tono5 = "#99b5e6";
        $tono6 = "#85a6e0";
        $tono7 = "#7097db";

        break;
    case "verde":
        $border = 0;
        $fondo_menu = "#AAFFAA";

        $claro = "#F8FBD0";
        $medio = "#699F62";
        $oscuro = "#363";
        $muy_oscuro1 = "#012803";
        $muy_oscuro2 = "#014d22";

        $fondo_oscuro = $oscuro;
        $fondo_claro = $claro;
        $letras = $oscuro;
        $letras_link = "navy";
        $letras_hover = "#00FF00";
        $lineas = "#CCCCCC";
        $cru = "#FFFCF2";
        $fondo_uno = "#A0F0A0";
        $fondo_dos = "#AFFFAF";
        $fondo_tres = "#EECCCC";

        $udm_flecha = "right-navgreen.gif";
        $tono1 = "#f0f5ef";
        $tono2 = "#e1ecdf";
        $tono3 = "#d2e2d0";
        $tono4 = "#c3d8c0";
        $tono5 = "#b4cfb0";
        $tono6 = "#a4c5a0";
        $tono7 = "#95bb90";

        break;
    case "naranja":
        $border = 0;
        $fondo_menu = "#FFAAAA";

        $claro = "white";
        $medio = "#FF6600";
        $oscuro = "#C73800";
        $muy_oscuro1 = "#461202";
        $muy_oscuro2 = "#661a03";

        $fondo_oscuro = "#FF6600";
        $fondo_claro = "oldlace";
        $letras = "black";
        $letras_link = "navy";
        $letras_hover = "#FF6600";
        $lineas = "#CCCCCC";
        $cru = "#FFFCF2";
        $fondo_uno = "#FFCC99";
        $fondo_dos = "#FFDDAA";
        $fondo_tres = "#EECCCC";

        $udm_flecha = "right-navorange.gif";
        $tono1 = "#fff0e6";
        $tono2 = "#ffe0cc";
        $tono3 = "#ffd1b3";
        $tono4 = "#ffc299";
        $tono5 = "#ffb380";
        $tono6 = "#ffa366";
        $tono7 = "#ff944d";

        break;
}

// Exterior
if (ServerConf::SERVIDOR === 'orbix.moneders.net') {
    $fondo_claro = "aliceblue";
    if (ConfigGlobal::$web_path === '/pruebas' || ConfigGlobal::$web_path === '/pruebassf') {
        $fondo_claro = "#fadfd5;";
    }
} else { // Tunel
    if (ConfigGlobal::$web_path === '/pruebas' || ConfigGlobal::$web_path === '/pruebassf') {
        $fondo_claro = "aquamarine";
    }
}