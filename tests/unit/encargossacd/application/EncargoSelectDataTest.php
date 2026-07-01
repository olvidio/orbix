<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoSelectData;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\entity\Local;

final class EncargoSelectDataTest extends TestCase
{
    public function test_idioma_se_resuelve_por_id_locale(): void
    {
        $encargo = $this->createMock(Encargo::class);
        $encargo->method('getId_ubi')->willReturn(0);
        $encargo->method('getIdioma_enc')->willReturn('es_ES.UTF-8');
        $encargo->method('getGrupo_encargo')->willReturn(1);
        $encargo->method('getId_enc')->willReturn(10);
        $encargo->method('getDesc_enc')->willReturn('Encargo test');
        $encargo->method('getDesc_lugar')->willReturn('');

        $encargoRepo = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepo->method('getEncargos')->willReturn([$encargo]);

        $local = $this->createMock(Local::class);
        $local->method('getNomIdiomaAsString')->willReturn('Castellano');

        $localRepo = $this->createMock(LocalRepositoryInterface::class);
        $localRepo->expects($this->once())
            ->method('findById')
            ->with('es_ES.UTF-8')
            ->willReturn($local);
        $localRepo->expects($this->never())->method('getLocales');

        $aplicacion = $this->createMock(EncargoAplicacionService::class);
        $aplicacion->method('getArraySeccion')->willReturn(['1' => 'sv']);

        $useCase = new EncargoSelectData($aplicacion, $encargoRepo, $localRepo);
        $out = $useCase->execute('', 0);

        $this->assertSame('Castellano', $out['filas'][0]['idioma']);
    }

    public function test_idioma_legacy_por_columna_idioma(): void
    {
        $encargo = $this->createMock(Encargo::class);
        $encargo->method('getId_ubi')->willReturn(0);
        $encargo->method('getIdioma_enc')->willReturn('es');
        $encargo->method('getGrupo_encargo')->willReturn(0);
        $encargo->method('getId_enc')->willReturn(11);
        $encargo->method('getDesc_enc')->willReturn('Encargo legacy');
        $encargo->method('getDesc_lugar')->willReturn('');

        $encargoRepo = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepo->method('getEncargos')->willReturn([$encargo]);

        $local = $this->createMock(Local::class);
        $local->method('getNomIdiomaAsString')->willReturn('Castellano');

        $localRepo = $this->createMock(LocalRepositoryInterface::class);
        $localRepo->method('findById')->with('es')->willReturn(null);
        $localRepo->method('getLocales')->with(['idioma' => 'es'])->willReturn([$local]);

        $aplicacion = $this->createMock(EncargoAplicacionService::class);
        $aplicacion->method('getArraySeccion')->willReturn([]);

        $useCase = new EncargoSelectData($aplicacion, $encargoRepo, $localRepo);
        $out = $useCase->execute('', 0);

        $this->assertSame('Castellano', $out['filas'][0]['idioma']);
    }
}
