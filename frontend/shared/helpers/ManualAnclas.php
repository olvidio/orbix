<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

/**
 * Anclas (`id`) para los encabezados del manual de usuario renderizado desde markdown.
 *
 * Parsedown no genera `id` en `<h1>`…`<h6>`; sin ellos no se puede enlazar a una sección concreta
 * del manual (p. ej. desde el panel de ayuda contextual). `frontend/shared/controller/manual.php`
 * inyecta los ids con {@see self::procesar()} y `tools/qa/generar_indice_ayuda.php` usa la misma
 * función sobre el mismo HTML, de modo que ambos acuerdan siempre el id de cada encabezado.
 *
 * Algoritmo del id: texto visible del encabezado → minúsculas sin acentos → todo lo que no sea
 * `[a-z0-9]` pasa a `-`. Si el id ya se ha usado antes en el documento (en orden de aparición) se
 * le añade `-2`, `-3`, …
 */
final class ManualAnclas
{
    private const ACENTOS = [
        'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a',
        'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
        'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
        'ñ' => 'n', 'ç' => 'c', 'ß' => 'ss',
    ];

    public static function slug(string $texto): string
    {
        $limpio = html_entity_decode(strip_tags($texto), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $limpio = strtr(mb_strtolower($limpio, 'UTF-8'), self::ACENTOS);
        $slug = trim((string)preg_replace('/[^a-z0-9]+/', '-', $limpio), '-');
        return $slug !== '' ? $slug : 'seccion';
    }

    /**
     * @return array{html: string, encabezados: list<array{nivel: int, texto: string, id: string}>}
     *         `html` con `id` en cada encabezado que no lo traía; `encabezados` en orden de aparición.
     */
    public static function procesar(string $html): array
    {
        $usados = [];
        $encabezados = [];
        $resultado = preg_replace_callback(
            '~<h([1-6])(\s[^>]*)?>(.*?)</h\1>~su',
            static function (array $m) use (&$usados, &$encabezados): string {
                $nivel = (int)$m[1];
                $atributos = $m[2] ?? '';
                $interior = $m[3];
                $texto = trim(html_entity_decode(strip_tags($interior), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                if (preg_match('/\sid=["\']([^"\']+)["\']/', $atributos, $existente) === 1) {
                    $id = $existente[1];
                    $usados[$id] = 1;
                    $encabezados[] = ['nivel' => $nivel, 'texto' => $texto, 'id' => $id];
                    return $m[0];
                }

                $base = self::slug($interior);
                $id = $base;
                $n = 1;
                while (isset($usados[$id])) {
                    $n++;
                    $id = $base . '-' . $n;
                }
                $usados[$id] = 1;
                $encabezados[] = ['nivel' => $nivel, 'texto' => $texto, 'id' => $id];

                return '<h' . $nivel . $atributos . ' id="' . $id . '">' . $interior . '</h' . $nivel . '>';
            },
            $html,
        );

        return ['html' => $resultado ?? $html, 'encabezados' => $encabezados];
    }
}
