<?php

declare(strict_types=1);

namespace Tests\unit\shared\application\copias;

use PHPUnit\Framework\TestCase;
use src\shared\application\copias\AvisarErrorCopias;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\entity\ColaMail;

final class AvisarErrorCopiasTest extends TestCase
{
    public function test_sin_mail_no_encola(): void
    {
        $repo = $this->createMock(ColaMailRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');

        $ok = (new AvisarErrorCopias($repo))->execute(
            '',
            'sv',
            ['errores' => 1, 'tareas' => []],
        );

        $this->assertFalse($ok);
    }

    public function test_mail_invalido_no_encola(): void
    {
        $repo = $this->createMock(ColaMailRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');

        $ok = (new AvisarErrorCopias($repo))->execute(
            'no-es-un-mail',
            'sv',
            ['errores' => 1, 'tareas' => []],
        );

        $this->assertFalse($ok);
    }

    public function test_sin_errores_no_encola(): void
    {
        $repo = $this->createMock(ColaMailRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');

        $ok = (new AvisarErrorCopias($repo))->execute(
            'admin@dlb.example',
            'sv',
            ['errores' => 0, 'tareas' => []],
        );

        $this->assertFalse($ok);
    }

    public function test_con_errores_encola_en_cola_mails(): void
    {
        $guardado = null;
        $repo = $this->createMock(ColaMailRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('Guardar')
            ->willReturnCallback(static function (ColaMail $mail) use (&$guardado): bool {
                $guardado = $mail;
                return true;
            });

        $ok = (new AvisarErrorCopias($repo))->execute(
            'admin@dlb.example',
            'sv',
            [
                'errores' => 1,
                'tareas' => [
                    [
                        'nombre' => 'cp_sacd',
                        'omitida' => null,
                        'resultado' => null,
                        'error' => 'origen caído',
                    ],
                ],
            ],
        );

        $this->assertTrue($ok);
        $this->assertInstanceOf(ColaMail::class, $guardado);
        $this->assertSame('admin@dlb.example', $guardado->getMail_to());
        $this->assertSame(AvisarErrorCopias::WRITED_BY, $guardado->getWrited_by());
        $this->assertStringContainsString('Error al resincronizar copias', (string) $guardado->getSubject());
        $this->assertStringContainsString('cp_sacd  ERROR: origen caído', (string) $guardado->getMessage());
    }

    public function test_excepcion_sin_resultado_tambien_encola(): void
    {
        $repo = $this->createMock(ColaMailRepositoryInterface::class);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $ok = (new AvisarErrorCopias($repo))->execute(
            'admin@dlb.example',
            'sf',
            null,
            'conexión comun caída',
        );

        $this->assertTrue($ok);
    }
}
