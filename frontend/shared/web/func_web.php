<?php

namespace frontend\shared\web;

/**
 * Funciones auxiliares para dibujar campos en formularios.
 *
 * Este fichero contiene sólo funciones sueltas. Cargarlo con `require_once`.
 */

/**
 * Dibuja una etiqueta + un input/checkbox para una celda de una tabla.
 *
 * @param object $obj       Objeto del que se obtienen los datos del campo.
 * @param string $atributo  Nombre del atributo (p.ej. "nom").
 * @param int    $size      Anchura del input de texto.
 * @param int    $span1     Colspan de la celda de la etiqueta. Si es 0, no se pinta la etiqueta <td>.
 * @param int    $span2     Colspan de la celda del contenido. Si es 0, etiqueta y valor comparten la misma celda.
 */
function dibujar_campo($obj, string $atributo, int $size, int $span1, int $span2): string
{
    $get = ucfirst($atributo);
    $getdatos = 'getDatos' . ucfirst($atributo);

    $class = '';
    $dibujo = '';
    $valor = $obj->$get;
    $oDatosCampo = $obj->$getdatos();
    $etiqueta = $oDatosCampo->getEtiqueta();
    if ($oDatosCampo->getTipo() === 'fecha') {
        $class = 'fecha';
        $valor = $valor->getFromLocal();
    }

    $valor = empty($valor) ? '' : htmlspecialchars($valor ?? '');

    $help = '';
    $help_ref = '';
    $name = $atributo;

    if (!empty($span1)) $dibujo .= "<td colspan='$span1'>";
    $dibujo .= "<span class=\"etiqueta\" ondblclick=\"fnjs_help('$help_ref')\" >" . ucfirst($etiqueta) . "</span>";
    if (!empty($span2)) {
        $dibujo .= "</td><td colspan='$span2'>";
    } else {
        $dibujo .= "&nbsp";
    }
    if ($oDatosCampo->getTipo() === 'check') {
        $chk = ($valor) ? 'checked' : '';
        $dibujo .= "<input class=\"$class contenido\" id=\"$name\" name=\"$name\" type=checkbox $chk >";
    } else {
        $dibujo .= "<input class=\"$class contenido\" size=$size id=\"$name\" name=\"$name\" value=\"$valor\" title=\"$help\">";
    }
    if (!empty($span1)) $dibujo .= "</td>";

    return $dibujo;
}
