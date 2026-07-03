<?php

declare(strict_types=1);

namespace frontend\encargossacd\helpers;

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\web\Desplegable;

final class EncargossacdCtrRender
{
/**
 * Construye el bloque HTML de colaboradores (sacd adicionales) de un encargo.
 *
 * @param array<int, array<string, mixed>> $colaboradores
 * @param array<int, string>               $dedicM
 * @param array<int, string>               $dedicT
 * @param array<int, string>               $dedicV
 * @param array<int, string>               $dedicSacd
 * @param array<int|string, string>        $opcionesBase
 * @param array<int|string, string>|null   $opcionesConSssc
 */
public static function construirOtrosSacd(
    int $e,
    int $mod_horario_e,
    array $colaboradores,
    array $dedicM,
    array $dedicT,
    array $dedicV,
    array $dedicSacd,
    array $opcionesBase,
    ?array $opcionesConSssc,
): string {
    if ($colaboradores === []) {
        return '';
    }

    $html = '';
    foreach ($colaboradores as $colab) {
        $s = PayloadCoercion::int($colab['s'] ?? 0);
        $id_nom = PayloadCoercion::int($colab['id_nom'] ?? 0);
        $necesitaSssc = !empty($colab['necesita_sssc']);

        $opciones = $necesitaSssc && $opcionesConSssc !== null ? $opcionesConSssc : $opcionesBase;
        $oDespl = new Desplegable();
        $oDespl->setBlanco(true);
        $oDespl->setOpciones($opciones);
        $oDespl->setOpcion_sel(EncargossacdPayload::desplegableOpcionSel($id_nom));

        $html .= "<tr><td>sacd $s:</td><td colspan=3 class=contenido><select name=id_sacd[$s]>";
        $html .= $oDespl->options();
        $html .= '</td></tr><tr><td class=etiqueta >' . ucfirst(_('dedicación')) . '</td>';

        if ($mod_horario_e === 3) {
            $txtHorario = PayloadCoercion::string($dedicSacd[$s] ?? '');
            $html .= '<td>' . $txtHorario . '</td></tr><tr>';
        } else {
            $m = PayloadCoercion::string($dedicM[$s] ?? '');
            $t = PayloadCoercion::string($dedicT[$s] ?? '');
            $v = PayloadCoercion::string($dedicV[$s] ?? '');
            $html .= "<td><input type=text size=1 name=dedic_m[$s] value=$m>" . _('mañanas');
            $html .= "</td><td><input type=text size=1 name=dedic_t[$s] value=$t>" . _('tarde 1ª hora');
            $html .= "</td><td><input type=text size=1 name=dedic_v[$s] value=$v>" . _('tarde 2ª hora');
            $html .= '</td></tr><tr>';
        }
    }

    return $html;
}

}
