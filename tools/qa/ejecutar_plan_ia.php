<?php

/**
 * Ejecuta, uno a uno, los pasos pendientes (`- [ ] **N.M**`) de un plan por pasos
 * (por defecto docs/dev/refactor_busquedas_criteria.md) lanzando `claude -p`, hasta que se
 * acabe el plan, el presupuesto o el cupo. Registra el consumo real de cada paso para afinar
 * las estimaciones de las fases siguientes.
 *
 * Uso (desde la raíz del repo):
 *   php tools/qa/ejecutar_plan_ia.php --dry-run                  # qué haría, sin gastar nada
 *   php tools/qa/ejecutar_plan_ia.php --fase=2                   # piloto: pasos 2.x (pregunta el % usado)
 *   php tools/qa/ejecutar_plan_ia.php --anotar-uso=63            # guarda "he gastado el 63 % de la semana"
 *   php tools/qa/ejecutar_plan_ia.php --usado=63 --max-pasos=2
 *
 * Cron (martes 14:30; el cupo semanal del plan Pro se renueva el martes a las 16:00):
 *   30 14 * * 2 PATH=/home/dani/.local/bin:/usr/local/bin:/usr/bin:/bin cd /home/dani/orbix_local/orbix && php tools/qa/ejecutar_plan_ia.php --fase=2 >> docs/dev/reports/ejecutar_plan_ia.log 2>&1
 *
 * Opciones:
 *   --plan=RUTA          plan a ejecutar (def. docs/dev/refactor_busquedas_criteria.md)
 *   --fase=N             solo la fase N (pasos "N.x") o un paso concreto (p. ej. --fase=2.1); def. todos
 *   --max-pasos=N        máximo de pasos en esta ejecución
 *   --usado=PCT          % semanal ya usado ahora (lo guarda en el estado)
 *   --anotar-uso=PCT     solo guarda el % y sale
 *   --reserva=PCT        % del cupo que no se toca (def. 5)
 *   --hasta=HH:MM|ISO    no empezar pasos después de esa hora (def. próximo reset: martes 16:00)
 *   --timeout=MIN        tope por paso (def. 120)
 *   --rama=NOMBRE        rama de trabajo (def. refactor/criteria-auto); --sin-rama para no cambiar de rama
 *   --reanudar           continuar aunque haya cambios sin commitear de un paso interrumpido
 *   --modelo=NOMBRE      modelo de claude (def. el de tu configuración)
 *   --permisos=bypass    usa --dangerously-skip-permissions en vez de la lista de herramientas permitidas
 *   --dry-run            muestra decisiones y prompt, no lanza claude
 *   --repo=RUTA          repo sobre el que trabajar (def. la raíz de este repo; útil para pruebas)
 *
 * Qué NO hace: push, merge ni tocar `main`. Todo queda en la rama de trabajo para revisar.
 * Cómo sabe cuánto queda: nada lo expone por CLI, así que usa (1) el % que anotas
 * (`--usado` / `--anotar-uso` / la pregunta interactiva), (2) el consumo medido de cada paso
 * y (3) la calibración "% de semana por unidad equivalente" que sale del registro. Si no sabe el
 * % (cron sin dato de esta semana), trabaja hasta que `claude` avise de que se acabó el cupo.
 *
 * Ficheros (docs/dev/reports/): consumo_ia.jsonl (un registro por paso), consumo_ia_estado.json.
 * Solo lee/escribe esos dos ficheros además de lo que haga `claude` en el repo.
 */

declare(strict_types=1);

date_default_timezone_set(getenv('ORBIX_TZ') ?: 'Europe/Madrid');

const RESERVA_DEF = 5.0;
const MARGEN = 1.3;               // se exige que el paso quepa con un 30 % de holgura
const PASO_MINIMO_UEQ = 150000;   // por debajo de esto no se lanza ni un módulo suelto de la fase 4
const IGNORAR_SUCIO = ['docs/.obsidian/', 'docs/guias/', 'docs/dev/reports/consumo_ia', 'docs/dev/reports/ejecutar_plan_ia', 'docs/dev/reports/.ejecutar_plan_ia'];
const RAMA_DEF = 'refactor/criteria-auto';

