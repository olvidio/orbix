<?php


function analitzar_twig($contingut_twig, $contingut_php)
{
    $rutes = [];

    // 1. CAPTURA DE RUTES LITERALS (JS/HTML)
    // Busquem només en llocs on hi ha rutes: url:, action=, href=, etc.
    $pattern_js = '/(?:url|action|href|onclick)[\'"]?\s*[:=,]\s*[^;]*?[\'"]([a-z0-9_\-\.\/]+\.php)[^\'"]*[\'"]/i';
    preg_match_all($pattern_js, $contingut_twig, $m_js);
    foreach ($m_js[1] as $r) {
        $rutes[] = ltrim($r, '/');
    }

    // 2. CAPTURA DE VARIABLES SEGONS EL SEU ÚS (Context de Navegació)
    // Definim quines funcions d'Orbix realment ens interessen per al mapa de flux
    $funcions_desti = 'fnjs_update_div|fnjs_buscar|fnjs_enviar_formulario|fnjs_left_side_hide';
    $atributs_desti = 'url|action|href';

    $patterns_context = [
        // a) Funcions específiques de navegació/AJAX: fnjs_update_div(..., '{{ var }}')
        '/(' . $funcions_desti . ')\([^)]*?\{\{\s*([a-z0-9_]+)[^}]*\}\}/i',
        // b) Atributs HTML o propietats d'objectes JS: url: '{{ var }}'
        '/(' . $atributs_desti . ')[\'"]?\s*[:=,]\s*[^;]*?\{\{\s*([a-z0-9_]+)[^}]*\}\}/i'
    ];

    $variables_sospitoses = [];
    foreach ($patterns_context as $p) {
        if (preg_match_all($p, $contingut_twig, $m)) {
            // El grup [2] conté el nom de la variable, el grup [1] la funció/atribut que l'ha disparat
            $variables_sospitoses = array_merge($variables_sospitoses, $m[2]);
        }
    }
    $variables_sospitoses = array_unique($variables_sospitoses);

    // 3. RESOLUCIÓ AL PHP (Només de les que s'usen per navegar)
    foreach ($variables_sospitoses as $var) {
        $trobat = false;
        if (!empty($contingut_php)) {
            // Busquem definició directa o mapeig a l'array (com abans)
            $pattern_v = '/\$' . $var . '\s*=\s*[^;]*?[\'"]([a-z0-9_\-\.\/]+\.php)/i';
            if (preg_match($pattern_v, $contingut_php, $m_v)) {
                $rutes[] = ltrim($m_v[1], '/');
                $trobat = true;
            } else {
                $pattern_map = '/[\'"]' . $var . '[\'"]\s*=>\s*(\$[a-z0-9_]+)/i';
                if (preg_match($pattern_map, $contingut_php, $m_map)) {
                    $v_php = str_replace('$', '', $m_map[1]);
                    $pattern_res = '/\$' . $v_php . '\s*=\s*[^;]*?[\'"]([a-z0-9_\-\.\/]+\.php)/i';
                    if (preg_match($pattern_res, $contingut_php, $m_res)) {
                        $rutes[] = ltrim($m_res[1], '/');
                        $trobat = true;
                    }
                }
            }
        }

        if (!$trobat) {
            $rutes[] = "{{ $var }} [DESTÍ NO RESOLT]";
        }
    }

    return array_unique($rutes);
}