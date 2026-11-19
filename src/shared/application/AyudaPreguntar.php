<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\config\ConfigGlobal;

/**
 * Busca en la documentación local (`docs/guias`, `docs/ai`, `docs/manual`) sin LLM ni red.
 *
 * Puntuación léxica (título, preguntas, cuerpo). Devuelve fragmentos útiles y una respuesta
 * armada con los mejores extractos.
 *
 * Las guías de proceso (`docs/guias/<area>/*.md`, `tipo: guia_proceso`) son las que explican
 * «cómo se hace» una tarea. Cada apartado (`##`, `###`, `####`) es un resultado propio y trae
 * sus pasos completos en `pasos`; si el mejor resultado es de una guía, la respuesta son esos
 * pasos. La parte «Para mantenimiento (técnico)» no se busca. Las preguntas a las que responde
 * un apartado se declaran bajo su título con `<!-- preguntas: "…" | "…" -->` y las de la guía
 * entera en `preguntas: [...]` de la cabecera.
 *
 * @phpstan-type Doc array{titulo: string, tipo: string, modulo: string, fuente: string, cuerpo: string, preguntas: list<string>, pasos: string, titulo_propio: string}
 * @phpstan-type DocIndexado array{doc: Doc, titulo: array<string, true>, padres: array<string, true>, preguntas: array<string, true>, cuerpo: array<string, true>, texto: string}
 */
final class AyudaPreguntar
{
    private const STOPWORDS = [
        'a', 'al', 'de', 'del', 'el', 'la', 'las', 'los', 'un', 'una', 'unos', 'unas',
        'y', 'o', 'u', 'en', 'por', 'para', 'con', 'sin', 'sobre', 'como', 'qué', 'que',
        'cómo', 'cual', 'cuál', 'cuales', 'cuáles', 'donde', 'dónde', 'cuando', 'cuándo',
        'es', 'son', 'se', 'me', 'te', 'le', 'lo', 'mi', 'tu', 'su', 'hay', 'hacer',
        'hago', 'puedo', 'puede', 'pueden', 'quiero', 'necesito', 'the', 'and', 'or',
        'to', 'of', 'in', 'for', 'is', 'are', 'how', 'what', 'where',
    ];

    /** Una guía revisada a mano pesa más que una ficha generada con la misma coincidencia. */
    private const FACTOR_GUIA = 1.5;

    /** Largo máximo de los pasos que se copian en la respuesta. */
    private const MAX_PASOS_RESPUESTA = 3000;

    /** Sufijos que se quitan para comparar «matriculo», «matricular» y «matrículas». */
    private const SUFIJOS = [
        'aciones', 'acion', 'ciones', 'cion', 'mente', 'iendo', 'ando', 'amos', 'emos', 'imos',
        'adas', 'ados', 'idas', 'idos', 'ada', 'ado', 'ida', 'ido',
        'ar', 'er', 'ir', 'an', 'en', 'as', 'es', 'os', 'a', 'e', 'o', 's',
    ];

    /** @var list<DocIndexado>|null */
    private ?array $documentos = null;