/** Estimación inicial (u.eq.) por paso; ver sección 10.2 del plan. Se corrige con el registro. */
const ESTIMACIONES = [
    '2.0' => 300000, '2.1' => 325000, '2.2' => 200000, '2.3' => 400000, '2.4' => 125000,
    '3.1' => 250000, '3.2' => 150000, '5.1' => 200000, '5.2' => 150000, '5.3' => 150000,
];
const ESTIMACION_DEF = 300000;

// ---------------------------------------------------------------------------------------------
// Argumentos
// ---------------------------------------------------------------------------------------------

$root = dirname(__DIR__, 2);
$o = [
    'plan' => 'docs/dev/refactor_busquedas_criteria.md', 'fase' => null, 'max-pasos' => null,
    'usado' => null, 'anotar-uso' => null, 'reserva' => (string) RESERVA_DEF, 'hasta' => null,
    'timeout' => '120', 'rama' => RAMA_DEF, 'modelo' => null, 'permisos' => 'lista', 'repo' => $root,
    'sin-rama' => false, 'reanudar' => false, 'dry-run' => false,
];
foreach (array_slice($argv, 1) as $a) {
    if (!str_starts_with($a, '--')) {
        fwrite(STDERR, "Argumento no válido: $a\n");
        exit(2);
    }
    [$k, $v] = array_pad(explode('=', substr($a, 2), 2), 2, null);
    if (!array_key_exists($k, $o)) {
        fwrite(STDERR, "Opción desconocida: --$k\n");
        exit(2);
    }
    $o[$k] = is_bool($o[$k]) ? true : $v;
}
$repo = rtrim((string) $o['repo'], '/');
$dry = (bool) $o['dry-run'];
$claude = getenv('CLAUDE_BIN') ?: 'claude';
$dirReports = $repo . '/docs/dev/reports';
$ficheroLog = $dirReports . '/consumo_ia.jsonl';
$ficheroEstado = $dirReports . '/consumo_ia_estado.json';
$esTty = function_exists('posix_isatty') && posix_isatty(STDIN);

// ---------------------------------------------------------------------------------------------
// Utilidades
// ---------------------------------------------------------------------------------------------

function ahora(): DateTimeImmutable
{
    return new DateTimeImmutable('now');
}

function out(string $m = ''): void
{
    echo $m, "\n";
}

/** Último reset semanal (martes 16:00) anterior o igual a $t. */
function ultimoReset(DateTimeImmutable $t): DateTimeImmutable
{
    $r = $t->setTime(16, 0);
    $dias = ((int) $t->format('N') - 2 + 7) % 7;   // martes = 2
    $r = $r->modify("-$dias days");
    return $r > $t ? $r->modify('-7 days') : $r;
}

function proximoReset(DateTimeImmutable $t): DateTimeImmutable
{
    return ultimoReset($t)->modify('+7 days');
}

/**
 * @param list<string> $cmd
 * @param array<string, string>|null $env
 * @return array{code: int, out: string, err: string, timeout: bool}
 */
function ejecutar(array $cmd, string $cwd, int $timeoutSec = 0, ?array $env = null): array
{
    $p = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $cwd, $env);
    if (!is_resource($p)) {
        return ['code' => 127, 'out' => '', 'err' => 'no se pudo lanzar ' . $cmd[0], 'timeout' => false];
    }
    fclose($pipes[0]);
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);
    $out = $err = '';
    $inicio = time();
    $timeout = false;
    while (true) {
        $st = proc_get_status($p);
        $out .= (string) stream_get_contents($pipes[1]);
        $err .= (string) stream_get_contents($pipes[2]);
        if (!$st['running']) {
            break;
        }
        if ($timeoutSec > 0 && time() - $inicio > $timeoutSec) {
            proc_terminate($p, 15);
            sleep(2);
            $st = proc_get_status($p);
            if ($st['running']) {
                proc_terminate($p, 9);
            }
            $timeout = true;
            break;
        }
        usleep(200000);
    }
    $out .= (string) stream_get_contents($pipes[1]);
    $err .= (string) stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($p);
    return ['code' => $code, 'out' => $out, 'err' => $err, 'timeout' => $timeout];
}

