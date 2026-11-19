#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * ¿Responde la ayuda a las preguntas de los usuarios?
 *
 * Pasa cada pregunta de `tools/audit/ayuda_preguntas.tsv` por el buscador de la ayuda
 * (`src\shared\application\AyudaPreguntar`, el de la pantalla «Preguntar a la documentación»)
 * y comprueba que la guía de proceso esperada sale entre los primeros resultados.
 *
 * Uso (desde la raíz del repo):
 *   php tools/audit/ayuda_preguntas.php              # resumen y lista de las que fallan
 *   php tools/audit/ayuda_preguntas.php --todas      # lista todas, con lo que responde cada una
 *   php tools/audit/ayuda_preguntas.php --strict     # exit 1 si alguna no sale la primera
 *   php tools/audit/ayuda_preguntas.php "como apunto a alguien"   # ver qué responde a una pregunta
 *
 * Resultado por pregunta:
 *   OK   la guía (y el apartado, si se indica) es el primer resultado: sus pasos son la respuesta
 *   CASI sale entre los 3 primeros, pero no la primera
 *   NO   no sale entre los 3 primeros
 *
 * Una pregunta que falla se arregla en la guía (docs/guias): añadiendo el apartado que falta o
 * declarando la pregunta bajo su título con `<!-- preguntas: "…" | "…" -->`.
 * Ver docs/guias/CONVENCIONES.md.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
require $root . '/libs/vendor/autoload.php';

$args = array_slice($argv, 1);
$todas = in_array('--todas', $args, true);
$strict = in_array('--strict', $args, true);
$sueltas = array_values(array_filter($args, static fn (string $a): bool => !str_starts_with($a, '--')));

$ayuda = new src\shared\application\AyudaPreguntar($root . '/docs');

if ($sueltas !== []) {
    foreach ($sueltas as $pregunta) {
        $r = $ayuda->execute(['pregunta' => $pregunta, 'limite' => 5]);
        echo "### $pregunta\n";
        foreach ($r['resultados'] as $h) {
            printf("  %5.1f  %-6s %s  «%s»\n", $h['score'], $h['tipo'], $h['fuente'], $h['titulo']);
        }
        echo "\n" . $r['respuesta'] . "\n\n";
    }
    exit(0);
}

$sinAcentos = static fn (string $s): string => strtr(mb_strtolower($s, 'UTF-8'), [
    'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
]);

$cuenta = ['OK' => 0, 'CASI' => 0, 'NO' => 0];
$lineas = file(__DIR__ . '/ayuda_preguntas.tsv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
foreach ($lineas as $linea) {
    if (str_starts_with($linea, '#')) {
        continue;
    }
    [$pregunta, $guia, $apartado] = array_pad(explode("\t", $linea), 3, '');
    $fuente = 'docs/guias/' . trim($guia) . '.md';
    $apartado = $sinAcentos(trim($apartado));

    $hits = $ayuda->execute(['pregunta' => $pregunta, 'limite' => 3])['resultados'];
    $posicion = 0;
    foreach ($hits as $i => $h) {
        if ($h['fuente'] === $fuente && ($apartado === '' || str_contains($sinAcentos($h['titulo']), $apartado))) {
            $posicion = $i + 1;
            break;
        }
    }
    $estado = $posicion === 1 ? 'OK' : ($posicion > 1 ? 'CASI' : 'NO');
    $cuenta[$estado]++;
    if ($todas || $estado !== 'OK') {
        $primero = $hits[0] ?? null;
        printf(
            "%-4s %s\n       espera: %s%s\n       sale:   %s\n",
            $estado,
            $pregunta,
            $guia,
            $apartado !== '' ? " › …{$apartado}…" : '',
            $primero === null ? '(nada)' : str_replace('docs/', '', $primero['fuente']) . ' «' . $primero['titulo'] . '»'
        );
    }
}

$total = array_sum($cuenta);
printf("\n%d preguntas: %d OK, %d CASI, %d NO\n", $total, $cuenta['OK'], $cuenta['CASI'], $cuenta['NO']);
exit($strict && $cuenta['OK'] < $total ? 1 : 0);