    public function __construct(
        private readonly ?string $docsRoot = null,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *     pregunta: string,
     *     respuesta: string,
     *     modo: string,
     *     resultados: list<array{
     *         titulo: string,
     *         tipo: string,
     *         modulo: string,
     *         fuente: string,
     *         excerpt: string,
     *         score: float,
     *         preguntas: list<string>,
     *         pasos: string
     *     }>
     * }
     */
    public function execute(array $input): array
    {
        $pregunta = trim(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'pregunta'));
        if ($pregunta === '') {
            $pregunta = trim(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'q'));
        }
        $limite = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'limite');
        if ($limite < 1) {
            $limite = 8;
        }
        if ($limite > 20) {
            $limite = 20;
        }

        if ($pregunta === '') {
            return [
                'pregunta' => '',
                'respuesta' => _("Escriba una pregunta sobre Orbix (por ejemplo: «cómo crear un acta»)."),
                'modo' => 'busqueda_local',
                'resultados' => [],
            ];
        }

        $tokens = $this->tokenize($pregunta);
        if ($tokens === []) {
            return [
                'pregunta' => $pregunta,
                'respuesta' => _("No hay términos suficientes para buscar. Pruebe con otras palabras."),
                'modo' => 'busqueda_local',
                'resultados' => [],
            ];
        }

        $root = $this->resolveDocsRoot();
        if ($root === null) {
            return [
                'pregunta' => $pregunta,
                'respuesta' => _("No se encuentra la carpeta de documentación en el servidor."),
                'modo' => 'busqueda_local',
                'resultados' => [],
            ];
        }

        $hits = [];
        foreach ($this->documentos($root) as $indexado) {
            $doc = $indexado['doc'];
            $score = $this->puntuar($indexado, $tokens, $pregunta);
            if ($score <= 0.0) {
                continue;
            }
            $hits[] = [
                'titulo' => $doc['titulo'],
                'tipo' => $doc['tipo'],
                'modulo' => $doc['modulo'],
                'fuente' => $doc['fuente'],
                'excerpt' => $this->excerpt($doc['cuerpo'], $tokens),
                'score' => round($score, 2),
                'preguntas' => $doc['preguntas'],
                'pasos' => $doc['pasos'],
            ];
        }

        usort($hits, static fn(array $a, array $b): int => $b['score'] <=> $a['score']);
        $hits = array_slice($hits, 0, $limite);

        return [
            'pregunta' => $pregunta,
            'respuesta' => $this->componerRespuesta($pregunta, $hits),
            'modo' => 'busqueda_local',
            'resultados' => $hits,
        ];
    }

    /**
     * Los documentos con sus palabras ya separadas; se leen una vez por instancia.
     *
     * @return list<DocIndexado>
     */
    private function documentos(string $docsRoot): array
    {
        if ($this->documentos === null) {
            $this->documentos = [];
            foreach ($this->iterarDocumentos($docsRoot) as $doc) {
                $this->documentos[] = [
                    'doc' => $doc,
                    'titulo' => array_fill_keys($this->tokenize($doc['titulo_propio']), true),
                    'padres' => array_fill_keys($this->tokenize($doc['titulo']), true),
                    'preguntas' => array_fill_keys($this->tokenize(implode(' ', $doc['preguntas'])), true),
                    'cuerpo' => array_fill_keys($this->tokenize(mb_substr($doc['cuerpo'], 0, 12000, 'UTF-8')), true),
                    'texto' => preg_replace('/\s+/', ' ', $this->normalizar($doc['cuerpo'])) ?? '',
                ];
            }
        }

        return $this->documentos;
    }

    private function resolveDocsRoot(): ?string
    {
        if ($this->docsRoot !== null && $this->docsRoot !== '') {
            $real = realpath($this->docsRoot);
            return ($real !== false && is_dir($real)) ? $real : null;
        }
        $base = (string) (ConfigGlobal::$directorio ?? '');
        if ($base === '') {
            return null;
        }
        $docs = realpath(rtrim($base, '/\\') . '/docs');
        return ($docs !== false && is_dir($docs)) ? $docs : null;
    }

    /**
     * @return \Generator<int, Doc>
     */
    private function iterarDocumentos(string $docsRoot): \Generator
    {
        foreach (glob($docsRoot . '/guias/*/*.md') ?: [] as $path) {
            if (str_starts_with(basename($path), '_')) {
                continue;
            }
            $rel = 'docs/guias/' . basename(dirname($path)) . '/' . basename($path);
            foreach ($this->leerGuia($path, $rel) as $doc) {
                yield $doc;
            }
        }

        $manualDir = $docsRoot . '/manual';
        if (is_dir($manualDir)) {
            foreach (glob($manualDir . '/*.md') ?: [] as $path) {
                $slug = basename($path, '.md');
                if (!preg_match('/^[a-z][a-z0-9_]*$/', $slug)) {
                    continue;
                }
                $doc = $this->leerMarkdown($path, 'manual', $slug, 'docs/manual/' . $slug . '.md');
                if ($doc !== null) {
                    yield $doc;
                }
            }
        }

        $aiDir = $docsRoot . '/ai';
        if (!is_dir($aiDir)) {
            return;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($aiDir, \FilesystemIterator::SKIP_DOTS)
        );
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with(strtolower($file->getFilename()), '.md')) {
                continue;
            }
            $real = $file->getRealPath();
            if ($real === false) {
                continue;
            }
            $rel = 'docs/ai/' . ltrim(str_replace('\\', '/', substr($real, strlen($aiDir))), '/');
            $parts = explode('/', $rel);
            $modulo = $parts[2] ?? 'ai';
            $doc = $this->leerMarkdown($real, 'ai', $modulo, $rel);
            if ($doc !== null) {
                yield $doc;
            }
        }
    }

    /**
     * @return Doc|null
     */
    private function leerMarkdown(string $path, string $tipo, string $modulo, string $fuente): ?array
    {
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return null;
        }
        $meta = $this->parseFrontmatter($raw);
        $cuerpo = $meta['cuerpo'];
        $titulo = $meta['titulo'];
        if ($titulo === '') {
            if (preg_match('/^#\s+(.+)$/m', $cuerpo, $m) === 1) {
                $titulo = trim($m[1]);
            } else {
                $titulo = basename($path, '.md');
            }
        }

        return [
            'titulo' => $titulo,
            'tipo' => $tipo,
            'modulo' => $modulo,
            'fuente' => $fuente,
            'cuerpo' => $cuerpo,
            'preguntas' => $meta['preguntas'],
            'pasos' => '',
            'titulo_propio' => $titulo,
        ];
    }

    /**
     * Parte una guía de proceso en apartados; cada uno es un documento del buscador.
     *
     * - La presentación (todo lo que no es «Pasos» ni «Errores frecuentes») va junta, con el
     *   título de la guía y las preguntas de la cabecera.
     * - Cada `###` y `####` es un apartado; su título lleva delante los de sus padres, y un `###`
     *   trae también el texto de sus `####`.
     * - «Procesos relacionados» y «Para mantenimiento (técnico)» no se buscan.
     *
     * @return list<Doc>
     */
    private function leerGuia(string $path, string $fuente): array
    {
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return [];
        }
        if (preg_match('/\A---\r?\n(.*?)\r?\n---\r?\n/s', $raw, $m) !== 1
            || preg_match('/^tipo:\s*"?guia_proceso"?\s*$/m', $m[1]) !== 1) {
            return [];
        }
        $meta = $this->parseFrontmatter($raw);
        $guia = $meta['titulo'] !== '' ? $meta['titulo'] : basename($path, '.md');
        $area = preg_match('/^area:\s*"?([a-z_]+)"?/m', $m[1], $a) === 1 ? $a[1] : basename(dirname($path));

        $cuerpo = preg_replace('/^## Para mantenimiento.*\z/ms', '', $meta['cuerpo']) ?? $meta['cuerpo'];

        // Por clave de apartado: título completo, título propio y líneas de texto.
        /** @var array<string, string> $titulos */
        $titulos = ['' => $guia];
        /** @var array<string, string> $propios */
        $propios = ['' => $guia];
        /** @var array<string, list<string>> $lineasDe */
        $lineasDe = ['' => []];
        $actual = '';
        $h2 = '';
        $h3 = '';
        $padre = null;
        $enCodigo = false;
        foreach (preg_split('/\r?\n/', $cuerpo) ?: [] as $linea) {
            if (str_starts_with(ltrim($linea), '```')) {
                $enCodigo = !$enCodigo;
            }
            if (!$enCodigo && preg_match('/^(#{1,4})\s+(.+?)\s*$/', $linea, $h) === 1) {
                $nivel = strlen($h[1]);
                $texto = $h[2];
                if ($nivel === 1) {
                    continue;
                }
                if ($nivel === 2) {
                    $h2 = $texto;
                    $h3 = '';
                    $padre = null;
                    if ($this->esApartadoDePasos($texto)) {
                        $actual = 'h2:' . $texto;
                        $titulos[$actual] ??= $guia . ' › ' . $texto;
                        $propios[$actual] ??= $texto;
                        $lineasDe[$actual] ??= [];
                    } elseif ($this->esApartadoSinBuscar($texto)) {
                        $actual = '-';
                    } else {
                        $actual = '';
                        $lineasDe[''][] = '';
                        $lineasDe[''][] = '**' . $texto . '**';
                    }
                    continue;
                }
                if ($actual === '-') {
                    continue;
                }
                if ($nivel === 3) {
                    $h3 = $texto;
                    $titulo = $guia . ' › ' . $texto;
                } else {
                    $titulo = $guia . ($h3 !== '' ? ' › ' . $h3 : '') . ' › ' . $texto;
                }
                $padre = $nivel === 4 && $h3 !== '' ? $h2 . '/' . $h3 . '/' : null;
                $actual = $h2 . '/' . $h3 . '/' . ($nivel === 4 ? $texto : '');
                $titulos[$actual] ??= $titulo;
                $propios[$actual] ??= $texto;
                $lineasDe[$actual] ??= [];
                if ($padre !== null && isset($lineasDe[$padre])) {
                    // El apartado padre trae también los pasos de sus subapartados.
                    $lineasDe[$padre][] = '';
                    $lineasDe[$padre][] = '**' . $texto . '**';
                }
                continue;
            }
            if ($actual === '-') {
                continue;
            }
            $lineasDe[$actual][] = $linea;
            if ($padre !== null && isset($lineasDe[$padre]) && preg_match('/^\s*<!--\s*preguntas:/', $linea) !== 1) {
                $lineasDe[$padre][] = $linea;
            }
        }

        $docs = [];
        foreach ($lineasDe as $clave => $lineasApartado) {
            $preguntas = $clave === '' ? $meta['preguntas'] : [];
            $lineas = [];
            foreach ($lineasApartado as $linea) {
                if (preg_match('/^\s*<!--\s*preguntas:\s*(.*?)\s*-->\s*$/', $linea, $p) === 1) {
                    foreach (explode('|', $p[1]) as $q) {
                        // Por caracteres y no por bytes: `trim()` con «» se llevaba medio «¿» inicial.
                        $q = preg_replace('/^[\s"«»]+|[\s"«»]+$/u', '', $q) ?? '';
                        if ($q !== '') {
                            $preguntas[] = $q;
                        }
                    }
                    continue;
                }
                $lineas[] = $linea;
            }
            $texto = trim(preg_replace('/\n{3,}/', "\n\n", implode("\n", $lineas)) ?? '');
            $texto = trim(preg_replace('/\n---\s*\z/', '', $texto) ?? $texto);
            if ($texto === '') {
                continue;
            }
            $docs[] = [
                'titulo' => $titulos[$clave] ?? $guia,
                'tipo' => 'guia',
                'modulo' => $area,
                'fuente' => $fuente,
                'cuerpo' => $texto,
                'preguntas' => $preguntas,
                'pasos' => $texto,
                'titulo_propio' => $propios[$clave] ?? $guia,
            ];
        }

        return $docs;
    }

    private function esApartadoDePasos(string $h2): bool
    {
        return in_array(mb_strtolower($h2, 'UTF-8'), ['pasos', 'errores frecuentes'], true);
    }

    private function esApartadoSinBuscar(string $h2): bool
    {
        return mb_strtolower($h2, 'UTF-8') === 'procesos relacionados';
    }

    /**
     * @return array{titulo: string, preguntas: list<string>, cuerpo: string}
     */
    private function parseFrontmatter(string $raw): array
    {
        $titulo = '';
        $preguntas = [];
        $cuerpo = $raw;
        if (preg_match('/\A---\r?\n(.*?)\r?\n---\r?\n(.*)\z/s', $raw, $m) === 1) {
            $fm = $m[1];
            $cuerpo = $m[2];
            if (preg_match('/^titulo:\s*"?([^"\n]+)"?/m', $fm, $t) === 1) {
                $titulo = trim($t[1]);
            }
            if (preg_match('/^preguntas:\s*\[(.*?)\]/ms', $fm, $p) === 1) {
                if (preg_match_all('/"([^"]+)"/', $p[1], $qs) > 0) {
                    foreach ($qs[1] as $q) {
                        $preguntas[] = $q;
                    }
                }
            }
        }

        return ['titulo' => $titulo, 'preguntas' => $preguntas, 'cuerpo' => $cuerpo];
    }

    /**
     * @return list<string>
     */
    private function tokenize(string $text): array
    {
        $parts = preg_split('/[^a-z0-9_]+/u', $this->normalizar($text)) ?: [];
        $out = [];
        $stop = array_fill_keys(self::STOPWORDS, true);
        foreach ($parts as $part) {
            if (strlen($part) < 2 || isset($stop[$part])) {
                continue;
            }
            $out[$this->raiz($part)] = true;
        }

        return array_map('strval', array_keys($out));
    }

    /** Raíz aproximada de una palabra: sin el sufijo de género, número o conjugación. */
    private function raiz(string $palabra): string
    {
        if (strlen($palabra) < 5) {
            return $palabra;
        }
        foreach (self::SUFIJOS as $sufijo) {
            if (str_ends_with($palabra, $sufijo) && strlen($palabra) - strlen($sufijo) >= 4) {
                return substr($palabra, 0, -strlen($sufijo));
            }
        }

        return $palabra;
    }

    /** Minúsculas y sin acentos. */
    private function normalizar(string $text): string
    {
        $norm = mb_strtolower($text, 'UTF-8');

        return strtr($norm, [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c',
        ]);
    }

    /**
     * @param DocIndexado $indexado
     * @param list<string> $tokens
     */
    private function puntuar(array $indexado, array $tokens, string $pregunta): float
    {
        $tituloSet = $indexado['titulo'];
        $padresSet = $indexado['padres'];
        $pregSet = $indexado['preguntas'];
        $cuerpoSet = $indexado['cuerpo'];

        $score = 0.0;
        foreach ($tokens as $t) {
            // Título y preguntas no se suman: las fichas generadas repiten el título como pregunta.
            if (isset($pregSet[$t])) {
                $score += 5.0;
            } elseif (isset($tituloSet[$t])) {
                $score += 4.0;
            } elseif (isset($padresSet[$t])) {
                // título de la guía o del apartado padre
                $score += 2.0;
            }
            if (isset($cuerpoSet[$t])) {
                $score += 1.0;
            }
        }

        // Bonus si coinciden varios términos
        $matched = 0;
        foreach ($tokens as $t) {
            if (isset($tituloSet[$t]) || isset($padresSet[$t]) || isset($pregSet[$t]) || isset($cuerpoSet[$t])) {
                $matched++;
            }
        }
        if ($matched >= 2) {
            $score += $matched * 0.5;
        }

        // La pregunta tal cual dentro del texto (p. ej. un mensaje de error copiado de la pantalla).
        $frase = trim(preg_replace('/\s+/', ' ', $this->normalizar($pregunta)) ?? '');
        if (count($tokens) >= 3 && $score > 0.0
            && $frase !== '' && str_contains($indexado['texto'], $frase)) {
            $score += 8.0;
        }

        if ($indexado['doc']['tipo'] === 'guia') {
            $score *= self::FACTOR_GUIA;
        }

        return $score;
    }

    /**
     * @param list<string> $tokens
     */
    private function excerpt(string $cuerpo, array $tokens): string
    {
        $plain = preg_replace('/^#+\s*/m', '', $cuerpo) ?? $cuerpo;
        $plain = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $plain) ?? $plain;
        $plain = preg_replace('/[`*_>#\-]+/', ' ', $plain) ?? $plain;
        $plain = preg_replace('/\s+/u', ' ', $plain) ?? $plain;
        $plain = trim($plain);

        $lower = mb_strtolower($plain, 'UTF-8');
        $pos = false;
        foreach ($tokens as $t) {
            $p = mb_strpos($lower, $t, 0, 'UTF-8');
            if ($p !== false && ($pos === false || $p < $pos)) {
                $pos = $p;
            }
        }
        if ($pos === false) {
            $pos = 0;
        }
        $start = max(0, $pos - 80);
        $slice = mb_substr($plain, $start, 280, 'UTF-8');
        if ($start > 0) {
            $slice = '…' . $slice;
        }
        if (mb_strlen($plain, 'UTF-8') > $start + 280) {
            $slice .= '…';
        }

        return $slice;
    }

    /**
     * @param list<array{titulo: string, tipo: string, modulo: string, excerpt: string, score: float, pasos: string}> $hits
     */
    private function componerRespuesta(string $pregunta, array $hits): string
    {
        if ($hits === []) {
            return sprintf(
                _("No se han encontrado pasajes claros para «%s». Pruebe con el nombre del módulo o de la pantalla (acta, planning, encargos…)."),
                $pregunta
            );
        }

        if ($hits[0]['tipo'] === 'guia') {
            return $this->componerRespuestaDeGuia($hits);
        }

        $lineas = [];
        $lineas[] = _("Según la documentación local (sin IA generativa):");
        $n = 0;
        foreach ($hits as $hit) {
            $n++;
            if ($n > 3) {
                break;
            }
            $lineas[] = sprintf('%d. [%s] %s — %s', $n, $this->donde($hit), $hit['titulo'], $hit['excerpt']);
        }
        $lineas[] = _("Abra el manual del módulo o refine la pregunta para más detalle.");

        return implode("\n", $lineas);
    }

    /**
     * El mejor resultado es un apartado de una guía de proceso: la respuesta son sus pasos.
     *
     * @param list<array{titulo: string, tipo: string, modulo: string, excerpt: string, score: float, pasos: string}> $hits
     */
    private function componerRespuestaDeGuia(array $hits): string
    {
        $mejor = $hits[0];
        $pasos = $this->textoPlano($mejor['pasos']);
        if (mb_strlen($pasos, 'UTF-8') > self::MAX_PASOS_RESPUESTA) {
            $pasos = mb_substr($pasos, 0, self::MAX_PASOS_RESPUESTA, 'UTF-8') . '…';
        }

        $lineas = [];
        $lineas[] = sprintf(_("Según la guía «%s» (documentación local, sin IA generativa):"), $mejor['titulo']);
        $lineas[] = '';
        $lineas[] = $pasos;

        $otros = [];
        foreach (array_slice($hits, 1, 3) as $hit) {
            $otros[] = sprintf('- [%s] %s', $this->donde($hit), $hit['titulo']);
        }
        if ($otros !== []) {
            $lineas[] = '';
            $lineas[] = _("También puede interesarle:");
            array_push($lineas, ...$otros);
        }

        return implode("\n", $lineas);
    }

    /** @param array{tipo: string, modulo: string} $hit */
    private function donde(array $hit): string
    {
        return match ($hit['tipo']) {
            'manual' => sprintf(_("Manual · %s"), $hit['modulo']),
            'guia' => sprintf(_("Guía · %s"), $hit['modulo']),
            default => sprintf(_("Ayuda · %s"), $hit['modulo']),
        };
    }

    /** Markdown de una guía como texto legible sin marcas. */
    private function textoPlano(string $markdown): string
    {
        $t = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $markdown) ?? $markdown;
        $t = preg_replace('/^>\s?/m', '', $t) ?? $t;
        $t = str_replace(['**', '`'], '', $t);
        $t = preg_replace('/\*([^*\n]+)\*/', '$1', $t) ?? $t;
        $t = preg_replace('/^#{1,6}\s*/m', '', $t) ?? $t;

        return trim($t);
    }
}
