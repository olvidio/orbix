<?php


function analitzar_phtml($contingut_phtml, $contingut_php)
{
    $rutes = [];

    // 1. RUTES LITERALS (JS/HTML)
    $pattern_js = '/(?:url|action|href|onclick)[\'"]?\s*[:=,]\s*[^;]*?[\'"]([a-z0-9_\-\.\/]+\.php)[^\'"]*[\'"]/i';
    if (preg_match_all($pattern_js, $contingut_phtml, $m_js)) {
        foreach ($m_js[1] as $r) {
            $rutes[] = ltrim($r, '/');
        }
    }

    // 2. CAPTURA DE VARIABLES SEGONS EL SEU ÚS (Context de Navegació)
    $funcions_desti = 'fnjs_update_div|fnjs_buscar|fnjs_enviar_formulario|fnjs_left_side_hide';
    $atributs_desti = 'url|action|href';

    $patterns_context = [
        '/(' . $funcions_desti . ')\([^)]*?<\?=\s*([^?]+)\s*\?>/i',
        '/(' . $atributs_desti . ')[\'"]?\s*[:=,]\s*[^;]*?<\?=\s*([^?]+)\s*\?>/i'
    ];

    $variables_sospitoses = [];
    foreach ($patterns_context as $p) {
        if (preg_match_all($p, $contingut_phtml, $m)) {
            foreach ($m[2] as $raw_var) {
                $raw_var = trim($raw_var);
                // Cas A: $a_campos['clau']
                if (preg_match('/\$a_campos\[[\'"](.+?)[\'"]\]/', $raw_var, $m_clau)) {
                    $variables_sospitoses[] = ['tipus' => 'array', 'nom' => $m_clau[1]];
                } // Cas B: $variable_simple
                elseif (strpos($raw_var, '$') === 0) {
                    $variables_sospitoses[] = ['tipus' => 'simple', 'nom' => ltrim($raw_var, '$')];
                }
            }
        }
    }

    // 3. RESOLUCIÓ AL PHP
    foreach ($variables_sospitoses as $v) {
        $nom = $v['nom'];
        $trobat = false;

        if (!empty($contingut_php)) {
            if ($v['tipus'] === 'array') {
                // Busquem l'assignació a l'array: $a_campos['nom'] = ...
                $pattern_res = '/\$a_campos\[[\'"]' . preg_quote($nom, '/') . '[\' injection]\]\s*=\s*([^;]+)/i';
                if (preg_match($pattern_res, $contingut_php, $m_res)) {
                    $valor = trim($m_res[1]);
                    if (strpos($valor, '$') === 0) {
                        $v_php = ltrim($valor, '$');
                        $p_final = '/\$' . preg_quote($v_php, '/') . '\s*=\s*[^;]*?[\'"]([a-z0-9_\-\.\/]+\.php)/i';
                        if (preg_match($p_final, $contingut_php, $m_f)) {
                            $rutes[] = ltrim($m_f[1], '/');
                            $trobat = true;
                        }
                    } else {
                        $rutes[] = ltrim(trim($valor, " '\""), '/');
                        $trobat = true;
                    }
                }
            } else {
                // Variable simple: $nom = ...
                $pattern_simple = '/\$' . preg_quote($nom, '/') . '\s*=\s*[^;]*?[\'"]([a-z0-9_\-\.\/]+\.php)/i';
                if (preg_match($pattern_simple, $contingut_php, $m_s)) {
                    $rutes[] = ltrim($m_s[1], '/');
                    $trobat = true;
                }
            }
        }

        if (!$trobat) {
            $rutes[] = ($v['tipus'] === 'array' ? "\$a_campos['$nom']" : "\$$nom") . " [DESTÍ NO RESOLT]";
        }
    }

    return array_unique($rutes);
}