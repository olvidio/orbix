<?php

namespace src\inventario\application;

use src\shared\config\ConfigGlobal;

/**
 * CSS embebido para impresión de inventario (`inventario.css.php` en disco).
 */
final class InventarioCssInlineData
{
    /**
     * @return array{css: string}
     */
    public function execute(): array
    {
        $path = ConfigGlobal::$dir_estilos . '/inventario.css.php';
        $css = is_readable($path) ? (string)file_get_contents($path) : '';

        return ['css' => $css];
    }
}
