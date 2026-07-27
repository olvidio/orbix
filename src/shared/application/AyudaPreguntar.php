<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\config\ConfigGlobal;

/**
 * Busca en la documentación local (`docs/ai`, `docs/manual`) sin LLM ni red.
 *
 * Puntuación léxica (título, preguntas del front matter, cuerpo). Devuelve
 * fragmentos útiles y una respuesta corta armada con los mejores extractos.
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
     *         preguntas: list<string>
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
                'respuesta' => _('Escriba una pregunta sobre Orbix (por ejemplo: «cómo crear un acta»).'),
                'modo' => 'busqueda_local',
                'resultados' => [],
            ];
        }

        $tokens = $this->tokenize($pregunta);
        if ($tokens === []) {
            return [
                'pregunta' => $pregunta,
                'respuesta' => _('No hay términos suficientes para buscar. Pruebe con otras palabras.'),
                'modo' => 'busqueda_local',
                'resultados' => [],
            ];
        }

        $root = $this->resolveDocsRoot();
        if ($root === null) {
            return [
                'pregunta' => $pregunta,
                'respuesta' => _('No se encuentra la carpeta de documentación en el servidor.'),
                'modo' => 'busqueda_local',
                'resultados' => [],
            ];
        }

        $hits = [];
        foreach ($this->iterarDocumentos($root) as $doc) {
            $score = $this->puntuar($doc, $tokens);
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
     * @return \Generator<int, array{
     *     titulo: string,
     *     tipo: string,
     *     modulo: string,
     *     fuente: string,
     *     cuerpo: string,
     *     preguntas: list<string>
     * }>
     */
    private function iterarDocumentos(string $docsRoot): \Generator
    {
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
     * @return array{
     *     titulo: string,
     *     tipo: string,
     *     modulo: string,
     *     fuente: string,
     *     cuerpo: string,
     *     preguntas: list<string>
     * }|null
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
        ];
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
        $norm = mb_strtolower($text, 'UTF-8');
        $norm = strtr($norm, [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c',
        ]);
        $parts = preg_split('/[^a-z0-9_]+/u', $norm) ?: [];
        $out = [];
        $stop = array_fill_keys(self::STOPWORDS, true);
        foreach ($parts as $part) {
            if (strlen($part) < 2 || isset($stop[$part])) {
                continue;
            }
            $out[$part] = true;
        }

        return array_keys($out);
    }

    /**
     * @param array{titulo: string, cuerpo: string, preguntas: list<string>} $doc
     * @param list<string> $tokens
     */
    private function puntuar(array $doc, array $tokens): float
    {
        $tituloTok = $this->tokenize($doc['titulo']);
        $pregTok = $this->tokenize(implode(' ', $doc['preguntas']));
        $cuerpoTok = $this->tokenize(mb_substr($doc['cuerpo'], 0, 12000, 'UTF-8'));

        $score = 0.0;
        $tituloSet = array_fill_keys($tituloTok, true);
        $pregSet = array_fill_keys($pregTok, true);
        $cuerpoSet = array_fill_keys($cuerpoTok, true);

        foreach ($tokens as $t) {
            if (isset($tituloSet[$t])) {
                $score += 4.0;
            }
            if (isset($pregSet[$t])) {
                $score += 5.0;
            }
            if (isset($cuerpoSet[$t])) {
                $score += 1.0;
            }
        }

        // Bonus si coinciden varios términos
        $matched = 0;
        foreach ($tokens as $t) {
            if (isset($tituloSet[$t]) || isset($pregSet[$t]) || isset($cuerpoSet[$t])) {
                $matched++;
            }
        }
        if ($matched >= 2) {
            $score += $matched * 0.5;
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
     * @param list<array{titulo: string, tipo: string, modulo: string, excerpt: string, score: float}> $hits
     */
    private function componerRespuesta(string $pregunta, array $hits): string
    {
        if ($hits === []) {
            return sprintf(
                _('No se han encontrado pasajes claros para «%s». Pruebe con el nombre del módulo o de la pantalla (acta, planning, encargos…).'),
                $pregunta
            );
        }

        $lineas = [];
        $lineas[] = _('Según la documentación local (sin IA generativa):');
        $n = 0;
        foreach ($hits as $hit) {
            $n++;
            if ($n > 3) {
                break;
            }
            $donde = $hit['tipo'] === 'manual'
                ? sprintf(_('Manual · %s'), $hit['modulo'])
                : sprintf(_('Ayuda · %s'), $hit['modulo']);
            $lineas[] = sprintf('%d. [%s] %s — %s', $n, $donde, $hit['titulo'], $hit['excerpt']);
        }
        $lineas[] = _('Abra el manual del módulo o refine la pregunta para más detalle.');

        return implode("\n", $lineas);
    }
}
