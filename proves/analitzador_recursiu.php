<?php


/**
 * Analitzador Professional Orbix v28 - Generador d'Estratègia de Testing
 *
 * Aquest script analitza un punt d'entrada (pàgina del menú) i genera una checklist
 * completa de testing detectant:
 * - Controladors PHP principals
 * - Vistes (Twig/Phtml) i els seus scripts JS
 * - Crides AJAX dins de les vistes
 * - Accions detectades: NUEVO, MODIFICAR, LISTAR, ELIMINAR, VER, etc.
 */

$ruta_base_proves = __DIR__;
$arrel_projecte = dirname(__DIR__);

$punt_entrada = "frontend/actividades/controller/actividad_que.php";
$nom_modul_principal = explode('/', $punt_entrada)[1];
$carpeta_desti = $ruta_base_proves . '/' . $nom_modul_principal;
if (!is_dir($carpeta_desti)) mkdir($carpeta_desti, 0777, true);

$fitxer_sortida = $carpeta_desti . '/checklist_' . $nom_modul_principal . '.md';
$visitats = [];
$accions_detectades = [];
$buffer_sortida = "# 📝 ESTRATÈGIA DE PROVES: " . strtoupper($nom_modul_principal) . "\n\n";
$buffer_sortida .= "**Punt d'entrada:** `{$punt_entrada}`\n\n";
$buffer_sortida .= "---\n\n## 📋 FITXERS A PROVAR\n\n";

function detectar_accions($ruta, $contingut)
{
    $accions = [];
    $nom_fitxer = basename($ruta);

    // Detectar per nom de fitxer
    if (preg_match('/(nuevo|nou|new|crear|create)/i', $nom_fitxer)) $accions[] = '➕ CREAR';
    if (preg_match('/(update|modificar|actualizar|edit)/i', $nom_fitxer)) $accions[] = '✏️ MODIFICAR';
    if (preg_match('/(delete|eliminar|borrar|remove)/i', $nom_fitxer)) $accions[] = '🗑️ ELIMINAR';
    if (preg_match('/(list|lista|listado)/i', $nom_fitxer)) $accions[] = '📋 LLISTAR';
    if (preg_match('/(select|buscar|search|que)/i', $nom_fitxer)) $accions[] = '🔍 BUSCAR';
    if (preg_match('/(ver|view|detalle|detail)/i', $nom_fitxer)) $accions[] = '👁️ VEURE';
    if (preg_match('/(ajax|json|api)/i', $nom_fitxer)) $accions[] = '🔄 AJAX';
    if (preg_match('/(import|exportar|export)/i', $nom_fitxer)) $accions[] = '📤 IMPORTAR/EXPORTAR';
    if (preg_match('/(print|imprimir|pdf)/i', $nom_fitxer)) $accions[] = '🖨️ IMPRIMIR';

    // Detectar dins del contingut
    if (preg_match('/\b(INSERT\s+INTO|->DBInsert|->create|new\s+\w+\(\))/i', $contingut)) {
        if (!in_array('➕ CREAR', $accions)) $accions[] = '➕ CREAR';
    }
    if (preg_match('/\b(UPDATE\s+\w+\s+SET|->DBUpdate|->update|->modify)/i', $contingut)) {
        if (!in_array('✏️ MODIFICAR', $accions)) $accions[] = '✏️ MODIFICAR';
    }
    if (preg_match('/\b(DELETE\s+FROM|->DBDelete|->delete|->remove)/i', $contingut)) {
        if (!in_array('🗑️ ELIMINAR', $accions)) $accions[] = '🗑️ ELIMINAR';
    }
    if (preg_match('/\b(SELECT\s+.*\s+FROM|->query|->getList|->getAll)/i', $contingut)) {
        if (!in_array('📋 LLISTAR', $accions)) $accions[] = '📋 LLISTAR';
    }

    return $accions;
}

