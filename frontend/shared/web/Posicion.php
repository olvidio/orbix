<?php

namespace frontend\shared\web;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\security\HashFront;

/**
 * @phpstan-type PositionEntry array{url: string, bloque: string, parametros: array<string, mixed>, stack: int}
 */
class Posicion
{
    private ?string $sid_div = null;
    private string $surl = '';
    private string $sbloque = '';
    /** @var array<string, mixed> */
    private array $aParametros = [];
    private int $stack = 0;
    private bool $constructor;

    /** @param array<string, mixed> $vars */
    public function __construct(string $php_self = '', array $vars = [])
    {
        $this->constructor = true;
        $this->surl = $php_self;
        $this->sbloque = '#main';
        $this->setParametros($vars);
        $this->constructor = false;
    }

    private function existsSession(): bool
    {
        return !$this->notExistsSession();
    }

    private function notExistsSession(): bool
    {
        return !isset($_SESSION['position']) || !is_array($_SESSION['position']);
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    private function &positionStackRef(): array
    {
        if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
            $_SESSION['position'] = [];
        }
        /** @var array<int|string, array<string, mixed>> $position */
        $position = &$_SESSION['position'];
        return $position;
    }

    /**
     * @param array<string, mixed> $raw
     * @return PositionEntry
     */
    private static function normalizeBloqueSelector(string $bloque): string
    {
        $bloque = ltrim(trim($bloque), '#');

        return $bloque === '' ? '#main' : '#' . $bloque;
    }

    private static function normalizeDomId(string $id): string
    {
        return ltrim(trim($id), '#');
    }

    private static function resolveBloqueFromEntry(string $sbloque, array $aParam): string
    {
        if (!empty($aParam['bloque']) && is_string($aParam['bloque'])) {
            return self::normalizeBloqueSelector($aParam['bloque']);
        }

        return self::normalizeBloqueSelector($sbloque);
    }

    private static function normalizeEntry(array $raw, int $stackKey): array
    {
        $bloque = isset($raw['bloque']) && is_string($raw['bloque']) ? $raw['bloque'] : '';

        return [
            'url' => isset($raw['url']) && is_string($raw['url']) ? $raw['url'] : '',
            'bloque' => self::normalizeBloqueSelector($bloque),
            'parametros' => isset($raw['parametros']) && is_array($raw['parametros']) ? $raw['parametros'] : [],
            'stack' => isset($raw['stack']) && is_numeric($raw['stack']) ? (int) $raw['stack'] : $stackKey,
        ];
    }

    /** @param PositionEntry $entry */
    private function applyEntry(array $entry, int $stackKey): void
    {
        $this->stack = $stackKey;
        $this->surl = $entry['url'];
        $this->sbloque = $entry['bloque'];
        $this->aParametros = $entry['parametros'];
    }

    private function deleteFroward(): void
    {
        if ($this->notExistsSession()) {
            return;
        }
        session_start();
        $stack = $this->positionStackRef();
        $current = $this->stack;
        $num = count($stack);
        $quitar = $num - $current;
        if ($quitar > 0) {
            array_splice($stack, -$quitar);
        }
        $stack = array_values($stack);
        foreach ($stack as $key => $values) {
            $stack[$key]['stack'] = $key;
        }
        $_SESSION['position'] = $stack;
        $this->stack = $current - 1;
        session_write_close();
    }

    private function goEnd(): void
    {
        if ($this->notExistsSession()) {
            return;
        }
        $stack = $this->positionStackRef();
        $raw = end($stack);
        $stackKey = key($stack);
        if (!is_array($raw) || $stackKey === null) {
            return;
        }
        $this->applyEntry(self::normalizeEntry($raw, (int) $stackKey), (int) $stackKey);
    }

    public function go(int $n = 0): void
    {
        if ($n === 0 || $this->notExistsSession()) {
            return;
        }

        $stack = $this->positionStackRef();
        $raw = end($stack);
        for ($i = 0; $i < $n; $i++) {
            $prev = prev($stack);
            if ($prev === false) {
                $raw = reset($stack);
                break;
            }
            $raw = $prev;
        }
        $stackKey = key($stack);
        if (!is_array($raw) || $stackKey === null) {
            return;
        }
        $this->applyEntry(self::normalizeEntry($raw, (int) $stackKey), (int) $stackKey);
    }

    public function getStack(int $n = 0): int
    {
        $stack = $this->positionStackRef();
        end($stack);
        for ($i = 0; $i < $n; $i++) {
            prev($stack);
        }
        $key = key($stack);
        $this->stack = ($key === null || $key === '') ? 0 : (int) $key;
        return $this->stack;
    }

