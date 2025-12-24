<?php

namespace src\encargossacd\application\traits;

use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\services\EncargoDominioService;

/**
 * EncargoFunciones
 *
 * Classe per poder fer servir les funcions
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class EncargoFunciones
{
    private $dominioService;
    private $aplicacionService;

    public function __construct()
    {
        $this->dominioService = new EncargoDominioService();
        $this->aplicacionService = new EncargoAplicacionService();
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->dominioService, $name)) {
            return call_user_func_array([$this->dominioService, $name], $arguments);
        }
        if (method_exists($this->aplicacionService, $name)) {
            return call_user_func_array([$this->aplicacionService, $name], $arguments);
        }
        throw new \BadMethodCallException("Método $name no encontrado en los servicios de Encargo.");
    }
}