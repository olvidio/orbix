<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\ui\http;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;
use Tests\myTest;

/**
 * `MultipartUploadGuard`: tamaños de `php.ini`, POST demasiado grande y errores `UPLOAD_ERR_*`. Las ramas que cortan
 * la petición (`exit`) no se ejecutan aquí; las prueban los specs de subida (certificados, actas).
 */
final class MultipartUploadGuardTest extends myTest
{
    /** @var array<string, mixed> */
    private array $serverAnterior = [];
    /** @var array<string, mixed> */
    private array $filesAnterior = [];
    private string|false $memoryLimitAnterior = false;

    public function setUp(): void
    {
        parent::setUp();
        $this->serverAnterior = $_SERVER;
        $this->filesAnterior = $_FILES;
        $this->memoryLimitAnterior = ini_get('memory_limit');
    }

    public function tearDown(): void
    {
        $_SERVER = $this->serverAnterior;
        $_FILES = $this->filesAnterior;
        if ($this->memoryLimitAnterior !== false) {
            ini_set('memory_limit', $this->memoryLimitAnterior);
        }
        parent::tearDown();
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function tamanosProvider(): iterable
    {
        yield 'kilobytes' => ['1048576K', 1048576 * 1024];
        yield 'megas' => ['512M', 512 * 1048576];
        yield 'gigas' => ['2G', 2 * 1073741824];
        yield 'bytes' => ['268435456', 268435456];
        yield 'minúscula' => ['256m', 256 * 1048576];
        yield 'sin límite' => ['-1', -1];
    }

    #[DataProvider('tamanosProvider')]
    public function test_lee_los_tamanos_de_php_ini(string $valor, int $bytes): void
    {
        ini_set('memory_limit', $valor);

        $this->assertSame($bytes, MultipartUploadGuard::parseIniSizeToBytes('memory_limit'));
    }

    public function test_directiva_que_no_existe_es_cero(): void
    {
        $this->assertSame(0, MultipartUploadGuard::parseIniSizeToBytes('directiva_que_no_existe'));
    }

    public function test_post_demasiado_grande_solo_en_un_post_que_supera_post_max_size(): void
    {
        $maximo = MultipartUploadGuard::parseIniSizeToBytes('post_max_size');
        if ($maximo <= 0) {
            $this->markTestSkipped('post_max_size sin límite en este PHP.');
        }

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_LENGTH'] = (string) ($maximo + 1);
        $this->assertTrue(MultipartUploadGuard::isPostTooLarge());

        $_SERVER['CONTENT_LENGTH'] = (string) $maximo;
        $this->assertFalse(MultipartUploadGuard::isPostTooLarge(), 'justo en el límite');

        $_SERVER['CONTENT_LENGTH'] = 'no numérico';
        $this->assertFalse(MultipartUploadGuard::isPostTooLarge());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['CONTENT_LENGTH'] = (string) ($maximo + 1);
        $this->assertFalse(MultipartUploadGuard::isPostTooLarge(), 'no es un POST');
    }

    public function test_exit_if_post_too_large_no_hace_nada_si_cabe(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_LENGTH'] = '10';

        ob_start();
        MultipartUploadGuard::exitIfPostTooLargeJson();
        $this->assertSame('', ob_get_clean());
    }

    public function test_codigo_http_413_solo_para_los_errores_de_tamano(): void
    {
        $this->assertSame(413, MultipartUploadGuard::httpStatusForPhpUploadError(UPLOAD_ERR_INI_SIZE));
        $this->assertSame(413, MultipartUploadGuard::httpStatusForPhpUploadError(UPLOAD_ERR_FORM_SIZE));
        foreach ([UPLOAD_ERR_OK, UPLOAD_ERR_PARTIAL, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION] as $codigo) {
            $this->assertSame(200, MultipartUploadGuard::httpStatusForPhpUploadError($codigo), (string) $codigo);
        }
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function mensajesProvider(): iterable
    {
        yield 'parcial' => [UPLOAD_ERR_PARTIAL, 'El archivo acta.pdf se subió solo en parte.'];
        yield 'sin fichero' => [UPLOAD_ERR_NO_FILE, 'No se ha seleccionado ningún archivo.'];
        yield 'sin temporal' => [UPLOAD_ERR_NO_TMP_DIR, 'Error del servidor: falta el directorio temporal para subidas.'];
        yield 'no se puede escribir' => [UPLOAD_ERR_CANT_WRITE, 'No se pudo guardar el archivo acta.pdf en disco.'];
        yield 'extensión' => [UPLOAD_ERR_EXTENSION, 'La subida fue bloqueada por una extensión de PHP.'];
        yield 'otro' => [99, 'Error al subir el archivo acta.pdf.'];
    }

    #[DataProvider('mensajesProvider')]
    public function test_mensaje_de_cada_error_de_subida(int $codigo, string $mensaje): void
    {
        $this->assertSame($mensaje, MultipartUploadGuard::messageForPhpUploadError($codigo, 'acta.pdf'));
    }

    public function test_los_errores_de_tamano_dicen_el_maximo_en_megas(): void
    {
        foreach ([UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE] as $codigo) {
            $this->assertMatchesRegularExpression(
                '/^El archivo supera el tamaño máximo permitido por el servidor \(aprox\. [0-9.]+ MB\)\.$/',
                MultipartUploadGuard::messageForPhpUploadError($codigo, 'acta.pdf')
            );
        }
    }

    public function test_un_fichero_subido_bien_devuelve_nombre_y_temporal(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_LENGTH'] = '100';
        $_FILES['acta_pdf'] = ['name' => 'acta.pdf', 'tmp_name' => '/tmp/phpABC', 'error' => UPLOAD_ERR_OK, 'size' => 100];

        $this->assertSame(['name' => 'acta.pdf', 'tmp_name' => '/tmp/phpABC'], MultipartUploadGuard::requireUploadedFileOrExit('acta_pdf'));
    }
}
