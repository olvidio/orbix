<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Ayuda contextual de una pantalla: tareas del manual que la explican.
 *
 * Lee el índice `docs/manual/_indice_pantallas.json` (lo genera
 * `docs/scripts/generar_indice_ayuda.php`) y devuelve, para el controller de frontend que
 * está viendo el usuario, su título y las secciones del manual con su ancla. Solo lectura.
 *
 * El input `controller` puede ser la ruta `frontend/<mod>/controller/<x>.php` o una URL que la
 * contenga (con host, query o hash): solo se usa la ruta normalizada como clave del índice, nunca
 * como ruta de fichero.
 *
 * Una pantalla sin entrada en el índice no es un error: se devuelve `encontrada = false` con
 * `aviso_txt`, y `manual_modulo` si el módulo tiene manual.
 */
final class AyudaPantallaData
{
    private const LIMITE_POR_DEFECTO = 6;
    private const LIMITE_MAXIMO = 30;
    private const RUTA_CONTROLLER = '#frontend/([a-z][a-z0-9_]*)/controller/([A-Za-z0-9_]+)\.php#';

    public function __construct(
        private readonly ?string $docsRoot = null,
    ) {
    }

    /**
     * @param array<string, mixed> $input `controller` (string), `limite` (int, opcional)
     * @return array{
     *     encontrada: bool,
     *     controller: string,
     *     titulo: string,
     *     modulo: string,
     *     manual_modulo: string,
     *     total_tareas: int,
     *     tareas: list<array{modulo: string, ancla: string, titulo: string, sirve: string}>,
     *     aviso_txt: string
     * }
     */
    public function execute(array $input): array
    {
        $controller = $this->normalizarController(FuncTablasSupport::inputString($input, 'controller'));
        $limite = FuncTablasSupport::inputInt($input, 'limite');
        if ($limite < 1) {
            $limite = self::LIMITE_POR_DEFECTO;
        }
        $limite = min($limite, self::LIMITE_MAXIMO);

        if ($controller === '') {
            return $this->sinAyuda('', '');
        }

        $modulo = $this->moduloDe($controller);
        $docs = $this->resolveDocsRoot();
        $pantalla = $docs !== null ? ($this->leerIndice($docs)[$controller] ?? null) : null;

        if ($pantalla === null) {
            return $this->sinAyuda($controller, $docs !== null && $this->existeManual($docs, $modulo) ? $modulo : '');
        }

        $tareas = $pantalla['manual'];
        return [
            'encontrada' => true,
            'controller' => $controller,
            'titulo' => $pantalla['titulo'],
            'modulo' => $pantalla['modulo'],
            'manual_modulo' => $tareas[0]['modulo'] ?? $pantalla['modulo'],
            'total_tareas' => count($tareas),
            'tareas' => array_slice($tareas, 0, $limite),
            'aviso_txt' => '',
        ];
    }

    private function normalizarController(string $valor): string
    {
        return preg_match(self::RUTA_CONTROLLER, $valor, $m) === 1 ? $m[0] : '';
    }

    private function moduloDe(string $controller): string
    {
        return preg_match(self::RUTA_CONTROLLER, $controller, $m) === 1 ? $m[1] : '';
    }

    /**
     * @return array{
     *     encontrada: bool, controller: string, titulo: string, modulo: string, manual_modulo: string,
     *     total_tareas: int, tareas: list<array{modulo: string, ancla: string, titulo: string, sirve: string}>,
     *     aviso_txt: string
     * }
     */
    private function sinAyuda(string $controller, string $manualModulo): array
    {
        return [
            'encontrada' => false,
            'controller' => $controller,
            'titulo' => '',
            'modulo' => $this->moduloDe($controller),
            'manual_modulo' => $manualModulo,
            'total_tareas' => 0,
            'tareas' => [],
            'aviso_txt' => _("No hay ayuda específica para esta pantalla."),
        ];
    }

    private function existeManual(string $docs, string $modulo): bool
    {
        return $modulo !== '' && is_file($docs . '/manual/' . $modulo . '.md');
    }

    private function resolveDocsRoot(): ?string
    {
        if ($this->docsRoot !== null && $this->docsRoot !== '') {
            $real = realpath($this->docsRoot);
            return ($real !== false && is_dir($real)) ? $real : null;
        }
        $base = (string)(ConfigGlobal::$directorio ?? '');
        if ($base === '') {
            return null;
        }
        $docs = realpath(rtrim($base, '/\\') . '/docs');
        return ($docs !== false && is_dir($docs)) ? $docs : null;
    }

    /**
     * @return array<string, array{
     *     titulo: string, modulo: string,
     *     manual: list<array{modulo: string, ancla: string, titulo: string, sirve: string}>
     * }>
     */
    private function leerIndice(string $docs): array
    {
        $path = $docs . '/manual/_indice_pantallas.json';
        if (!is_file($path)) {
            return [];
        }
        $data = json_decode((string)file_get_contents($path), true);
        if (!is_array($data) || !isset($data['pantallas']) || !is_array($data['pantallas'])) {
            return [];
        }

        $indice = [];
        foreach ($data['pantallas'] as $ruta => $p) {
            if (!is_string($ruta) || !is_array($p)) {
                continue;
            }
            $tareas = [];
            foreach (is_array($p['manual'] ?? null) ? $p['manual'] : [] as $t) {
                if (!is_array($t) || !isset($t['modulo'], $t['ancla'], $t['titulo'])) {
                    continue;
                }
                $tareas[] = [
                    'modulo' => (string)$t['modulo'],
                    'ancla' => (string)$t['ancla'],
                    'titulo' => (string)$t['titulo'],
                    'sirve' => (string)($t['sirve'] ?? ''),
                ];
            }
            $indice[$ruta] = [
                'titulo' => (string)($p['titulo'] ?? ''),
                'modulo' => (string)($p['modulo'] ?? ''),
                'manual' => $tareas,
            ];
        }
        return $indice;
    }
}
