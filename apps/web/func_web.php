<?php

namespace web;

use core;

/**
 * Esta página sólo contiene funciones. Es para incluir en otras.
 *
 *
 * @package    delegacion
 * @subpackage    fichas
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

/**
 * Función para dibujar el campo de una tabla. La etiqeta más el contenido.
 *
 * Se pone en las celdas de una tabla.
 *    $texto = nombre del campo
 *    $size = tamaño del cuadro input donde va el valor del campo
 *    $span1 = valor span de la celda de la etiqueta. Si span1=0, no se ponen las etiquetas <td>.
 *    $span2 = valor span de la celda del contenido. Si span2=0, la etiqueta y el valor en la misma celda.
 */
function dibujar_campo($obj, $atributo, $size, $span1, $span2)
{
    $get = ucfirst($atributo);
    $getdatos = 'getDatos' . ucfirst($atributo);

    $class = '';
    $dibujo = "";
    $valor = $obj->$get;
    $oDatosCampo = $obj->$getdatos();
    $etiqueta = $oDatosCampo->getEtiqueta();
    // si el campo es fecha, añado la clase=fecha
    if ($oDatosCampo->getTipo() == 'fecha') {
        $class = 'fecha';
        $valor = $valor->getFromLocal();
    }

    $valor = htmlspecialchars($valor);
    /*
    $help=$a_valores_campo["help"];
    $help_ref=$a_valores_campo["help_ref"];
    */
    $help = '';
    $help_ref = '';

    //$name=$texto."_".$a_valores_campo["name"];
    $name = $atributo;

    if (!empty($span1)) $dibujo .= "<td colspan='$span1'>";
    $dibujo .= "<span class=\"etiqueta\" ondblclick=\"fnjs_help('$help_ref')\" >" . ucfirst($etiqueta) . "</span>";
    if (!empty($span2)) {
        $dibujo .= "</td><td colspan='$span2'>";
    } else {
        $dibujo .= "&nbsp";
    }
    if ($oDatosCampo->getTipo() == 'check') {
        $chk = ($valor) ? 'checked' : '';
        $dibujo .= "<input class=\"$class contenido\" id=\"$name\" name=\"$name\" type=checkbox $chk >";
    } else {
        $dibujo .= "<input class=\"$class contenido\" size=$size id=\"$name\" name=\"$name\" value=\"$valor\" title=\"$help\">";
    }
    if (!empty($span1)) $dibujo .= "</td>";

    return $dibujo;
}
