<?php

declare(strict_types=1);

namespace Tests\integration\notas\infrastructure\repositories;

use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\value_objects\ActaNumero;
use Tests\factories\notas\ActaFactory;
use Tests\myTest;

/**
 * `PgActaRepository::getUltimaActa()`: último número de acta de una región/dl en un año, que usa
 * `Acta::inventarActa()` para proponer el siguiente.
 *
 * Las actas se crean con siglas inventadas y el año 98 para no chocar con las reales.
 */
final class PgActaUltimaActaTest extends myTest
{
    private ActaDlRepositoryInterface $actasDl;
    private ActaRepositoryInterface $actas;
    /** @var list<Acta> */
    private array $creadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->actasDl = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
        $this->actas = $GLOBALS['container']->get(ActaRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        foreach ($this->creadas as $acta) {
            $this->actasDl->Eliminar($acta);
        }
        parent::tearDown();
    }

    private function acta(string $numero): void
    {
        $acta = (new ActaFactory())->createSimple();
        $acta->setActaVo(new ActaNumero($numero));
        $this->assertTrue($this->actasDl->Guardar($acta));
        $this->creadas[] = $acta;
    }

    public function test_devuelve_el_numero_mas_alto_de_esa_region_y_ese_anyo(): void
    {
        $this->acta('zqt 9/98');
        $this->acta('zqt 41/98');
        $this->acta('zqt 50/97');
        $this->acta('zqx 60/98');

        $this->assertSame(41, $this->actas->getUltimaActa('98', 'zqt'));
        $this->assertSame(41, $this->actas->getUltimaActa(98, 'ZQT'), 'sin distinguir mayúsculas');
        $this->assertSame(50, $this->actas->getUltimaActa('97', 'zqt'));
        $this->assertSame(0, $this->actas->getUltimaActa('96', 'zqt'));
    }

    /**
     * Comportamiento heredado (anotado en el plan): con la región por defecto «?» nunca encuentra nada, porque el
     * número se extrae con `^\w{1,8}\s+(\d+)/aa` y «?» no es `\w`.
     */
    public function test_region_desconocida_con_interrogante_no_encuentra_nada(): void
    {
        $this->acta('? 7/98');

        $this->assertSame(0, $this->actas->getUltimaActa('98'));
    }

    /**
     * Regresión: la región se metía tal cual en el SQL, y `Acta::inventarActa()` le pasa el texto del acta que
     * escribe el usuario cuando no tiene el formato «sigla n/aa». Con «zqt' OR '1'='1» la comilla cerraba el literal y
     * el resto se ejecutaba como SQL (aquí devolvía 41); con caracteres de expresión regular la consulta fallaba.
     */
    public function test_la_region_cuenta_como_texto_literal(): void
    {
        $this->acta('zqt 41/98');
        $this->acta('zqy 900/98');

        $this->assertSame(0, $this->actas->getUltimaActa('98', "zqt' OR '1'='1"));
        $this->assertSame(0, $this->actas->getUltimaActa('98', 'zq.'), 'el punto no es comodín');
        $this->assertSame(0, $this->actas->getUltimaActa('98', 'zqt('));
    }

    public function test_inventar_acta_propone_el_siguiente_numero(): void
    {
        $this->acta('zqt 41/98');

        $this->assertSame('zqt 42/98?', Acta::inventarActa('zqt', '23/7/1998'), 'con «?» final: número provisional');
    }
}
