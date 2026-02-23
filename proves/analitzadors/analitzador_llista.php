<?php


function analitzar_llista_php($contingut_php)
{
    $rutes = [];

    // 1. Cercar rutes directes en botons o URLs (url: '...', action: '...')
    $pattern_url = '/(?:url|action|href|link)[\'"]?\s*[:=,]\s*[\'"]([a-z0-9_\-\.\/]+\.php)/i';
    if (preg_match_all($pattern_url, $contingut_php, $m)) {
        foreach ($m[1] as $url) $rutes[] = ltrim($url, '/');
    }

    // 2. Cercar crides a funcions JS de navegació (fnjs_update_div, fnjs_enviar_formulario, etc.)
    // i extreure'n el fitxer PHP si està com a argument
    $pattern_js_nav = '/fnjs_[a-z0-9_]+\([^,]+,[\'"]([a-z0-9_\-\.\/]+\.php)/i';
    if (preg_match_all($pattern_js_nav, $contingut_php, $m)) {
        foreach ($m[1] as $url) $rutes[] = ltrim($url, '/');
    }

    return array_unique($rutes);
}