    public function goStack(int|string $stack = '*'): bool
    {
        if ($stack === '*') {
            return false;
        }
        $position = $this->positionStackRef();
        if (!isset($position[$stack])) {
            return false;
        }
        $raw = $position[$stack];
        $stackKey = is_int($stack) ? $stack : (int) $stack;
        $this->applyEntry(self::normalizeEntry($raw, $stackKey), $stackKey);
        return true;
    }

    public function olvidar(int|string $stack = '*'): void
    {
        $position = &$this->positionStackRef();
        if ($stack !== '*') {
            $idx = is_int($stack) ? $stack : (int) $stack;
            array_splice($position, $idx + 1);
        } else {
            array_splice($position, $this->stack);
        }
        $this->guardar();
    }

    public function recordar(int $parar = 0): void
    {
        $stackParam = $this->aParametros['stack'] ?? 0;
        $this->stack = is_numeric($stackParam) ? (int) $stackParam : 0;
        $this->limitar(20);
        if ($this->stack === 0) {
            if ($this->existsSession()) {
                $position = $this->positionStackRef();
                end($position);
                $key = key($position);
                $base = ($key === null) ? 0 : (int) $key;
                $stack = $parar === 0 ? $base + 1 : $base;
            } else {
                $stack = 0;
            }
        } else {
            $this->deleteFroward();
            $stack = $parar === 0 ? $this->stack + 1 : $this->stack;
        }
        $this->setParametro('stack', $stack);
        $aPosition = [
            'url' => $this->surl,
            'bloque' => $this->sbloque,
            'parametros' => $this->aParametros,
            'stack' => $stack,
        ];

        session_start();
        $position = &$this->positionStackRef();
        $position[$stack] = $aPosition;
        session_write_close();
    }

    private function guardar(): void
    {
        // Tras go($n), $this->stack apunta a la entrada que hay que persistir
        // (p. ej. setParametros(..., 1) actualiza la página anterior en la pila).
        $stack = $this->stack;
        session_start();
        $position = &$this->positionStackRef();
        $position[$stack] = [
            'url' => $this->surl,
            'bloque' => $this->sbloque,
            'parametros' => $this->aParametros,
            'stack' => $stack,
        ];
        session_write_close();
    }

    public function go_atras(int $n = 0): string
    {
        $this->go($n);
        if ($this->surl === '') {
            return '';
        }
        $id_div = self::normalizeDomId($this->getId_div());
        $id_div = $id_div === '' ? 'go_atras' : $id_div;

        $url = $this->surl;
        $aParam = $this->aParametros;
        $sparametros = HashFront::add_hash($aParam, $url);
        $bloque = self::resolveBloqueFromEntry($this->sbloque, $aParam);

        $html = '<div id="' . $id_div . '" style="display: none;">';
        $html .= '	<form id="go">';
        $html .= '	url: <input name="url" type="text" value="' . $url . '" size=70><br>';
        $html .= '	parametros: <input name="parametros" type="text" value="' . $sparametros . '" size=70><br>';
        $html .= '	bloque: <input name="id_div" type="text" value="' . $bloque . '" size=70>';
        $html .= '</form>';
        $html .= '</div>';
        $this->goEnd();
        return $html;
    }

    public function js_atras(int $n = 0): string
    {
        $this->go($n);
        if ($this->surl === '') {
            return '';
        }
        $id_div = self::normalizeDomId($this->getId_div());
        $id_div = $id_div === '' ? 'js_atras' : $id_div;

        $url = $this->surl;
        $aParam = $this->aParametros;
        $sparametros = HashFront::add_hash($aParam, $url);
        $bloque = self::normalizeBloqueSelector($this->sbloque);

        $html = '<form id="go">';
        $html .= '	<input name="url" type="text" value="' . $url . '" size=70><br>';
        $html .= '	<input name="parametros" type="text" value="' . $sparametros . '" size=70><br>';
        $html .= '	<input name="id_div" type="text" value="' . $bloque . '" size=70>';
        $html .= '</form>';

        $this->goEnd();
        return "fnjs_mostrar_atras('#" . $id_div . "','$html');";
    }

