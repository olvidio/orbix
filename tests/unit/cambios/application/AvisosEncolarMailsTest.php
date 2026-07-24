<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\cambios\application\ActividadParaAvisoLookup;
use src\cambios\application\AvisosEncolarMails;
use src\cambios\application\CambioAvisoTxtBuilder;
use src\cambios\application\CambioParaAvisoLookup;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\cambios\domain\entity\CambioUsuario;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;

final class AvisosEncolarMailsTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'sfsv' => 1,
            'esquema' => 'H-dlbv',
        ];
        $_SESSION['config'] = [
            'a_apps' => [],
            'app_installed' => [],
        ];
    }

    public function test_encola_un_mail_por_usuario_y_borra_cambio_usuario(): void
    {
        $cambioUsuario = new CambioUsuario();
        $cambioUsuario->setId_item(100);
        $cambioUsuario->setId_schema_cambio(3001);
        $cambioUsuario->setId_item_cambio(55);
        $cambioUsuario->setId_usuario(7);
        $cambioUsuario->setSfsv(1);
        $cambioUsuario->setAviso_tipo(AvisoTipoId::TIPO_MAIL);
        $cambioUsuario->setAvisado(false);

        $cambioUsuarioRepo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $cambioUsuarioRepo->expects($this->exactly(2))
            ->method('getCambiosUsuario')
            ->willReturnCallback(static function (array $aWhere) use ($cambioUsuario): array {
                if (isset($aWhere['id_item_cambio'])) {
                    return [$cambioUsuario];
                }
                return [$cambioUsuario];
            });
        $cambioUsuarioRepo->expects($this->once())
            ->method('Eliminar')
            ->with($cambioUsuario)
            ->willReturn(true);

        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmailAsString')->willReturn('user@example.com');

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(7)->willReturn($usuario);

        $preferenciaRepo = $this->createMock(PreferenciaRepositoryInterface::class);
        $preferenciaRepo->method('findById')->willReturn(null);

        $cambio = new Cambio();
        $cambio->setId_tipo_cambio(Cambio::TIPO_CMB_DELETE);
        $cambio->setObjeto('Asistente');
        $cambio->setId_activ(10);
        $cambio->setPropiedad('id_nom');
        $cambio->setValor_old('10012845');
        $cambio->setValor_new(null);
        $cambio->setQuien_cambia(7);
        $cambio->setSfsv_quien_cambia(1);
        $cambio->setTimestamp_cambio(new DateTimeLocal('2026-07-23 10:00:00'));
        $cambio->setDl_org('dlb');

        $dlRepo = $this->createMock(CambioDlRepositoryInterface::class);
        $dlRepo->method('getCambios')->willReturn([$cambio]);
        $publicRepo = $this->createMock(CambioRepositoryInterface::class);
        $publicRepo->method('getCambios')->willReturn([]);
        $cambioLookup = new CambioParaAvisoLookup($publicRepo, $dlRepo);

        $persona = $this->createMock(PersonaDl::class);
        $persona->method('getPrefApellidosNombre')->willReturn('García López, Juan');
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(10012845)->willReturn($persona);

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getNom_activ')->willReturn('cv agd Castelldaura');
        $allRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepo->method('findById')->willReturn($actividad);
        $exRepo = $this->createMock(ActividadExRepositoryInterface::class);
        $exRepo->method('findById')->willReturn(null);

        $txtBuilder = new CambioAvisoTxtBuilder(
            new ActividadParaAvisoLookup($allRepo, $exRepo),
            $this->createMock(CambioRepositoryInterface::class),
            $finder,
            $this->createMock(TipoTarifaRepositoryInterface::class),
            $this->createMock(RepeticionRepositoryInterface::class),
            $this->createMock(ActividadFaseRepositoryInterface::class),
        );

        /** @var list<ColaMail> $encolados */
        $encolados = [];
        $colaRepo = $this->createMock(ColaMailRepositoryInterface::class);
        $colaRepo->expects($this->once())
            ->method('Guardar')
            ->willReturnCallback(function (ColaMail $mail) use (&$encolados): bool {
                $encolados[] = $mail;
                return true;
            });

        $useCase = new AvisosEncolarMails(
            $cambioUsuarioRepo,
            $usuarioRepo,
            $preferenciaRepo,
            $cambioLookup,
            $txtBuilder,
            $colaRepo,
        );

        $resumen = $useCase->execute(dispararGenerarTabla: false);

        $this->assertSame(1, $resumen['encolados']);
        $this->assertSame(0, $resumen['usuarios_sin_email']);
        $this->assertSame(1, $resumen['total_avisos']);
        $this->assertCount(1, $encolados);
        $this->assertSame('user@example.com', $encolados[0]->getMail_to());
        $this->assertSame(AvisosEncolarMails::WRITED_BY, $encolados[0]->getWrited_by());
        $this->assertStringContainsString('García López, Juan', (string) $encolados[0]->getMessage());
        $this->assertStringNotContainsString('10012845', (string) $encolados[0]->getMessage());
    }

    public function test_sin_email_no_encola(): void
    {
        $cambioUsuario = new CambioUsuario();
        $cambioUsuario->setId_item(100);
        $cambioUsuario->setId_schema_cambio(3001);
        $cambioUsuario->setId_item_cambio(55);
        $cambioUsuario->setId_usuario(7);
        $cambioUsuario->setSfsv(1);
        $cambioUsuario->setAviso_tipo(AvisoTipoId::TIPO_MAIL);
        $cambioUsuario->setAvisado(false);

        $cambioUsuarioRepo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $cambioUsuarioRepo->method('getCambiosUsuario')->willReturn([$cambioUsuario]);
        $cambioUsuarioRepo->expects($this->never())->method('Eliminar');

        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmailAsString')->willReturn(null);

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn($usuario);

        $colaRepo = $this->createMock(ColaMailRepositoryInterface::class);
        $colaRepo->expects($this->never())->method('Guardar');

        $useCase = new AvisosEncolarMails(
            $cambioUsuarioRepo,
            $usuarioRepo,
            $this->createMock(PreferenciaRepositoryInterface::class),
            new CambioParaAvisoLookup(
                $this->createMock(CambioRepositoryInterface::class),
                $this->createMock(CambioDlRepositoryInterface::class),
            ),
            new CambioAvisoTxtBuilder(
                new ActividadParaAvisoLookup(
                    $this->createMock(ActividadAllRepositoryInterface::class),
                    $this->createMock(ActividadExRepositoryInterface::class),
                ),
                $this->createMock(CambioRepositoryInterface::class),
                $this->createMock(PersonaFinderService::class),
                $this->createMock(TipoTarifaRepositoryInterface::class),
                $this->createMock(RepeticionRepositoryInterface::class),
                $this->createMock(ActividadFaseRepositoryInterface::class),
            ),
            $colaRepo,
        );

        $resumen = $useCase->execute(dispararGenerarTabla: false);

        $this->assertSame(0, $resumen['encolados']);
        $this->assertSame(0, $resumen['total_avisos']);
    }
}
