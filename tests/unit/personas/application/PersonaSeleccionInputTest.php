<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\support\PersonaSeleccionInput;

/** Identificación de la persona seleccionada en un listado (`id_nom#id_tabla` o campos sueltos). */
final class PersonaSeleccionInputTest extends TestCase
{
    /**
     * @dataProvider entradasProvider
     * @param array<string, mixed> $input
     * @param array{id_nom: int, id_tabla: string} $esperado
     */
    public function test_extrae_id_nom_e_id_tabla(array $input, array $esperado): void
    {
        $this->assertSame($esperado, PersonaSeleccionInput::idNomYTabla($input));
        $this->assertSame($esperado['id_nom'], PersonaSeleccionInput::idNom($input));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, array{id_nom: int, id_tabla: string}}>
     */
    public static function entradasProvider(): iterable
    {
        yield 'sel como lista' => [['sel' => ['10011#n']], ['id_nom' => 10011, 'id_tabla' => 'n']];
        yield 'sel como lista, solo el primero' => [['sel' => ['10011#n', '10012#a']], ['id_nom' => 10011, 'id_tabla' => 'n']];
        yield 'sel como texto' => [['sel' => '10012#agd'], ['id_nom' => 10012, 'id_tabla' => 'agd']];
        yield 'sel sin tabla' => [['sel' => ['10011']], ['id_nom' => 10011, 'id_tabla' => '']];
        yield 'campos sueltos' => [['id_nom' => '10013', 'id_tabla' => 's'], ['id_nom' => 10013, 'id_tabla' => 's']];
        yield 'lista vacía usa campos sueltos' => [['sel' => [], 'id_nom' => '7'], ['id_nom' => 7, 'id_tabla' => '']];
        yield 'sel vacío usa campos sueltos' => [['sel' => '', 'id_nom' => '8', 'id_tabla' => 'n'], ['id_nom' => 8, 'id_tabla' => 'n']];
        yield 'primer elemento no escalar usa campos sueltos' => [['sel' => [['x']], 'id_nom' => '9'], ['id_nom' => 9, 'id_tabla' => '']];
        yield 'nada' => [[], ['id_nom' => 0, 'id_tabla' => '']];
        yield 'sel con texto no numérico' => [['sel' => ['abc#n']], ['id_nom' => 0, 'id_tabla' => 'n']];
    }
}
