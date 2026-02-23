<?php


require_once "analitzador_llista.php";

function resoldre_ruta_vista($contingut_php, $root_dir, $fitxer_origen = '')
{
    $resul = ['rel_vista' => '', 'full_vista' => '', 'extensio' => '', 'es_llista' => false];

    if (strpos($contingut_php, 'new Lista()') !== false) {
        $resul['es_llista'] = true;
    }

    // Detectar si el fitxer origen és de apps/ o frontend/
    $prefix = 'apps/';
    if (!empty($fitxer_origen)) {
        if (strpos($fitxer_origen, 'frontend/') === 0) {
            $prefix = 'frontend/';
        }
    }

    $folder_base = '';
    if (preg_match('/new\s+(ViewTwig|ViewPhtml|ViewNewPhtml)\([\'"](.+?)[\'"]\)/i', $contingut_php, $m)) {
        // Corregim les barres abans i després del replace
        $raw_path = str_replace('\\', '/', $m[2]);
        // Sempre canviem controller per view, tant per apps com per frontend
        $folder_base = str_replace('controller', 'view', $raw_path);
    }

    $nom_vista = '';
    if (preg_match('/renderizar\([\'"](.+?)\.(html\.twig|phtml)[\'"]/i', $contingut_php, $m_v)) {
        $nom_vista = $m_v[1] . '.' . $m_v[2];
        $resul['extensio'] = ($m_v[2] === 'html.twig') ? 'twig' : 'phtml';
    } elseif (preg_match('/(?:include|require)(?:_once)?\s*\(?[\'"]([^\'"]+\.phtml)[\'"]/i', $contingut_php, $m_p)) {
        $nom_vista = $m_p[1];
        $resul['extensio'] = 'phtml';
    }

    if (!empty($nom_vista)) {
        if (!empty($folder_base)) {
            // Si el folder_base ja té el prefix (frontend/ o apps/), no cal afegir-lo
            if (strpos($folder_base, 'frontend/') === 0 || strpos($folder_base, 'apps/') === 0) {
                $path_final = trim($folder_base, '/') . "/" . ltrim($nom_vista, '/');
            } else {
                $path_final = $prefix . trim($folder_base, '/') . "/" . ltrim($nom_vista, '/');
            }
        } else {
            $path_final = $prefix . "actividades/view/" . ltrim($nom_vista, '/');
        }

        // Unifiquem totes les barres a l'estil Linux/Web
        $resul['rel_vista'] = str_replace('\\', '/', $path_final);
        $resul['full_vista'] = str_replace('\\', '/', $root_dir . ltrim($resul['rel_vista'], '/'));
    }
    return $resul;
}