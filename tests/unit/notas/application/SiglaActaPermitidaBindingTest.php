<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use src\notas\application\support\ActaPersonaFormListas;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\application\support\SiglaActaPermitida;
use Tests\myTest;

/**
 * `PersonaNotaInputParser` depende de la interfaz {@see SiglaActaPermitida} (seam para poder
 * probar el parser sin sesión ni ficheros de configuración). El binding a la implementación
 * real vive en `src/notas/config/dependencies.php` y no lo cubre ningún otro test: sin él, la
 * pantalla de guardar nota fallaría al resolver el contenedor.
 */
final class SiglaActaPermitidaBindingTest extends myTest
{
    public function test_contenedor_resuelve_parser_y_sigla_permitida(): void
    {
        $container = $GLOBALS['container'];

        $this->assertInstanceOf(
            ActaPersonaFormListas::class,
            $container->get(SiglaActaPermitida::class)
        );
        $this->assertInstanceOf(
            PersonaNotaInputParser::class,
            $container->get(PersonaNotaInputParser::class)
        );
    }
}
