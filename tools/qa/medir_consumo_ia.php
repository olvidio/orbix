<?php

/**
 * Mide el consumo de tokens de una sesión de Claude Code, agrupado por turno de usuario
 * (cada mensaje tuyo y todo el trabajo de la IA hasta el siguiente). Sirve para estimar
 * cuánto cuesta un paso de un plan por pasos (p. ej. docs/dev/refactor_busquedas_criteria.md)
 * antes de lanzar el siguiente.
 *
 * Uso (desde la raíz del repo):
 *   php tools/qa/medir_consumo_ia.php                      # sesión más reciente del proyecto
 *   php tools/qa/medir_consumo_ia.php --session=<uuid>     # una sesión concreta
 *   php tools/qa/medir_consumo_ia.php --all                # todas las sesiones del proyecto
 *   php tools/qa/medir_consumo_ia.php --json               # salida JSON (para guardar el histórico)
 *   php tools/qa/medir_consumo_ia.php --dir=/ruta/a/transcripciones
 *
 * Lee solo los .jsonl de ~/.claude/projects/<ruta-del-proyecto>/. No modifica nada.
 *
 * Columnas: llamadas a la API, tokens de entrada nuevos, escritura en caché, lectura de
 * caché, salida, y "u.eq.": unidades equivalentes de entrada = ent + 1.25·escritura +
 * 0.1·lectura + 5·salida (proporciones del precio de API; es una referencia relativa para
 * comparar pasos entre sí, NO el cupo real de tu plan, que Anthropic no publica en tokens).
 */

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$dir = null;
$session = null;
$all = false;
$json = false;
foreach (array_slice($argv, 1) as $arg) {
    if (str_starts_with($arg, '--dir=')) {
        $dir = substr($arg, 6);
    } elseif (str_starts_with($arg, '--session=')) {
        $session = substr($arg, 10);
    } elseif ($arg === '--all') {
        $all = true;
    } elseif ($arg === '--json') {
        $json = true;
    }
}
if ($dir === null) {
    $home = getenv('HOME') ?: '';
    $dir = $home . '/.claude/projects/' . preg_replace('/[^A-Za-z0-9]/', '-', $root);
}
$files = glob(rtrim($dir, '/') . '/*.jsonl') ?: [];
if ($files === []) {
    fwrite(STDERR, "No hay transcripciones en $dir\n");
    exit(1);
}
usort($files, static fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));
if ($session !== null) {
    $files = array_values(array_filter($files, static fn (string $f): bool => str_contains(basename($f), $session)));
} elseif (!$all) {
    $files = [$files[0]];
}
if ($files === []) {
    fwrite(STDERR, "Sesión no encontrada\n");
    exit(1);
}

/** @return list<array<string, mixed>> */
function turnosDe(string $file): array
{
    $turnos = [];
    $actual = null;
    $porMensaje = [];   // id de mensaje => usage (el último gana: los trozos de streaming son acumulativos)

    $cerrar = static function () use (&$actual, &$porMensaje, &$turnos): void {
        if ($actual === null) {
            return;
        }
        foreach ($porMensaje as $u) {
            $actual['llamadas']++;
            $actual['in'] += (int) ($u['input_tokens'] ?? 0);
            $actual['cache_w'] += (int) ($u['cache_creation_input_tokens'] ?? 0);
            $actual['cache_r'] += (int) ($u['cache_read_input_tokens'] ?? 0);
            $actual['out'] += (int) ($u['output_tokens'] ?? 0);
        }
        $turnos[] = $actual;
        $porMensaje = [];
    };

    $fh = fopen($file, 'rb');
    if ($fh === false) {
        return [];
    }
    while (($linea = fgets($fh)) !== false) {
        $d = json_decode($linea, true);
        if (!is_array($d)) {
            continue;
        }
        $tipo = $d['type'] ?? '';
        $msg = $d['message'] ?? null;
        if ($tipo === 'user' && is_array($msg) && !isset($d['toolUseResult']) && empty($d['isMeta'])) {
            $c = $msg['content'] ?? '';
            $texto = '';
            if (is_string($c)) {
                $texto = $c;
            } elseif (is_array($c)) {
                foreach ($c as $b) {
                    if (is_array($b) && ($b['type'] ?? '') === 'text') {
                        $texto .= (string) $b['text'];
                    } elseif (is_array($b) && ($b['type'] ?? '') === 'tool_result') {
                        $texto = '';
                        break;
                    }
                }
            }
            $texto = trim((string) preg_replace('/\s+/', ' ', $texto));
            if ($texto === '' || str_starts_with($texto, '<')) {
                continue;   // resultados de herramientas, recordatorios del sistema, comandos
            }
            $cerrar();
            $actual = [
                'sesion' => substr(basename($file, '.jsonl'), 0, 8),
                'inicio' => (string) ($d['timestamp'] ?? ''),
                'prompt' => mb_substr($texto, 0, 70),
                'llamadas' => 0, 'in' => 0, 'cache_w' => 0, 'cache_r' => 0, 'out' => 0,
            ];
            continue;
        }
        if ($tipo === 'assistant' && is_array($msg) && isset($msg['usage']) && $actual !== null) {
            $id = (string) ($msg['id'] ?? $d['uuid'] ?? uniqid());
            $porMensaje[$id] = $msg['usage'];
            $actual['fin'] = (string) ($d['timestamp'] ?? '');
        }
    }
    fclose($fh);
    $cerrar();
    return $turnos;
}

function ueq(array $t): int
{
    return (int) round($t['in'] + 1.25 * $t['cache_w'] + 0.1 * $t['cache_r'] + 5 * $t['out']);
}

$turnos = [];
foreach (array_reverse($files) as $f) {
    array_push($turnos, ...turnosDe($f));
}
foreach ($turnos as &$t) {
    $t['ueq'] = ueq($t);
}
unset($t);

if ($json) {
    echo json_encode($turnos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\n";
    exit(0);
}

$k = static fn (int $n): string => number_format($n, 0, ',', '.');
printf("%-8s %-16s %-48s %5s %8s %9s %10s %8s %10s\n", 'sesión', 'inicio', 'turno', 'API', 'ent', 'cache_w', 'cache_r', 'sal', 'u.eq.');
$tot = ['llamadas' => 0, 'in' => 0, 'cache_w' => 0, 'cache_r' => 0, 'out' => 0, 'ueq' => 0];
foreach ($turnos as $t) {
    printf(
        "%-8s %-16s %-48s %5d %8s %9s %10s %8s %10s\n",
        $t['sesion'],
        substr(str_replace('T', ' ', $t['inicio']), 0, 16),
        mb_substr($t['prompt'], 0, 48),
        $t['llamadas'],
        $k($t['in']),
        $k($t['cache_w']),
        $k($t['cache_r']),
        $k($t['out']),
        $k($t['ueq'])
    );
    foreach ($tot as $c => $_) {
        $tot[$c] += $t[$c];
    }
}
printf(
    "%-8s %-16s %-48s %5d %8s %9s %10s %8s %10s\n",
    '', '', 'TOTAL (' . count($turnos) . ' turnos)',
    $tot['llamadas'], $k($tot['in']), $k($tot['cache_w']), $k($tot['cache_r']), $k($tot['out']), $k($tot['ueq'])
);
if (count($turnos) > 0) {
    $u = array_column($turnos, 'ueq');
    sort($u);
    printf("\nu.eq. por turno: mediana %s, media %s, máximo %s\n", $k($u[intdiv(count($u), 2)]), $k((int) round(array_sum($u) / count($u))), $k(max($u)));
}