function git(string $repo, string ...$args): string
{
    $r = ejecutar(['git', '-C', $repo, ...$args], $repo);
    return trim($r['out']);
}

/** @return list<string> ficheros con cambios sin commitear, salvo los ignorados. */
function sucio(string $repo): array
{
    // Sin trim(): el primer carácter de la primera línea puede ser el espacio de " M ruta".
    $salida = ejecutar(['git', '-C', $repo, 'status', '--porcelain', '--untracked-files=all'], $repo)['out'];
    $lineas = array_filter(explode("\n", $salida), static fn (string $l): bool => trim($l) !== '');
    $res = [];
    foreach ($lineas as $l) {
        $ruta = trim(substr($l, 3));
        foreach (IGNORAR_SUCIO as $pref) {
            if (str_starts_with($ruta, $pref)) {
                continue 2;
            }
        }
        $res[] = $l;
    }
    return $res;
}

function preguntar(string $q, bool $esTty): ?string
{
    if (!$esTty) {
        return null;
    }
    echo $q . ' ';
    $r = fgets(STDIN);
    return $r === false ? null : trim($r);
}

function parsearPct(?string $s): ?float
{
    if ($s === null) {
        return null;
    }
    $s = str_replace([',', '%', ' '], ['.', '', ''], $s);
    return is_numeric($s) && (float) $s >= 0 && (float) $s <= 100 ? (float) $s : null;
}

/** @return list<array<string, mixed>> */
function leerLog(string $f): array
{
    if (!is_file($f)) {
        return [];
    }
    $r = [];
    foreach (file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $l) {
        $d = json_decode($l, true);
        if (is_array($d)) {
            $r[] = $d;
        }
    }
    return $r;
}

