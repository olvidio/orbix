<?php

declare(strict_types=1);

namespace Tests\unit\frontend\security;

use frontend\shared\security\HashFSinFirma;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * La firma `h` es obligatoria: una petición que trae datos y no viene firmada se rechaza, salvo las que
 * no traen datos o ya se protegen por otro medio (cápsula, token de descarga, portada, app móvil).
 */
final class HashFSinFirmaTest extends TestCase
{
    /** @return iterable<string, array{string, array<string, mixed>, array<string, mixed>}> */
    public static function conFirmaObligatoria(): iterable
    {
        yield 'pantalla con un id por POST' => ['/orbix/frontend/personas/controller/personas_editar.php', ['id_nom' => '21', 'obj_pau' => 'PersonaN'], []];
        yield 'pantalla con un id por GET' => ['/orbix/frontend/personas/controller/personas_editar.php', [], ['id_nom' => '21']];
        yield 'endpoint src con datos' => ['/src/personas/personas_editar_data', ['id_nom' => '21'], []];
        yield 'endpoint src en su forma larga' => ['/src/personas/infrastructure/ui/http/controllers/personas_editar_data.php', ['id_nom' => '21'], []];
        yield 'cápsula inventada con más campos' => ['/src/personas/persona_update', ['ctx_update' => 'inventada', 'id_nom' => '21'], []];
        yield 'cápsula de la sesión en una pantalla (no abre cápsulas)' => ['/orbix/frontend/shared/parsedown.php', ['file' => '/.env', 'ctx_x' => 'buena'], []];
        yield 'cápsula de la sesión en un endpoint que no abre cápsulas' => ['/src/shared/tablaDB_depende_datos', ['clase_info' => 'x', 'ctx_x' => 'buena'], []];
        yield 'cápsula de la sesión en un endpoint que no existe' => ['/src/x/y', ['ctx_eliminar' => ['mala', 'buena'], 'mod' => 'eliminar'], []];
        yield 'tk junto a otro parámetro' => ['/src/ubis/plano_download', [], ['tk' => 'x', 'id_direccion' => '5']];
        yield 'tk por POST' => ['/src/ubis/plano_download', ['tk' => 'x'], []];
        yield 'un index.php que no es la portada' => ['/orbix/frontend/zz/controller/index.php', ['id' => '1'], []];
        yield 'ruta de la app con otro prefijo' => ['/src/misas/ver_plan_sacd_data_x', ['id_sacd' => '1'], []];
        yield 'datos en la query y un POST con solo un campo neutro' => ['/orbix/frontend/asistentes/controller/lista_asistentes.php', ['scroll_id' => '1'], ['id_activ' => '21']];
        yield 'datos en la query y un POST con un campo que se llama ctx' => ['/src/encargossacd/sacd_ficha_data', ['ctx' => '1'], ['id_nom' => '21']];
        yield 'datos en la query y la cápsula de la sesión en un endpoint que no la abre' => ['/src/shared/tablaDB_depende_datos', ['ctx_x' => 'buena'], ['clase_info' => 'x']];
        yield 'datos junto a los parámetros de ruta' => ['/src/personas/personas_editar_data', [], ['r' => '/src/misas/ver_plan_sacd_data', 'id_nom' => '21']];
    }

    /**
     * @param array<string, mixed> $post
     * @param array<string, mixed> $get
     */
    #[DataProvider('conFirmaObligatoria')]
    public function test_una_peticion_con_datos_tiene_que_venir_firmada(string $ruta, array $post, array $get): void
    {
        $this->assertTrue(HashFSinFirma::exigida($ruta, $post, $get, static fn (string $c): bool => $c === 'buena'));
    }

