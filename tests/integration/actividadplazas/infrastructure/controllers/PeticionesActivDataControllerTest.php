<?php

declare(strict_types=1);

namespace Tests\integration\actividadplazas\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;
use Tests\support\DetectaEscriturasEnBd;

/**
 * `/src/actividadplazas/peticiones_activ_data` (pantalla «petición ca / crt» de una persona) solo lee.
 *
 * Antes borraba, al abrirse, las peticiones de la persona cuya actividad no salía en la lista de
 * candidatas (actividad terminada, despublicada, de otro curso…; todas si la persona era de paso).
 * Ahora esas peticiones solo se ocultan (duda 19 del plan de revisión de tests).
 *
 * La petición de prueba se crea y el controlador se ejecuta dentro de una transacción que se deshace
 * ({@see DetectaEscriturasEnBd}): el test no deja nada en la BD.
 */
final class PeticionesActivDataControllerTest extends myTest
{
    use DetectaEscriturasEnBd;

    private const CONTROLADOR = __DIR__ . '/../../../../../src/actividadplazas/infrastructure/ui/http/controllers/peticiones_activ_data.php';
    private const ID_NOM = 990099001;
    private const ID_ACTIV = 990099002;

    /** @return iterable<string, array{string}> */
    public static function tiposDePersona(): iterable
    {
        yield 'numerario: la actividad pedida no está entre las candidatas' => ['n'];
        yield 'persona de paso: no hay candidatas' => ['pn'];
    }

    #[DataProvider('tiposDePersona')]
    public function test_abrir_la_pantalla_no_borra_la_peticion_que_no_esta_en_la_lista(string $na): void
    {
        $postAnterior = $_POST;
        $salida = '';
        try {
            $bd = $this->escriturasDe(function () use ($na, &$salida): void {
                $peticion = new PlazaPeticion();
                $peticion->setId_nom(self::ID_NOM);
                $peticion->setId_activ(self::ID_ACTIV);
                $peticion->setOrden(1);
                $peticion->setTipo('ca');
                $repositorio = DependencyResolver::get(PlazaPeticionRepositoryInterface::class);
                $this->assertNotFalse($repositorio->Guardar($peticion));

                $_POST = ['id_nom' => (string) self::ID_NOM, 'na' => $na, 'sactividad' => 'ca', 'todos' => '1'];
                InProcessSrcDispatch::begin();
                ob_start();
                try {
                    require self::CONTROLADOR;
                } finally {
                    $salida = (string) ob_get_clean();
                    InProcessSrcDispatch::end();
                }
            });
        } finally {
            $_POST = $postAnterior;
        }

        $json = json_decode($salida, true);
        $this->assertIsArray($json, 'debe responder JSON; respondió: ' . substr($salida, 0, 200));
        $this->assertTrue($json['success'] ?? false, $salida);
        $datos = is_string($json['data'] ?? null) ? json_decode($json['data'], true) : null;
        $this->assertIsArray($datos);
        $this->assertSame('', $datos['sid_activ'] ?? null, 'la petición que no está en la lista no se enseña');

        // insertadas/modificadas/borradas: solo el alta que hace el test (1/0/0).
        $enPeticiones = array_values(array_filter($bd['escrituras'], static fn (string $t): bool => str_contains($t, 'plazas_peticion')));
        $this->assertCount(1, $enPeticiones, implode(', ', $bd['escrituras']));
        $this->assertStringEndsWith('(1/0/0)', $enPeticiones[0], 'el controlador no borra la petición');
        $this->assertSame([], $bd['abortadas']);
    }
}
