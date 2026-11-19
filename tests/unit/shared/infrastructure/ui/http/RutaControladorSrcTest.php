<?php

namespace Tests\unit\shared\infrastructure\ui\http;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\ui\http\RutaControladorSrc;

/**
 * El «fallback legacy» de public/index.php hace `require` del fichero que indica la ruta
 * (`?r=/src/...` o la URL). Solo puede cargar controladores HTTP de un módulo de `src/`:
 * con `r=/src/../.env` cualquier usuario con sesión leía (o ejecutaba) cualquier fichero del proyecto.
 */
class RutaControladorSrcTest extends TestCase
{
    private string $raiz;

    protected function setUp(): void
    {
        $this->raiz = dirname(__DIR__, 6);
    }

    public function test_resuelve_un_controlador_de_un_modulo(): void
    {
        $ruta = '/src/usuarios/infrastructure/ui/http/controllers/usuario_info.php';

        $this->assertSame($this->raiz . $ruta, RutaControladorSrc::resolver($this->raiz, $ruta));
    }

    public function test_una_ruta_sin_fichero_no_se_resuelve_y_queda_para_el_router(): void
    {
        $this->assertNull(RutaControladorSrc::resolver($this->raiz, '/src/usuarios/usuario_info'));
    }

    /**
     * @dataProvider rutasQueNoSonUnControlador
     */
    public function test_no_resuelve_nada_que_no_sea_un_controlador(string $ruta): void
    {
        $this->assertNull(RutaControladorSrc::resolver($this->raiz, $ruta));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function rutasQueNoSonUnControlador(): iterable
    {
        yield 'sube a la raíz: .env' => ['/src/../.env'];
        yield 'sube a la raíz: composer.json' => ['/src/../composer.json'];
        yield 'sube a la raíz: script php' => ['/src/../index.php'];
        yield 'sube desde un controlador' => ['/src/usuarios/infrastructure/ui/http/controllers/../../../../../../.env'];
        yield 'sube y vuelve a entrar en otro controlador' => ['/src/usuarios/../usuarios/infrastructure/ui/http/controllers/usuario_info.php'];
        yield 'fichero de src que no es un controlador' => ['/src/shared/global_object.inc'];
        yield 'clase de application' => ['/src/usuarios/application/usuariosLista.php'];
        yield 'configuración de un módulo' => ['/src/usuarios/config/routes.php'];
        yield 'script de línea de órdenes' => ['/src/personas/infrastructure/cli/sacd_resincronizar.php'];
        yield 'trozo incluido por un controlador' => ['/src/notas/infrastructure/ui/http/controllers/comprobar_notas_page_body.inc.php'];
        yield 'byte nulo' => ["/src/usuarios/infrastructure/ui/http/controllers/usuario_info.php\0.txt"];
        yield 'no empieza por /src/' => ['/frontend/usuarios/controller/usuario_lista.php'];
    }
}
