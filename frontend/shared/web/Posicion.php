<?php

namespace frontend\shared\web;

use src\shared\config\ConfigGlobal;
use web\Hash;

class Posicion
{
    private ?string $sid_div = null;
    private string $surl = '';
    private string $sbloque = '';
    private array $aParametros = [];
    private int $stack;
    private bool $constructor;

    public function __construct($php_self = '', $vars = array())
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
        return (!array_key_exists('position', $_SESSION) || !is_array($_SESSION['position']));
    }

    private function deleteFroward(): void
    {
        if ($this->notExistsSession()) {
            return;
        }
        session_start();
        $stack = $this->stack;
        $num = count($_SESSION['position']);
        $quitar = $num - $stack;
        array_splice($_SESSION['position'], -$quitar);
        $_SESSION['position'] = array_values($_SESSION['position']);
        foreach ($_SESSION['position'] as $key => $values) {
            $_SESSION['position'][$key]['stack'] = $key;
        }
        $this->stack = $stack - 1;
        session_write_close();
    }

    /**
     * Coloca el cursor de position en la última posición.
     */
    private function goEnd(): void
    {
        if ($this->notExistsSession()) {
            return;
        }
        $aPosition = end($_SESSION['position']);
        $this->stack = key($_SESSION['position']);
        $this->surl = $aPosition['url'];
        $this->sbloque = $aPosition['bloque'];
        $this->aParametros = $aPosition['parametros'];
    }

    /**
     * Coloca el cursor de Posicion n posiciones atrás.
     * Para n=0, no se usa el valor en $_SESSION['position'], sino el actual.
     */
    public function go(int $n = 0): void
    {
        if ($n === 0 || $this->notExistsSession()) {
            return;
        }

        $aPosition = end($_SESSION['position']);
        for ($i = 0; $i < $n; $i++) {
            $aPosition = prev($_SESSION['position']);
            if ($aPosition === FALSE) {
                $aPosition = reset($_SESSION['position']);
                break;
            }
        }
        $this->stack = key($_SESSION['position']);
        $this->surl = $aPosition['url'];
        $this->sbloque = $aPosition['bloque'];
        $this->aParametros = $aPosition['parametros'];
    }

    public function getStack($n = 0): int
    {
        end($_SESSION['position']);
        for ($i = 0; $i < $n; $i++) {
            prev($_SESSION['position']);
        }
        $key = key($_SESSION['position']);
        $this->stack = empty($key) ? 0 : $key;
        return $this->stack;
    }

    /**
     * Coloca el cursor de Posicion en stack.
     *
     * @param int|string $stack índice del array $_SESSION['position'].
     */
    public function goStack(int|string $stack = '*'): bool
    {
        if (isset($_SESSION['position'][$stack])) {
            $aPosition = $_SESSION['position'][$stack];
            $this->stack = $aPosition['stack'];
            $this->surl = $aPosition['url'];
            $this->sbloque = $aPosition['bloque'];
            $this->aParametros = $aPosition['parametros'];
            return true;
        }
        return false;
    }

    public function olvidar($stack = '*'): void
    {
        if ($stack !== '*') {
            array_splice($_SESSION['position'], $stack + 1);
        } elseif (isset($this->stack)) {
            array_splice($_SESSION['position'], $this->stack);
        }
        $this->guardar();
    }

    /**
     * @param int $parar Para el incremento de la pila. Por defecto 0. Usar 1 para actualizar la misma página.
     */
    public function recordar($parar = 0): void
    {
        $this->stack = $this->aParametros['stack'] ?? 0;
        $this->limitar(20);
        if (empty($this->stack)) {
            if ($this->existsSession()) {
                end($_SESSION['position']);
                $stack = empty($parar) ? (int)key($_SESSION['position']) + 1 : (int)key($_SESSION['position']);
            } else {
                $stack = 0;
            }
        } else {
            $this->deleteFroward();
            $stack = empty($parar) ? $this->stack + 1 : $this->stack;
        }
        $this->setParametro('stack', $stack);
        $aPosition = array('url' => $this->surl, 'bloque' => $this->sbloque, 'parametros' => $this->aParametros, 'stack' => $stack);

        session_start();
        $_SESSION['position'][$stack] = $aPosition;
        session_write_close();
    }

    private function guardar(): void
    {
        if (!isset($this->stack)) {
            if ($this->existsSession()) {
                end($_SESSION['position']);
                $stack = key($_SESSION['position']);
            } else {
                $stack = 0;
            }
        } else {
            $stack = $this->stack;
        }
        session_start();
        $_SESSION['position'][$stack] = array('url' => $this->surl, 'bloque' => $this->sbloque, 'parametros' => $this->aParametros, 'stack' => $stack);
        session_write_close();
    }

    public function go_atras($n = 0): string
    {
        $this->go($n);
        if (empty($this->surl)) {
            return '';
        }
        $id_div = $this->getId_div();
        $id_div = empty($id_div) ? 'go_atras' : $id_div;

        $url = $this->surl;
        $aParam = $this->aParametros;
        if (!empty($aParam['bloque'])) {
            $this->sbloque = '#' . $aParam['bloque'];
        }
        $sparametros = Hash::add_hash($aParam, $url);
        $bloque = $this->sbloque;

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

    public function js_atras($n = 0): string
    {
        $this->go($n);
        if (empty($this->surl)) {
            return '';
        }
        $id_div = $this->getId_div();
        $id_div = empty($id_div) ? 'js_atras' : $id_div;

        $url = $this->surl;
        $aParam = $this->aParametros;
        $sparametros = Hash::add_hash($aParam, $url);
        $bloque = $this->sbloque;

        $html = '<form id="go">';
        $html .= '	<input name="url" type="text" value="' . $url . '" size=70><br>';
        $html .= '	<input name="parametros" type="text" value="' . $sparametros . '" size=70><br>';
        $html .= '	<input name="id_div" type="text" value="' . $bloque . '" size=70>';
        $html .= '</form>';

        $this->goEnd();
        return "fnjs_mostrar_atras('#$id_div','$html');";
    }

    /**
     * Retorna un div con formulario para enviar datos a Posicion.
     */
    public function mostrar_left_slide(int $n = 0): string
    {
        $this->go($n);
        if (empty($this->surl)) {
            return '';
        }
        $id_div = $this->getId_div();
        $id_div = empty($id_div) ? 'ir_atras' : $id_div;

        $url = $this->surl;
        $aParam = $this->aParametros;
        if (!empty($aParam['bloque'])) {
            $this->sbloque = '#' . $aParam['bloque'];
        }
        $sparametros = Hash::add_hash($aParam, $url);
        $bloque = $this->sbloque;

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

    /**
     * Retorna una imagen de flecha, con formulario para enviar datos a Posicion.
     * onClick -> activa fnjs_ir_a(id_div)
     */
    public function mostrar_back_arrow(int $n = 0): string
    {
        $this->go($n);
        if (empty($this->surl)) {
            return '';
        }
        $id_div = $this->getId_div();
        $id_div = empty($id_div) ? 'ir_atras2' : $id_div;

        $url = $this->surl;
        $aParam = $this->aParametros;
        $sparametros = Hash::add_hash($aParam, $url);
        $bloque = $this->sbloque;

        $html = '<div id="' . $id_div . '" style="display: none;">';
        $html .= '<form id="go">';
        $html .= '	<input name="url" type="hidden" value="' . $url . '" size=70>';
        $html .= '	<input name="parametros" type="hidden" value="' . $sparametros . '" size=70>';
        $html .= '	<input name="id_div" type="hidden" value="' . $bloque . '" size=70>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= "<img onclick=fnjs_ir_a('#$id_div') src=" . ConfigGlobal::getWeb_icons() . '/flechas/left.gif border=0 height=40>';

        $this->goEnd();
        return $html;
    }

    private function limitar($n = 10): void
    {
        if (isset($_SESSION['position']) && $this->existsSession()) {
            $max = 2 * $n;
            $num = count($_SESSION['position']);
            if ($num > $max) {
                array_splice($_SESSION['position'], -$n);
                end($_SESSION['position']);
                $this->stack = key($_SESSION['position']);
            }
        }
    }

    public function setId_div($id_div): void
    {
        $this->sid_div = $id_div;
    }

    public function getId_div(): string
    {
        if (empty($this->sid_div)) {
            return '';
        }
        return $this->sid_div;
    }

    public function setUrl($url): void
    {
        $this->surl = $url;
    }

    public function getUrl(): string
    {
        return $this->surl;
    }

    public function setBloque($bloque): void
    {
        $this->sbloque = $bloque;
    }

    public function getBloque(): string
    {
        return $this->sbloque;
    }

    public function addParametro($nomParametre, $valor, $n = 0): void
    {
        if (!empty($_SESSION['position'])) {
            $this->go($n);
            $this->setParametro($nomParametre, $valor);
            $this->goEnd();
        }
    }

    public function setParametro($nomParametre, $valor): void
    {
        $this->aParametros[$nomParametre] = $valor;
    }

    public function setParametros($aVars, $n = 0): void
    {
        if ($this->constructor) {
            foreach ($aVars as $key => $value) {
                $this->aParametros[$key] = $value;
            }
        } else {
            if (!empty($_SESSION['position'])) {
                $this->go($n);
                foreach ($aVars as $key => $value) {
                    $this->aParametros[$key] = $value;
                }
                $this->guardar();
                $this->goEnd();
            }
        }
    }

    public function getParametro($nomParametre, $n = 0)
    {
        if ($n == 0) {
            if (!isset($this->aParametros[$nomParametre])) {
                $valParametre = '';
            } else {
                $valParametre = empty($this->aParametros[$nomParametre]) ? '' : $this->aParametros[$nomParametre];
            }
        } else {
            $this->go($n);
            if (!isset($this->aParametros[$nomParametre])) {
                $valParametre = '';
            } else {
                $valParametre = empty($this->aParametros[$nomParametre]) ? '' : $this->aParametros[$nomParametre];
            }
            $this->goEnd();
        }
        return $valParametre;
    }
}