function analitzar($ruta, $nivell = 1, $tipus_pare = '')
{
    global $arrel_projecte, $visitats, $buffer_sortida, $accions_detectades;

    $ruta = str_replace(['\\', '//'], '/', trim($ruta, ' /'));

    // FILTRES ANTI-SOROLL
    if (str_contains($ruta, 'controller.phtml')) return;
    if (preg_match('/(global_|core\/|db_|ConfigGlobal|class\.|func\.|\.inc)/i', $ruta)) return;

    if (in_array($ruta, $visitats) || $nivell > 15) return;
    $visitats[] = $ruta;

    $ruta_full = $arrel_projecte . '/' . $ruta;
    $existeix = file_exists($ruta_full);

    $ext = pathinfo($ruta, PATHINFO_EXTENSION);
    $tipus = match ($ext) {
        'php' => "⚙️ PHP",
        'phtml' => "🖼️ PHTML",
        'twig' => "🖼️ TWIG",
        'js' => "📜 JS",
        default => "📄"
    };
    $icona = $existeix ? "" : " ⚠️ (No trobat)";
    $indent = str_repeat("  ", $nivell - 1);

    // Detectar accions si el fitxer existeix
    $accions_str = "";
    if ($existeix) {
        $contingut = file_get_contents($ruta_full);
        $accions = detectar_accions($ruta, $contingut);
        if (!empty($accions)) {
            $accions_str = " → " . implode(', ', $accions);
            $accions_detectades[$ruta] = $accions;
        }
    } else {
        $contingut = "";
    }

    $buffer_sortida .= "{$indent}- [ ] **NIVELL $nivell** ($tipus): `{$ruta}`{$accions_str}{$icona}\n";

    if (!$existeix) return;
    $trobats = [];

    // 1. CERCA DE VISTES (Dins de PHP) - ViewTwig o ViewPhtml
    // Pattern: new ViewTwig('actividades/controller') ... ->renderizar('actividad_que.html.twig', ...)
    if ($ext === 'php') {
        // Primer, busquem la creació de la vista per obtenir el directori base
        if (preg_match("/new\s+View(Phtml|Twig)\s*\(\s*['\"]([^'\"]+)['\"]\s*\)/i", $contingut, $m_view)) {
            $tipus_vista = strtolower($m_view[1]);
            $dir_constructor = $m_view[2]; // Ex: 'actividades/controller'

            // El constructor reemplaça 'controller' o 'model' per 'view'
            $dir_base = preg_replace(['#/controller#', '#/model#'], ['/view', '/view'], $dir_constructor);

            // Ara busquem les crides a renderizar
            if (preg_match_all("/->renderizar\s*\(\s*['\"]([^'\"]+)['\"]/i", $contingut, $m_render)) {
                foreach ($m_render[1] as $nom_vista) {
                    // La ruta completa és: apps/{dir_base}/{nom_vista}
                    $ruta_vista = "apps/{$dir_base}/{$nom_vista}";
                    $trobats[] = ["ruta" => $ruta_vista, "nivell" => $nivell + 1, "tipus" => 'vista'];
                }
            }
        }
    }

    // 2. Si estem en una VISTA (phtml o twig), busquem crides AJAX dins dels scripts
    if (($ext === 'phtml' || $ext === 'twig') && preg_match_all("/url\s*[:=]\s*['\"]([^'\"]+\.php)['\"]|ajax\s*\([^)]*url\s*:\s*['\"]([^'\"]+)['\"]|\.load\s*\(\s*['\"]([^'\"]+\.php)['\"]/is", $contingut, $m_ajax)) {
        // Combinar tots els grups de captura
        $urls_ajax = array_merge(
            array_filter($m_ajax[1]),
            array_filter($m_ajax[2]),
            array_filter($m_ajax[3])
        );

        foreach ($urls_ajax as $url) {
            $url = trim($url);
            // Si comença amb apps/ ja és una ruta completa
            if (str_starts_with($url, 'apps/')) {
                if (preg_match('#apps/([a-zA-Z0-9_]+)/controller/([a-zA-Z0-9_-]+\.php)#', $url, $match)) {
                    $trobats[] = ["ruta" => "apps/{$match[1]}/controller/{$match[2]}", "nivell" => $nivell + 1, "tipus" => 'ajax'];
                }
            }
        }
    }

    // 3. Cerques adicionals dins de qualsevol fitxer: rutes completes apps/module/controller/file.php
    if (preg_match_all("#['\"]apps/([a-zA-Z0-9_]+)/controller/([a-zA-Z0-9_-]+\.php)['\"]#", $contingut, $m_rescat)) {
        foreach ($m_rescat[0] as $match) {
            $link = trim($match, '\'"');
            $trobats[] = ["ruta" => $link, "nivell" => $nivell + 1, "tipus" => 'link'];
        }
    }

    // 4. CERCA DE LINKS RELATIUS (només nom de fitxer, dins del mateix mòdul/controller)
    if (preg_match_all("/['\"]([a-zA-Z0-9_-]{3,}\.php)['\"]/", $contingut, $m_files)) {
        foreach ($m_files[1] as $f) {
            // Evitar noms genèrics
            if (in_array($f, ['data.php', 'json.php', 'api.php', 'index.php'])) continue;

            $parts = explode('/', $ruta);
            if (count($parts) < 2) continue;

            $modul = $parts[1];
            $candidat = "apps/{$modul}/controller/{$f}";

            // Només afegir si existeix
            if (file_exists($arrel_projecte . '/' . $candidat)) {
                $trobats[] = ["ruta" => $candidat, "nivell" => $nivell + 1, "tipus" => 'relatiu'];
            }
        }
    }

    // Eliminar duplicats
    $trobats_unics = [];
    $rutes_vistes = [];
    foreach ($trobats as $item) {
        if (!in_array($item['ruta'], $rutes_vistes)) {
            $trobats_unics[] = $item;
            $rutes_vistes[] = $item['ruta'];
        }
    }

    // PROCESSAR RECURSIVAMENT
    foreach ($trobats_unics as $item) {
        analitzar($item['ruta'], $item['nivell'], $item['tipus']);
    }
}

analitzar($punt_entrada);

// Afegir resum d'accions al final
$buffer_sortida .= "\n---\n\n## 🎯 RESUM D'ACCIONS DETECTADES\n\n";