    public function mostrar_left_slide(int $n = 0): string
    {
        $this->go($n);
        if ($this->surl === '') {
            return '';
        }
        $id_div = self::normalizeDomId($this->getId_div());
        $id_div = $id_div === '' ? 'ir_atras' : $id_div;

        $url = $this->surl;
        $aParam = $this->aParametros;
        $sparametros = HashFront::add_hash($aParam, $url);
        $bloque = self::resolveBloqueFromEntry($this->sbloque, $aParam);

        $html = '<div id="' . $id_div . '" style="display: none;">';
        $html .= '	<form id="go">';
        $html .= '	url: <input name="url" type="text" value="' . $url . '" size=70><br>';
        $html .= '	parametros: <input name="parametros" type="text" value="' . $sparametros . '" size=70><br>';
        $html .= '	bloque: <input name="id_div" type="text" value="' . $bloque . '" size=70>';
        $html .= '</form>';
        $html .= '</div>';

        $this->goEnd();
        return $html;
    }

    public function mostrar_back_arrow(int $n = 0): string
    {
        $this->go($n);
        if ($this->surl === '') {
            return '';
        }
        $id_div = self::normalizeDomId($this->getId_div());
        $id_div = $id_div === '' ? 'ir_atras2' : $id_div;

        $url = $this->surl;
        $aParam = $this->aParametros;
        $sparametros = HashFront::add_hash($aParam, $url);
        $bloque = self::normalizeBloqueSelector($this->sbloque);

        $html = '<div id="' . $id_div . '" style="display: none;">';
        $html .= '<form id="go">';
        $html .= '	<input name="url" type="hidden" value="' . $url . '" size=70>';
        $html .= '	<input name="parametros" type="hidden" value="' . $sparametros . '" size=70>';
        $html .= '	<input name="id_div" type="hidden" value="' . $bloque . '" size=70>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= "<img onclick=fnjs_ir_a('#" . $id_div . "') src=" . OrbixRuntime::getWebIcons() . '/flechas/left.gif border=0 height=40>';

        $this->goEnd();
        return $html;
    }

    private function limitar(int $n = 10): void
    {
        if ($this->existsSession()) {
            $position = $this->positionStackRef();
            $max = 2 * $n;
            $num = count($position);
            if ($num > $max) {
                array_splice($position, -$n);
                end($position);
                $key = key($position);
                $this->stack = ($key === null) ? 0 : (int) $key;
            }
        }
    }

    public function setId_div(?string $id_div): void
    {
        $this->sid_div = $id_div;
    }

    public function getId_div(): string
    {
        return $this->sid_div ?? '';
    }

    public function setUrl(string $url): void
    {
        $this->surl = $url;
    }

    public function getUrl(): string
    {
        return $this->surl;
    }

    public function setBloque(string $bloque): void
    {
        $this->sbloque = self::normalizeBloqueSelector($bloque);
    }

    public function getBloque(): string
    {
        return $this->sbloque;
    }

    public function addParametro(string $nomParametre, mixed $valor, int $n = 0): void
    {
        if ($this->existsSession()) {
            $this->go($n);
            $this->setParametro($nomParametre, $valor);
            $this->goEnd();
        }
    }

    public function setParametro(string $nomParametre, mixed $valor): void
    {
        $this->aParametros[$nomParametre] = $valor;
    }

    /** @param array<string, mixed> $aVars */
    public function setParametros(array $aVars, int $n = 0): void
    {
        if ($this->constructor) {
            foreach ($aVars as $key => $value) {
                $this->aParametros[$key] = $value;
            }
        } elseif ($this->existsSession()) {
            $this->go($n);
            foreach ($aVars as $key => $value) {
                $this->aParametros[$key] = $value;
            }
            $this->guardar();
            $this->goEnd();
        }
    }

    public function getParametro(string $nomParametre, int $n = 0): mixed
    {
        if ($n === 0) {
            return $this->aParametros[$nomParametre] ?? '';
        }
        $this->go($n);
        $val = $this->aParametros[$nomParametre] ?? '';
        $this->goEnd();
        return empty($val) ? '' : $val;
    }

    /**
     * Sustituye los parámetros de una entrada de la pila (p. ej. dossiers_ver al volver desde un form hijo).
     * Evita arrastrar meta-hash de formularios que invalidan {@see HashFront::add_hash}.
     *
     * @param array<string, mixed> $parametros
     */
    public function replaceStackParametros(array $parametros, int $n = 1): void
    {
        if ($this->notExistsSession()) {
            return;
        }
        $this->go($n);
        if (!isset($parametros['stack'])) {
            $parametros['stack'] = $this->aParametros['stack'] ?? $this->stack;
        }
        $this->aParametros = $parametros;
        $this->guardar();
        $this->goEnd();
    }
}
