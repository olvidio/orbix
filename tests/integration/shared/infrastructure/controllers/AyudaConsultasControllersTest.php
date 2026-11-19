<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure\controllers;

use src\menus\domain\PermisoMenuBits;
use src\shared\config\ConfigGlobal;
use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\criteria\AyudaConsultaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\AyudaConsultaEstado;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/**
 * El recorrido completo de una consulta a la ayuda por sus controladores: se pregunta (queda
 * anotada sin usuario), quien preguntó dice que no le sirvió, un administrador la ve en la
 * revisión y la marca como resuelta. La pregunta lleva una marca propia para limpiar solo eso.
 */
final class AyudaConsultasControllersTest extends myTest
{
    private const DIR = __DIR__ . '/../../../../../src/shared/infrastructure/ui/http/controllers/';
    private const PREGUNTA = 'zzqx pregunta de prueba de los controladores de la ayuda';

    private AyudaConsultaRepositoryInterface $repository;

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    private mixed $permisoAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AyudaConsultaRepositoryInterface::class);
        $this->postAnterior = $_POST;
        $this->permisoAnterior = $_SESSION['iPermMenus'] ?? null;
        $this->limpiar();
    }

    public function tearDown(): void
    {
        $this->limpiar();
        $_POST = $this->postAnterior;
        if ($this->permisoAnterior === null) {
            unset($_SESSION['iPermMenus']);
        } else {
            $_SESSION['iPermMenus'] = $this->permisoAnterior;
        }
        parent::tearDown();
    }

    /** @return list<AyudaConsulta> */
    private function propias(): array
    {
        return $this->repository->matching(Criteria::create()->where(Filter::eq(F::Pregunta, self::PREGUNTA)));
    }

    private function limpiar(): void
    {
        foreach ($this->propias() as $consulta) {
            $this->repository->Eliminar($consulta);
        }
    }

    private function comoAdministrador(bool $si): void
    {
        $_SESSION['iPermMenus'] = $si ? PermisoMenuBits::map()['admin_sv'] : PermisoMenuBits::map()['est'];
    }

    /**
     * @param array<string, mixed> $post
     * @return array{success: bool, mensaje: string, data: array<string, mixed>}
     */
    private function ejecutar(string $controlador, array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::DIR . $controlador;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: «" . substr($salida, 0, 200) . '»');
        $data = is_string($json['data'] ?? null) ? json_decode($json['data'], true) : null;
        if (($json['success'] ?? null) === true && ($json['data'] ?? null) !== 'ok') {
            $this->assertIsArray($data, "$controlador: `data` no es un objeto JSON: «" . substr($salida, 0, 300) . '»');
        }

        return [
            'success' => ($json['success'] ?? null) === true,
            'mensaje' => (string) ($json['mensaje'] ?? ''),
            'data' => is_array($data) ? $data : [],
        ];
    }

    public function test_preguntar_anota_la_consulta_sin_el_usuario(): void
    {
        $rta = $this->ejecutar('ayuda_preguntar.php', [
            'pregunta' => self::PREGUNTA,
            'pantalla' => 'frontend/notas/controller/acta_ver.php',
        ]);

        $this->assertTrue($rta['success']);
        $this->assertNotSame('', $rta['data']['ctx_valorar']);

        $propias = $this->propias();
        $this->assertCount(1, $propias);
        $this->assertSame(ConfigGlobal::mi_region_dl(), (string) $propias[0]->getEsquema());
        $this->assertSame('frontend/notas/controller/acta_ver.php', $propias[0]->getPantalla());
        $this->assertSame(count($rta['data']['resultados']), $propias[0]->getN_resultados());
        $this->assertNull($propias[0]->getUtil());
        // Ningún campo guarda quién preguntó.
        $this->assertStringNotContainsString(
            ConfigGlobal::mi_usuario() !== '' ? ConfigGlobal::mi_usuario() : "\0",
            (string) json_encode($propias[0]->toArray())
        );
    }

    public function test_sin_pregunta_no_se_anota_ni_se_puede_valorar(): void
    {
        $rta = $this->ejecutar('ayuda_preguntar.php', []);

        $this->assertTrue($rta['success']);
        $this->assertSame('', $rta['data']['ctx_valorar']);
    }

    public function test_quien_pregunto_dice_que_no_le_sirvio_y_un_administrador_la_resuelve(): void
    {
        $this->comoAdministrador(false);
        $ctx = $this->ejecutar('ayuda_preguntar.php', ['pregunta' => self::PREGUNTA])['data']['ctx_valorar'];

        $rta = $this->ejecutar('ayuda_consulta_valorar.php', [
            'ctx_valorar' => $ctx,
            'util' => '0',
            'comentario' => 'buscaba otra cosa',
        ]);
        $this->assertTrue($rta['success'], $rta['mensaje']);
        $this->assertFalse($this->propias()[0]->getUtil());
        $this->assertSame('buscaba otra cosa', $this->propias()[0]->getComentario());

        // Quien no es administrador no ve la revisión ni puede cambiar estados.
        $lista = $this->ejecutar('ayuda_consultas_lista_data.php', []);
        $this->assertTrue($lista['success']);
        $this->assertSame(['aviso_txt'], array_keys($lista['data']));

        $this->comoAdministrador(true);
        $lista = $this->ejecutar('ayuda_consultas_lista_data.php', []);
        $grupo = null;
        foreach ($lista['data']['grupos'] as $g) {
            if ($g['pregunta'] === self::PREGUNTA) {
                $grupo = $g;
            }
        }
        $this->assertNotNull($grupo, 'La consulta marcada como «no me ha servido» debe salir en la revisión');
        $this->assertSame(1, $grupo['no_sirvio']);
        $this->assertSame(['buscaba otra cosa'], $grupo['comentarios']);
        $this->assertStringContainsString(self::PREGUNTA . "\t", $lista['data']['tsv']);

        $this->comoAdministrador(false);
        $rta = $this->ejecutar('ayuda_consultas_estado_update.php', ['ctx_estado' => $grupo['ctx_resolver']]);
        $this->assertFalse($rta['success']);
        $this->assertSame(AyudaConsultaEstado::PENDIENTE, $this->propias()[0]->getEstado());

        $this->comoAdministrador(true);
        $rta = $this->ejecutar('ayuda_consultas_estado_update.php', ['ctx_estado' => $grupo['ctx_resolver']]);
        $this->assertTrue($rta['success'], $rta['mensaje']);
        $this->assertSame(AyudaConsultaEstado::RESUELTA, $this->propias()[0]->getEstado());
    }

    public function test_valorar_necesita_la_capsula_de_la_respuesta_y_decir_si_o_no(): void
    {
        $rta = $this->ejecutar('ayuda_consulta_valorar.php', ['ctx_valorar' => 'inventada', 'util' => '0']);
        $this->assertFalse($rta['success']);

        $ctx = $this->ejecutar('ayuda_preguntar.php', ['pregunta' => self::PREGUNTA])['data']['ctx_valorar'];
        $rta = $this->ejecutar('ayuda_consulta_valorar.php', ['ctx_valorar' => $ctx]);
        $this->assertFalse($rta['success']);
        $this->assertNull($this->propias()[0]->getUtil());
    }
}
