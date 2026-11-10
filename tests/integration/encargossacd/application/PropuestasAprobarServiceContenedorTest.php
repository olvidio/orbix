<?php

namespace Tests\integration\encargossacd\application;

use src\encargossacd\application\PropuestasAprobar;
use src\encargossacd\application\services\PropuestasAprobarService;
use Tests\myTest;

/**
 * El contenedor debe poder construir el servicio aunque tenga un parámetro opcional (la limpieza inyectable).
 */
class PropuestasAprobarServiceContenedorTest extends myTest
{
    public function test_el_contenedor_construye_el_servicio_y_el_caso_de_uso(): void
    {
        $this->assertInstanceOf(PropuestasAprobarService::class, $GLOBALS['container']->get(PropuestasAprobarService::class));
        $this->assertInstanceOf(PropuestasAprobar::class, $GLOBALS['container']->get(PropuestasAprobar::class));
    }
}