/** @param array<string, mixed> $reg */
function anotarLog(string $f, array $reg): void
{
    @mkdir(dirname($f), 0775, true);
    file_put_contents($f, json_encode($reg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND | LOCK_EX);
}

/** @return array{pct: float, ts: string}|null */
function leerEstado(string $f): ?array
{
    $d = is_file($f) ? json_decode((string) file_get_contents($f), true) : null;
    return is_array($d) && isset($d['pct'], $d['ts']) ? ['pct' => (float) $d['pct'], 'ts' => (string) $d['ts']] : null;
}

function guardarEstado(string $f, float $pct): void
{
    @mkdir(dirname($f), 0775, true);
    file_put_contents($f, json_encode(['pct' => $pct, 'ts' => ahora()->format('c')], JSON_PRETTY_PRINT) . "\n", LOCK_EX);
}

function mediana(array $v): float
{
    sort($v);
    $n = count($v);
    return $n === 0 ? 0.0 : (float) ($n % 2 ? $v[intdiv($n, 2)] : ($v[$n / 2 - 1] + $v[$n / 2]) / 2);
}

function ueq(int $in, int $cw, int $cr, int $out): int
{
    return (int) round($in + 1.25 * $cw + 0.1 * $cr + 5 * $out);
}

/** % de semana por u.eq. según los pasos con % antes/después anotados a mano; null si no hay datos. */
function calibracion(array $log): ?float
{
    $d = $u = 0.0;
    foreach ($log as $r) {
        if (($r['resultado'] ?? '') === 'OK' && isset($r['pct_antes'], $r['pct_despues'], $r['ueq']) && $r['pct_despues'] >= $r['pct_antes']) {
            $d += $r['pct_despues'] - $r['pct_antes'];
            $u += $r['ueq'];
        }
    }
    return $d >= 1.0 && $u > 0 ? $d / $u : null;
}

/** Estimación de u.eq. de un paso: lo medido para ese id; si no, la tabla corregida con el factor real/estimado. */
function estimar(string $id, array $log): int
{
    $reales = [];
    $factores = [];
    foreach ($log as $r) {
        if (($r['resultado'] ?? '') !== 'OK' || !isset($r['ueq'])) {
            continue;
        }
        if (($r['id'] ?? '') === $id) {
            $reales[] = $r['ueq'];
        }
        $base = ESTIMACIONES[$r['id'] ?? ''] ?? null;
        if ($base !== null) {
            $factores[] = $r['ueq'] / $base;
        }
    }
    if ($reales !== []) {
        return (int) mediana($reales);
    }
    $base = ESTIMACIONES[$id] ?? null;
    if ($base === null) {
        $todos = array_column(array_filter($log, static fn (array $r): bool => ($r['resultado'] ?? '') === 'OK'), 'ueq');
        return $todos !== [] ? (int) mediana($todos) : ESTIMACION_DEF;
    }
    $f = $factores !== [] ? min(3.0, max(0.5, mediana($factores))) : 1.0;
    return (int) round($base * $f);
}

/** @return array{id: string, texto: string, linea: int}|null primer paso sin marcar que cumple el filtro de fase */
function siguientePaso(string $plan, ?string $fase): ?array
{
    foreach (file($plan, FILE_IGNORE_NEW_LINES) ?: [] as $i => $l) {
        if (preg_match('/^- \[ \] \*\*(\d+(?:\.\d+)+)[ *.:]/', $l, $m) === 1) {
            if ($fase === null || $m[1] === $fase || str_starts_with($m[1], $fase . '.')) {
                return ['id' => $m[1], 'texto' => trim(substr($l, 6)), 'linea' => $i + 1];
            }
        }
    }
    return null;
}

function pasoMarcado(string $plan, string $id): bool
{
    foreach (file($plan, FILE_IGNORE_NEW_LINES) ?: [] as $l) {
        if (preg_match('/^- \[(.)\] \*\*' . preg_quote($id, '/') . '[ *.:]/', $l, $m) === 1) {
            return $m[1] !== ' ';
        }
    }
    return true;   // la línea ya no existe (p. ej. bloque de la fase 4 troceado en módulos)
}

function uuid4(): string
{
    $b = random_bytes(16);
    $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
    $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
}

// ---------------------------------------------------------------------------------------------
// Comprobaciones previas
// ---------------------------------------------------------------------------------------------

if ($o['anotar-uso'] !== null) {
    $p = parsearPct((string) $o['anotar-uso']);
    if ($p === null) {
        fwrite(STDERR, "Porcentaje no válido\n");
        exit(2);
    }
    guardarEstado($ficheroEstado, $p);
    out("Anotado: {$p} % usado a las " . ahora()->format('Y-m-d H:i'));
    exit(0);
}

$plan = str_starts_with((string) $o['plan'], '/') ? (string) $o['plan'] : $repo . '/' . $o['plan'];
if (!is_file($plan)) {
    fwrite(STDERR, "No existe el plan: $plan\n");
    exit(2);
}
if (git($repo, 'rev-parse', '--is-inside-work-tree') !== 'true') {
    fwrite(STDERR, "$repo no es un repositorio git\n");
    exit(2);
}
@mkdir($dirReports, 0775, true);
$lock = fopen($dirReports . '/.ejecutar_plan_ia.lock', 'c');
if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
    fwrite(STDERR, "Ya hay otra ejecución en marcha\n");
    exit(3);
}
if (!$dry && ejecutar([$claude, '--version'], $repo)['code'] !== 0) {
    fwrite(STDERR, "No encuentro '$claude' en el PATH\n");
    exit(2);
}

$inicioEjecucion = ahora();
$hasta = $o['hasta'] !== null
    ? (preg_match('/^\d{1,2}:\d{2}$/', (string) $o['hasta']) === 1
        ? new DateTimeImmutable('today ' . $o['hasta'])
        : new DateTimeImmutable((string) $o['hasta']))
    : proximoReset($inicioEjecucion);
$reserva = (float) $o['reserva'];
$reset = ultimoReset($inicioEjecucion);
out('== ejecutar_plan_ia ' . $inicioEjecucion->format('Y-m-d H:i') . " | no empezar pasos después de {$hasta->format('Y-m-d H:i')} | último reset {$reset->format('Y-m-d H:i')}");

// Cambios sin commitear de un paso anterior interrumpido
$sucio = sucio($repo);
if ($sucio !== [] && !$o['reanudar'] && !$dry) {
    out('HAY CAMBIOS SIN COMMITEAR (¿paso interrumpido?):');
    foreach (array_slice($sucio, 0, 15) as $l) {
        out("  $l");
    }
    out('Revísalos y commitea/descarta, o relanza con --reanudar para que claude los complete.');
    exit(4);
}

