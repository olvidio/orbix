<?php

declare(strict_types=1);

namespace Tests\integration\devel_db_admin\application;

use PHPUnit\Framework\Attributes\DataProvider;
use src\devel_db_admin\application\ApptablesUpdate;
use src\devel_db_admin\application\CopiarEsquema;
use src\devel_db_admin\application\CrearEsquema;
use src\devel_db_admin\application\EliminarEsquemaDl;
use src\devel_db_admin\application\RenombrarEsquema;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;

/**
 * Las herramientas de esquemas rechazan una región, dl o esquema con caracteres que no son de una sigla
 * antes de tocar nada (revisión de seguridad del 2026-11-18). Antes, renombrar con una dl con comillas
 * renombraba de verdad los roles del esquema de origen.
 *
 * Por si algún día la validación falla, todos los nombres son de esquemas y roles que no existen (`Zz`) y
 * el test solo corre contra las BD de pruebas: los roles de PostgreSQL y los `*.roles.inc` son comunes a
 * desarrollo y pruebas. `CrearUsuarios` no se prueba aquí (crea roles y escribe la configuración antes de
 * cualquier otra cosa): su validación es la misma, {@see \Tests\unit\devel_db_admin\domain\SiglasEsquemaTest}.
 */
final class EsquemaSiglasValidadasTest extends myTest
{
    public function setUp(): void
    {
        if (getenv('WEBDIR') !== 'pruebas') {
            $this->markTestSkipped('Herramientas de esquemas: solo en el entorno de pruebas (tools/qa/phpunit_protegido.php).');
        }
        parent::setUp();
    }

    /** @return iterable<string, array{string, string}> */
    public static function siglasNoValidas(): iterable
    {
        yield 'comilla en la dl' => ['Zz', "zz', 'g') OR true --"];
        yield 'comilla en la región' => ["Zz' OR '1'='1", 'dlzz'];
        yield 'comillas dobles' => ['Zz', 'zz"; SELECT 1; --'];
        yield 'dólar' => ['Zz', 'zz$$; --'];
        yield 'vacía' => ['', 'dlzz'];
    }

    #[DataProvider('siglasNoValidas')]
    public function test_renombrar_rechaza_la_sigla(string $region, string $dl): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new RenombrarEsquema($this->createStub(DbSchemaRepositoryInterface::class)))->ejecutar('Zz-noexiste', $region, $dl, 1, 1, 0);
    }

    #[DataProvider('siglasNoValidas')]
    public function test_eliminar_rechaza_la_sigla(string $region, string $dl): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new EliminarEsquemaDl())->ejecutar($region, $dl, 1, 1, 0);
    }

    #[DataProvider('siglasNoValidas')]
    public function test_crear_rechaza_la_sigla(string $region, string $dl): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CrearEsquema($this->createStub(DbSchemaRepositoryInterface::class)))->ejecutar('Zz-noexiste', $region, $dl, 1, 1, 0);
    }

    #[DataProvider('siglasNoValidas')]
    public function test_copiar_rechaza_la_sigla(string $region, string $dl): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CopiarEsquema())->ejecutar('Zz-noexiste', $region, $dl, 1, 1, 0);
    }

    public function test_crear_y_copiar_rechazan_un_esquema_de_referencia_no_valido(): void
    {
        foreach ([
            fn () => (new CrearEsquema($this->createStub(DbSchemaRepositoryInterface::class)))->ejecutar("Zz-noexiste'; --", 'Zz', 'dlzz', 1, 1, 0),
            fn () => (new CopiarEsquema())->ejecutar("Zz-noexiste'; --", 'Zz', 'dlzz', 1, 1, 0),
        ] as $llamada) {
            try {
                $llamada();
                $this->fail('Se esperaba InvalidArgumentException');
            } catch (\InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_tablas_de_una_app_rechaza_el_esquema(): void
    {
        $sesionPrevia = $_SESSION;
        $_SESSION['config']['a_apps'] = ['cambios' => 5];
        try {
            $this->expectException(\InvalidArgumentException::class);
            (new ApptablesUpdate())->ejecutar(['id_app' => 5, 'esquema' => "Zz') OR true; --v", 'accion' => 'crear_esquema']);
        } finally {
            $_SESSION = $sesionPrevia;
        }
    }
}
