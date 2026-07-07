<?php

declare(strict_types=1);

namespace frontend\shared\web;

use frontend\shared\helpers\ListNavSupport;

/**
 * Contexto de petición frontend + acceso a {@see NavStack} v2.
 *
 * Ya no mantiene pila en {@code $_SESSION['position']} (eliminada en Fase 4).
 */
class Posicion
{
    private ?NavStack $navStack = null;

    /** @var array<string, mixed> */
    private array $aParametros;

    /** @param array<string, mixed> $vars */
    public function __construct(string $php_self = '', array $vars = [])
    {
        unset($vars['stack'], $vars['Gstack']);
        $this->aParametros = $vars;
        if ($php_self !== '') {
            $this->aParametros['_php_self'] = $php_self;
        }
    }

    public function nav(): NavStack
    {
        if ($this->navStack === null) {
            $this->navStack = new NavStack($this->aParametros);
        }

        return $this->navStack;
    }

    /**
     * Marca NavStack v2 para mostrar #left_slide (la flecha visible es solo la del shell).
     * Devuelve vacío si no hay destino en la pila.
     */
    public function mostrarNavAtras(int $n = 1): string
    {
        if ($this->nav()->backTarget($n) === null) {
            return '';
        }

        return '<span class="orbix-nav-atras" data-nav-atras="' . $n . '" hidden aria-hidden="true"></span>';
    }

    /**
     * Llamada JS atrás v2 para callbacks (p. ej. tras guardar formulario).
     */
    public function jsNavAtras(int $n = 1): string
    {
        if ($this->nav()->backTarget($n) === null) {
            return '';
        }

        return 'fnjs_nav_atras(' . $n . ');';
    }

    public function mostrarNavAtrasFromDossiers(): string
    {
        $n = ListNavSupport::navBackStepsFromDossiersVer($this->nav());

        return $this->mostrarNavAtras($n);
    }

    public function mostrarNavAtrasToDossiersParent(): string
    {
        $n = $this->nav()->backStepsUntilUrlContains('dossiers_ver.php');

        return $this->mostrarNavAtras($n);
    }

    public function jsNavAtrasToDossiersParent(): string
    {
        $n = $this->nav()->backStepsUntilUrlContains('dossiers_ver.php');

        return $this->jsNavAtras($n);
    }

    public function setParametro(string $nomParametre, mixed $valor): void
    {
        $this->aParametros[$nomParametre] = $valor;
    }

    /** @param array<string, mixed> $aVars */
    public function setParametros(array $aVars, int $n = 0): void
    {
        unset($aVars['stack'], $aVars['Gstack']);
        foreach ($aVars as $key => $value) {
            $this->aParametros[$key] = $value;
        }
        if ($n === 0) {
            $this->navStack = null;
        }
    }

    public function addParametro(string $nomParametre, mixed $valor): void
    {
        $this->setParametro($nomParametre, $valor);
    }

    public function getParametro(string $nomParametre, int $n = 0): mixed
    {
        return $this->aParametros[$nomParametre] ?? '';
    }

    public function setBloque(string $bloque): void
    {
        $this->aParametros['bloque'] = $bloque;
    }

    public function getBloque(): string
    {
        $bloque = $this->aParametros['bloque'] ?? '#main';

        return is_string($bloque) ? $bloque : '#main';
    }

    public function setUrl(string $url): void
    {
        $this->aParametros['_php_self'] = $url;
    }

    public function getUrl(): string
    {
        $url = $this->aParametros['_php_self'] ?? '';

        return is_string($url) ? $url : '';
    }
}