// Rama de trabajo
if (!$o['sin-rama'] && !$dry) {
    $actual = git($repo, 'rev-parse', '--abbrev-ref', 'HEAD');
    if ($actual !== $o['rama']) {
        $existe = ejecutar(['git', '-C', $repo, 'rev-parse', '--verify', '--quiet', 'refs/heads/' . $o['rama']], $repo)['code'] === 0;
        $r = ejecutar($existe ? ['git', '-C', $repo, 'checkout', (string) $o['rama']] : ['git', '-C', $repo, 'checkout', '-b', (string) $o['rama']], $repo);
        if ($r['code'] !== 0) {
            fwrite(STDERR, "No pude cambiar a la rama {$o['rama']}: {$r['err']}\n");
            exit(5);
        }
        out("Rama de trabajo: {$o['rama']}" . ($existe ? '' : " (creada desde $actual)"));
    }
}

// ---------------------------------------------------------------------------------------------
// % usado
// ---------------------------------------------------------------------------------------------

$log = leerLog($ficheroLog);
$ppu = calibracion($log);   // % por u.eq.
$usado = null;              // % usado estimado ahora
$usadoOrigen = '';
if ($o['usado'] !== null) {
    $usado = parsearPct((string) $o['usado']);
    if ($usado === null) {
        fwrite(STDERR, "--usado no válido\n");
        exit(2);
    }
    guardarEstado($ficheroEstado, $usado);
    $usadoOrigen = 'indicado';
} else {
    $est = leerEstado($ficheroEstado);
    if ($est !== null && new DateTimeImmutable($est['ts']) >= $reset) {
        $usado = $est['pct'];
        $usadoOrigen = 'anotado el ' . (new DateTimeImmutable($est['ts']))->format('d/m H:i');
        if ($ppu !== null) {   // sumar lo gastado desde la anotación
            foreach ($log as $r) {
                if (isset($r['ts'], $r['ueq']) && new DateTimeImmutable($r['ts']) > new DateTimeImmutable($est['ts'])) {
                    $usado += $r['ueq'] * $ppu;
                }
            }
            $usadoOrigen .= ' + gasto registrado desde entonces';
        }
    } else {
        $r = parsearPct(preguntar('¿Qué % del límite semanal llevas usado? (Pro; Enter = no lo sé)', $esTty));
        if ($r !== null) {
            $usado = $r;
            guardarEstado($ficheroEstado, $usado);
            $usadoOrigen = 'indicado';
        }
    }
}
if ($usado !== null) {
    out(sprintf('Uso semanal: %.1f %% (%s). Reserva: %.1f %%.', $usado, $usadoOrigen, $reserva));
} else {
    out('Uso semanal desconocido: se trabaja hasta que claude avise de que se acabó el cupo (o hasta el reset).');
}
out($ppu !== null
    ? sprintf('Calibración: %.5f %% de semana por 1.000 u.eq. (%d registros).', $ppu * 1000, count($log))
    : 'Sin calibración todavía: este registro servirá para calcularla (anota el % antes y después de cada paso).');

// ---------------------------------------------------------------------------------------------
// Bucle de pasos
// ---------------------------------------------------------------------------------------------

$hechos = 0;
$maxPasos = $o['max-pasos'] !== null ? (int) $o['max-pasos'] : null;
$ultimoIntento = '';
$motivoFin = 'no quedan pasos pendientes';

