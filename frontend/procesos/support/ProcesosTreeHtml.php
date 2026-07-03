<?php

namespace frontend\procesos\support;

use frontend\shared\helpers\PayloadCoercion;

/**
 * HTML del árbol de fases a partir de la clave `aPadres` devuelta por
 * {@see \src\procesos\application\ProcesosGet::execute()}.
 */
final class ProcesosTreeHtml
{
    /**
     * @param array<int, array<int, array<string, mixed>>> $aPadres
     */
    public static function dibujarTree(array $aPadres): string
    {
        if ($aPadres === []) {
            return '';
        }
        ksort($aPadres);
        $html = '<div id="tree">';
        if (!empty($aPadres[0])) {
            foreach ($aPadres[0] as $padre) {
                $id_fase_i = PayloadCoercion::int($padre['id'] ?? 0);
                $nom = PayloadCoercion::string($padre['nom'] ?? '');
                if (array_key_exists($id_fase_i, $aPadres)) {
                    $html .= '<div class="branch">';
                    $html .= '<div class="entry"><span>' . $nom . '</span>';
                    $html .= '<div class="branch">';
                    $html .= self::dibujarTreeHijos($aPadres, $id_fase_i);
                    $html .= '</div>';
                    $html .= '</div>';
                } else {
                    $html .= '<div class="entry"><span>' . $nom . '</span></div>';
                }
            }
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * @param array<int, array<int, array<string, mixed>>> $aPadres
     */
    private static function dibujarTreeHijos(array $aPadres, int $id_fase): string
    {
        if (empty($aPadres[$id_fase])) {
            return '';
        }
        $html = '';
        foreach ($aPadres[$id_fase] as $padre) {
            $id_fase_i = PayloadCoercion::int($padre['id'] ?? 0);
            $nom = PayloadCoercion::string($padre['nom'] ?? '');
            if (array_key_exists($id_fase_i, $aPadres)) {
                $html .= '<div class="branch">';
                $html .= '<div class="entry"><span>' . $nom . '</span>';
                $html .= '<div class="branch">';
                $html .= self::dibujarTreeHijos($aPadres, $id_fase_i);
                $html .= '</div>';
                $html .= '</div>';
            } else {
                $html .= '<div class="entry"><span>' . $nom . '</span></div>';
            }
        }

        return $html;
    }
}