    /** @return iterable<string, array{string, array<string, mixed>, array<string, mixed>}> */
    public static function sinFirmaAdmitida(): iterable
    {
        yield 'pantalla sin parámetros' => ['/orbix/frontend/usuarios/controller/usuario_lista.php', [], []];
        yield 'solo parámetros de la interfaz' => ['/orbix/frontend/usuarios/controller/usuario_lista.php', ['PHPSESSID' => '1', 'nav' => 'reset', 'scroll_id_x' => '3'], []];
        yield 'endpoint src sin datos' => ['/src/usuarios/usuario_lista', [], ['r' => '/src/usuarios/usuario_lista', 'dir' => 'x']];
        yield 'solo la cápsula' => ['/src/ubis/plano_borrar', ['ctx_plano_borrar' => 'x'], []];
        yield 'cápsula de la sesión con los campos de su formulario' => ['/src/inventario/cabecera_pie_txt_guardar', ['ctx_guardar' => 'buena', 'texto' => 'x'], []];
        yield 'lista de cápsulas con otro campo' => ['/src/usuarios/usuario_guardar_pwd', ['ctx_guardar' => ['mala', 'buena'], 'password' => 'x'], []];
        yield 'cápsula de la sesión, forma larga' => ['/src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php', ['ctx_guardar' => 'buena', 'password' => 'x'], []];
        yield 'descarga con token' => ['/src/ubis/plano_download', [], ['tk' => 'x', 'r' => '/src/ubis/plano_download', 'dir' => 'x']];
        yield 'portada: entrar' => ['/orbix/index.php', ['username' => 'a', 'password' => 'b'], []];
        yield 'portada sin index.php' => ['/orbix/', ['id_grupmenu' => '3'], []];
        yield 'app móvil' => ['/src/misas/ver_plan_sacd_data', ['id_sacd' => '1'], []];
        yield 'app móvil, forma larga' => ['/src/misas/infrastructure/ui/http/controllers/ver_plan_sacd_data.php', ['id_sacd' => '1'], []];
    }

    /**
     * @param array<string, mixed> $post
     * @param array<string, mixed> $get
     */
    #[DataProvider('sinFirmaAdmitida')]
    public function test_lo_que_no_trae_datos_o_ya_va_protegido_no_necesita_firma(string $ruta, array $post, array $get): void
    {
        $this->assertFalse(HashFSinFirma::exigida($ruta, $post, $get, static fn (string $c): bool => $c === 'buena'));
    }

    public function test_la_lista_de_la_app_movil_es_la_documentada(): void
    {
        $doc = (string) file_get_contents(dirname(__DIR__, 4) . '/docs/catalogo/_endpoints_cliente_movil.md');
        preg_match_all('~`(/src/[a-z_]+/[a-z_0-9]+)`~', $doc, $m);
        $documentadas = array_values(array_unique($m[1]));
        sort($documentadas);
        $lista = HashFSinFirma::RUTAS_CLIENTE_MOVIL;
        sort($lista);

        $this->assertSame($documentadas, $lista);
    }

    public function test_por_defecto_se_corta_y_con_aviso_no(): void
    {
        $previo = $_ENV['ORBIX_HASHF_SIN_FIRMA'] ?? null;
        try {
            unset($_ENV['ORBIX_HASHF_SIN_FIRMA']);
            putenv('ORBIX_HASHF_SIN_FIRMA');
            $this->assertTrue(HashFSinFirma::seCorta());
            $_ENV['ORBIX_HASHF_SIN_FIRMA'] = 'aviso';
            $this->assertFalse(HashFSinFirma::seCorta());
        } finally {
            if ($previo === null) {
                unset($_ENV['ORBIX_HASHF_SIN_FIRMA']);
            } else {
                $_ENV['ORBIX_HASHF_SIN_FIRMA'] = $previo;
            }
        }
    }

    public function test_con_firma_en_el_cuerpo_la_query_no_puede_traer_datos_propios(): void
    {
        $post = ['h' => 'x', 'id_activ' => '5', 'filtro' => 'a'];

        $this->assertFalse(HashFSinFirma::queryFueraDeLaFirma($post, []));
        $this->assertFalse(HashFSinFirma::queryFueraDeLaFirma($post, ['r' => '/src/x/y', 'dir' => 'x', 'PHPSESSID' => '1', 'nav' => 'reset']));
        $this->assertFalse(HashFSinFirma::queryFueraDeLaFirma($post, ['id_activ' => '5']), 'el mismo dato repetido en la query');
        $this->assertFalse(HashFSinFirma::queryFueraDeLaFirma($post, ['hnov' => '1', 'h' => 'y', 'hno' => 'a!b', 'horig' => 'z']), 'los campos de la firma de un enlace sin valores');
        $this->assertTrue(HashFSinFirma::queryFueraDeLaFirma($post, ['id_activ' => '6']), 'otro valor en la query');
        $this->assertTrue(HashFSinFirma::queryFueraDeLaFirma($post, ['id_nom' => '21']), 'un dato que la firma no cubre');
        $this->assertFalse(HashFSinFirma::queryFueraDeLaFirma([], ['h' => 'x', 'id_nom' => '21']), 'firmada por GET: no hay cuerpo');
        $this->assertFalse(HashFSinFirma::queryFueraDeLaFirma(['id_nom' => '21'], ['h' => 'x', 'a' => '1']), 'firma en la query: se valida todo junto');
    }
}