while (true) {
    if ($maxPasos !== null && $hechos >= $maxPasos) {
        $motivoFin = "alcanzado --max-pasos=$maxPasos";
        break;
    }
    $paso = siguientePaso($plan, $o['fase']);
    if ($paso === null) {
        break;
    }
    if (ahora() >= $hasta) {
        $motivoFin = 'pasada la hora límite (' . $hasta->format('H:i') . ')';
        break;
    }
    if ($hechos > 0 && !$esTty && $ppu === null && $usado !== null) {
        $motivoFin = 'sin calibración y sin terminal: solo se ejecuta un paso para calibrar';
        break;
    }

    $id = $paso['id'];
    $estU = estimar($id, $log);
    $esBloque = str_starts_with($id, '4.');
    $presupUeq = null;
    out("\n--- Paso $id: " . mb_substr($paso['texto'], 0, 110));
    out('    estimación: ' . number_format($estU, 0, ',', '.') . ' u.eq.' . ($ppu !== null ? sprintf(' (~%.1f %% de la semana)', $estU * $ppu) : ''));

    if ($usado !== null && $ppu !== null) {
        $libre = 100.0 - $reserva - $usado;
        $presupUeq = (int) max(0, floor($libre / $ppu));
        out(sprintf('    disponible: %.1f %% (~%s u.eq.)', $libre, number_format($presupUeq, 0, ',', '.')));
        $cabe = $esBloque ? $presupUeq >= PASO_MINIMO_UEQ : $estU * MARGEN <= $presupUeq;
        if (!$cabe) {
            $motivoFin = "no cabe en el presupuesto restante (hacen falta ~" . number_format((int) ($estU * MARGEN), 0, ',', '.') . ' u.eq.)';
            break;
        }
    }

    // Prompt
    $presup = $presupUeq !== null ? "Presupuesto restante estimado para esta tanda: ~" . number_format($presupUeq, 0, ',', '.') . " u.eq. (unidades equivalentes de entrada; ver sección 10.1 del plan)." : 'Presupuesto restante: desconocido.';
    $sucioTxt = $sucio !== [] && $o['reanudar']
        ? "\nATENCIÓN: hay cambios sin commitear de un paso interrumpido. Revísalos con git status/diff antes de nada y complétalos como parte de este paso (o descártalos si están rotos)."
        : '';
    $bloque4 = $esBloque
        ? "\nEste paso es un bloque de la fase 4 con varios módulos. Primero trocéalo en el plan: una casilla `- [ ] **{$id}.<modulo>**` por módulo (commit propio). Después ejecuta módulo a módulo, un commit por módulo, marcando cada casilla, y PARA cuando el siguiente módulo no quepa en el presupuesto restante. Deja sin marcar los módulos pendientes."
        : '';
    $prompt = <<<TXT
Lee {$o['plan']} y ejecuta SOLO el siguiente paso pendiente, que es el {$id}. Sigue la sección 8 del plan y las reglas de AGENTS.md.

Contexto de ejecución: es una tanda automática sin nadie delante. No hagas preguntas ni esperes respuesta.
- Termina el paso con tests en verde (PHPUnit se ejecuta con docker exec en el contenedor orbix-php-fpm-1, ver memoria del proyecto), marca la casilla `[x]` del paso en el plan con una nota breve de decisiones (como en los pasos anteriores) y haz UN commit. Termina el mensaje del commit con la línea: Co-Authored-By: Claude Sonnet 5.5 <noreply@anthropic.com>
- No hagas push, ni merge, ni cambies de rama, ni toques `main`. No dejes cambios sin commitear.
- No inventes decisiones que sean del usuario. Si algo bloquea de verdad (decisión de diseño no cerrada, test que no puedes arreglar, entorno roto), deja tus cambios sin commitear guardados con `git stash push -u -m "bloqueado {$id}"`, no marques la casilla y explica el motivo.
- Si el paso resulta mucho mayor de lo previsto, divídelo en el plan (sección 8) y ejecuta solo la primera parte.
{$presup}{$bloque4}{$sucioTxt}

La ÚLTIMA línea de tu respuesta debe ser exactamente una de:
RESULTADO: OK
RESULTADO: PARCIAL: <qué falta>
RESULTADO: BLOQUEADO: <motivo>
TXT;

    $sesion = uuid4();
    $cmd = [$claude, '-p', $prompt, '--output-format', 'json', '--session-id', $sesion];
    if ($o['permisos'] === 'bypass') {
        $cmd[] = '--dangerously-skip-permissions';
    } else {
        $cmd[] = '--permission-mode';
        $cmd[] = 'acceptEdits';
        $cmd[] = '--allowedTools';
        foreach (['Read', 'Edit', 'Write', 'Glob', 'Grep', 'Bash(git add:*)', 'Bash(git commit:*)', 'Bash(git status:*)', 'Bash(git diff:*)', 'Bash(git log:*)', 'Bash(git rm:*)', 'Bash(git mv:*)', 'Bash(git stash:*)', 'Bash(git show:*)', 'Bash(php:*)', 'Bash(docker exec:*)', 'Bash(rg:*)', 'Bash(grep:*)', 'Bash(ls:*)', 'Bash(find:*)', 'Bash(wc:*)', 'Bash(mkdir:*)', 'Bash(cat:*)', 'Bash(sed:*)', 'Bash(python3:*)', 'Bash(head:*)', 'Bash(tail:*)'] as $t) {
            $cmd[] = $t;
        }
    }
    if ($o['modelo'] !== null) {
        array_push($cmd, '--model', (string) $o['modelo']);
    }

    if ($dry) {
        out('    [dry-run] lanzaría: claude -p <prompt de ' . strlen($prompt) . ' bytes> --session-id ' . $sesion . ' ...');
        out('    ----- prompt -----');
        out('    ' . str_replace("\n", "\n    ", $prompt));
        $motivoFin = 'dry-run: solo se muestra el primer paso';
        break;
    }

    $pctAntes = $usado;
    $headAntes = git($repo, 'rev-parse', 'HEAD');
    $t0 = time();
    out('    lanzando claude (sesión ' . substr($sesion, 0, 8) . ')...');
    $r = ejecutar($cmd, $repo, (int) $o['timeout'] * 60);
    $dur = time() - $t0;

    $j = json_decode($r['out'], true);
    $j = is_array($j) ? $j : [];
    $texto = (string) ($j['result'] ?? '');
    $limite = (bool) preg_match('/usage limit|limit reached|rate.?limit|resets? (at|in)|límite/i', $texto . ' ' . $r['err'] . ' ' . ($r['out'] !== '' && $j === [] ? $r['out'] : ''))
        || ($j['api_error_status'] ?? null) === 429;

    // Consumo: modelUsage del JSON (total de la ejecución) y, como contraste, la transcripción.
    $in = $cw = $cr = $sal = 0;
    foreach ((array) ($j['modelUsage'] ?? []) as $mu) {
        $in += (int) ($mu['inputTokens'] ?? 0);
        $cw += (int) ($mu['cacheCreationInputTokens'] ?? 0);
        $cr += (int) ($mu['cacheReadInputTokens'] ?? 0);
        $sal += (int) ($mu['outputTokens'] ?? 0);
    }
    if ($in + $cw + $cr + $sal === 0 && isset($j['usage'])) {
        $in = (int) ($j['usage']['input_tokens'] ?? 0);
        $cw = (int) ($j['usage']['cache_creation_input_tokens'] ?? 0);
        $cr = (int) ($j['usage']['cache_read_input_tokens'] ?? 0);
        $sal = (int) ($j['usage']['output_tokens'] ?? 0);
    }
    $ueqJson = ueq($in, $cw, $cr, $sal);
    $ueqTrans = null;
    $med = ejecutar(['php', __DIR__ . '/medir_consumo_ia.php', '--session=' . $sesion, '--json', '--dir=' . (getenv('HOME') ?: '') . '/.claude/projects/' . preg_replace('/[^A-Za-z0-9]/', '-', $repo)], $repo);
    $tt = json_decode($med['out'], true);
    if (is_array($tt) && $tt !== []) {
        $ueqTrans = (int) array_sum(array_column($tt, 'ueq'));
    }

    // Veredicto objetivo: HEAD avanzó, árbol limpio y casilla marcada (salvo bloques de fase 4)
    $headDespues = git($repo, 'rev-parse', 'HEAD');
    $avanzo = $headDespues !== $headAntes;
    $limpio = sucio($repo) === [];
    $marcado = pasoMarcado($plan, $id);
    $res = 'ERROR';
    $detalle = '';
    if ($r['timeout']) {
        $res = 'TIMEOUT';
    } elseif ($limite && !$avanzo) {
        $res = 'LIMITE';
    } elseif (preg_match('/RESULTADO:\s*(OK|PARCIAL|BLOQUEADO)\s*:?\s*(.*)\s*$/u', $texto, $m) === 1) {
        $detalle = trim($m[2]);
        if ($m[1] === 'OK') {
            $res = $avanzo && $limpio && $marcado ? 'OK' : 'INCONSISTENTE';
            if ($res === 'INCONSISTENTE') {
                $detalle = 'dijo OK pero: ' . implode(', ', array_filter([!$avanzo ? 'no hay commit nuevo' : '', !$limpio ? 'quedan cambios sin commitear' : '', !$marcado ? 'casilla sin marcar' : '']));
            }
        } else {
            $res = $m[1];
        }
    } elseif ($j === []) {
        $detalle = trim(mb_substr($r['err'] . ' ' . $r['out'], 0, 300));
    }

    $pctDespues = null;
    if ($res === 'OK' && $esTty) {
        $pctDespues = parsearPct(preguntar('¿Qué % semanal llevas usado ahora? (Enter = no lo sé)', $esTty));
        if ($pctDespues !== null) {
            guardarEstado($ficheroEstado, $pctDespues);
        }
    }

    $reg = [
        'ts' => ahora()->format('c'), 'id' => $id, 'resultado' => $res, 'detalle' => $detalle,
        'ueq' => $ueqJson, 'ueq_transcripcion' => $ueqTrans, 'coste_usd' => $j['total_cost_usd'] ?? null,
        'turnos' => $j['num_turns'] ?? null, 'duracion_s' => $dur, 'ent' => $in, 'cache_w' => $cw, 'cache_r' => $cr, 'sal' => $sal,
        'estimado' => $estU, 'pct_antes' => $pctDespues !== null ? $pctAntes : null, 'pct_despues' => $pctDespues,
        'limite' => $limite, 'sesion' => $sesion, 'commit' => $headDespues, 'rama' => git($repo, 'rev-parse', '--abbrev-ref', 'HEAD'),
        'denegaciones' => count((array) ($j['permission_denials'] ?? [])),
    ];
    anotarLog($ficheroLog, $reg);
    $log[] = $reg;

    out(sprintf(
        '    %s en %ds: %s u.eq. (estimado %s)%s%s',
        $res,
        $dur,
        number_format($ueqJson, 0, ',', '.'),
        number_format($estU, 0, ',', '.'),
        $ueqTrans !== null ? ' [transcripción: ' . number_format($ueqTrans, 0, ',', '.') . ']' : '',
        $detalle !== '' ? " — $detalle" : ''
    ));
    if ($reg['denegaciones'] > 0) {
        out("    AVISO: {$reg['denegaciones']} herramienta(s) denegada(s) por permisos; revisa la lista o usa --permisos=bypass.");
    }

    // Actualizar % usado
    $ppu = calibracion($log) ?? $ppu;
    if ($pctDespues !== null) {
        $usado = $pctDespues;
    } elseif ($usado !== null && $ppu !== null) {
        $usado += $ueqJson * $ppu;
    }

    if ($res !== 'OK') {
        $motivoFin = "paso $id terminó en $res" . ($detalle !== '' ? ": $detalle" : '');
        if ($res === 'LIMITE') {
            $motivoFin = 'cupo agotado (claude avisa de límite)';
        }
        break;
    }
    $hechos++;
    $sucio = [];
    if ($ultimoIntento === $id) {
        $motivoFin = "el paso $id sigue pendiente tras un OK: sin progreso";
        break;
    }
    $ultimoIntento = $id;
}

// ---------------------------------------------------------------------------------------------
// Resumen
// ---------------------------------------------------------------------------------------------

out("\n== Fin: $motivoFin");
out("Pasos completados en esta ejecución: $hechos");
if ($usado !== null) {
    out(sprintf('Uso semanal estimado ahora: %.1f %%', $usado));
}
$pend = siguientePaso($plan, $o['fase']);
out($pend !== null ? "Siguiente paso pendiente: {$pend['id']}" : 'No quedan pasos pendientes' . ($o['fase'] !== null ? " en la fase {$o['fase']}" : ''));
out('Registro de consumo: ' . str_replace($repo . '/', '', $ficheroLog));
exit(0);
