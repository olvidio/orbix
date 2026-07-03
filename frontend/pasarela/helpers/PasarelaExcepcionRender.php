<?php

declare(strict_types=1);

namespace frontend\pasarela\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class PasarelaExcepcionRender
{
    /**
     * @param array{default: string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>} $data
     */
    public static function listaConDefaultHtml(array $data, string $defaultOnclick, string $rowOnclickFn): string
    {
        $default = htmlspecialchars($data['default'], ENT_QUOTES, 'UTF-8');
        $html = '<table>';
        $html .= '<tr><td>' . _('por defecto') . '</td><td>';
        $html .= '<span class="link" onclick="' . $defaultOnclick . '">' . $default . '</span></td></tr>';
        $html .= '</table><table>';
        foreach ($data['excepciones'] as $row) {
            $idTipoActiv = PayloadCoercion::int($row['id_tipo_activ']);
            $etiqueta = htmlspecialchars($row['etiqueta'], ENT_QUOTES, 'UTF-8');
            $valor = $row['valor'];
            $valorJs = htmlspecialchars(addslashes($valor), ENT_QUOTES, 'UTF-8');
            $valorHtml = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            $html .= "<tr><td>$etiqueta</td><td>";
            $html .= "<span class=\"link\" onclick=\"$rowOnclickFn($idTipoActiv,'$valorJs')\">$valorHtml</span></td></tr>";
        }
        $html .= '</table>';

        return $html;
    }

    /**
     * @param array{excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>} $data
     */
    public static function listaHtml(array $data, string $rowOnclickFn): string
    {
        $html = '<table>';
        foreach ($data['excepciones'] as $row) {
            $idTipoActiv = PayloadCoercion::int($row['id_tipo_activ']);
            $etiqueta = htmlspecialchars($row['etiqueta'], ENT_QUOTES, 'UTF-8');
            $valor = $row['valor'];
            $valorJs = htmlspecialchars(addslashes($valor), ENT_QUOTES, 'UTF-8');
            $valorHtml = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            $html .= "<tr><td>$etiqueta</td><td>";
            $html .= "<span class=\"link\" onclick=\"$rowOnclickFn($idTipoActiv,'$valorJs')\" size=\"200\">$valorHtml</span></td></tr>";
        }
        $html .= '</table>';

        return $html;
    }
}