$accions_agrupades = [];
foreach ($accions_detectades as $fitxer => $accions) {
    foreach ($accions as $accio) {
        if (!isset($accions_agrupades[$accio])) {
            $accions_agrupades[$accio] = [];
        }
        $accions_agrupades[$accio][] = $fitxer;
    }
}

foreach ($accions_agrupades as $accio => $fitxers) {
    $buffer_sortida .= "### $accio\n";
    foreach ($fitxers as $fitxer) {
        $buffer_sortida .= "- `$fitxer`\n";
    }
    $buffer_sortida .= "\n";
}

$buffer_sortida .= "---\n\n## ✅ CHECKLIST DE TESTING\n\n";
$buffer_sortida .= "### Tests funcionals a realitzar:\n\n";

if (isset($accions_agrupades['🔍 BUSCAR'])) {
    $buffer_sortida .= "#### 1. Formulari de cerca\n";
    $buffer_sortida .= "- [ ] Obrir la pàgina principal\n";
    $buffer_sortida .= "- [ ] Emplenar els camps de cerca\n";
    $buffer_sortida .= "- [ ] Verificar que es mostren resultats correctes\n";
    $buffer_sortida .= "- [ ] Provar filtres i ordenació\n\n";
}

if (isset($accions_agrupades['➕ CREAR'])) {
    $buffer_sortida .= "#### 2. Crear nou registre\n";
    $buffer_sortida .= "- [ ] Clicar botó 'Nuevo'\n";
    $buffer_sortida .= "- [ ] Emplenar formulari amb dades vàlides\n";
    $buffer_sortida .= "- [ ] Guardar i verificar missatge d'èxit\n";
    $buffer_sortida .= "- [ ] Verificar que el registre apareix al llistat\n";
    $buffer_sortida .= "- [ ] Provar validacions (camps obligatoris, formats, etc.)\n\n";
}

if (isset($accions_agrupades['✏️ MODIFICAR'])) {
    $buffer_sortida .= "#### 3. Modificar registre existent\n";
    $buffer_sortida .= "- [ ] Seleccionar un registre del llistat\n";
    $buffer_sortida .= "- [ ] Modificar camps\n";
    $buffer_sortida .= "- [ ] Guardar canvis\n";
    $buffer_sortida .= "- [ ] Verificar que els canvis s'han aplicat correctament\n\n";
}

if (isset($accions_agrupades['🗑️ ELIMINAR'])) {
    $buffer_sortida .= "#### 4. Eliminar registre\n";
    $buffer_sortida .= "- [ ] Seleccionar un registre\n";
    $buffer_sortida .= "- [ ] Confirmar eliminació\n";
    $buffer_sortida .= "- [ ] Verificar que ja no apareix al llistat\n\n";
}

if (isset($accions_agrupades['📋 LLISTAR'])) {
    $buffer_sortida .= "#### 5. Llistat i navegació\n";
    $buffer_sortida .= "- [ ] Verificar que es mostren totes les columnes correctament\n";
    $buffer_sortida .= "- [ ] Provar paginació (si existeix)\n";
    $buffer_sortida .= "- [ ] Provar ordenació per diferents columnes\n\n";
}

if (isset($accions_agrupades['🔄 AJAX'])) {
    $buffer_sortida .= "#### 6. Funcionalitats AJAX\n";
    $buffer_sortida .= "- [ ] Verificar que les crides AJAX funcionen correctament\n";
    $buffer_sortida .= "- [ ] Comprovar que les respostes actualitzen la interfície\n";
    $buffer_sortida .= "- [ ] Verificar errors i timeouts\n\n";
}

if (isset($accions_agrupades['📤 IMPORTAR/EXPORTAR'])) {
    $buffer_sortida .= "#### 7. Importar/Exportar\n";
    $buffer_sortida .= "- [ ] Provar exportació de dades\n";
    $buffer_sortida .= "- [ ] Verificar format del fitxer exportat\n";
    $buffer_sortida .= "- [ ] Provar importació (si existeix)\n\n";
}

$buffer_sortida .= "### Tests de navegació:\n\n";
$buffer_sortida .= "- [ ] Verificar que tots els enllaços funcionen\n";
$buffer_sortida .= "- [ ] Provar navegació endavant/enrere del navegador\n";
$buffer_sortida .= "- [ ] Verificar breadcrumbs i menús de navegació\n\n";

$buffer_sortida .= "### Tests de seguretat:\n\n";
$buffer_sortida .= "- [ ] Verificar permisos d'accés\n";
$buffer_sortida .= "- [ ] Provar accés sense autenticació (ha de redirigir a login)\n";
$buffer_sortida .= "- [ ] Verificar que no es poden fer accions no permeses\n\n";

file_put_contents($fitxer_sortida, $buffer_sortida);

echo "\n✅ ESTRATÈGIA DE TESTING GENERADA!\n";
echo "📄 Fitxer: $fitxer_sortida\n";
echo "📊 Fitxers analitzats: " . count($visitats) . "\n";
echo "🎯 Accions detectades: " . count($accions_agrupades) . "\n\n";
