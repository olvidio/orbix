<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\SacdFichaUpdate;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacd;
use Tests\factories\encargossacd\EncargoSacdFactory;

final class SacdFichaUpdateTest extends TestCase
{
    public function test_procesa_encargo_en_indice_cero(): void
    {
        $id_nom = 10011;
        $id_enc = 42;
        $id_item = 9001;

        $encargoSacd = (new EncargoSacdFactory())->createSimple($id_item);
        $encargoSacd->setId_enc($id_enc);
        $encargoSacd->setId_nom($id_nom);

        $encargoSacdRepository = $this->createMock(EncargoSacdRepositoryInterface::class);
        $encargoSacdRepository->expects($this->once())
            ->method('getEncargosSacd')
            ->willReturn([$encargoSacd]);

        $aplicacionService = $this->createMock(EncargoAplicacionService::class);
        $aplicacionService->expects($this->once())->method('insert_sacd')->with($id_enc, $id_nom, 2);
        $aplicacionService->expects($this->exactly(3))
            ->method('modificar_horario_sacd')
            ->with($id_item, $id_enc, $id_nom, $this->logicalOr('m', 't', 'v'), $this->anything());

        $useCase = new SacdFichaUpdate(
            $aplicacionService,
            $this->createMock(EncargoRepositoryInterface::class),
            $this->createMock(EncargoSacdObservRepositoryInterface::class),
            $encargoSacdRepository,
        );

        $result = $useCase->execute([
            'id_nom' => $id_nom,
            'enc_num' => 1,
            'observ' => '',
            'id_tipo_enc' => [0 => 5020],
            'id_enc' => [0 => $id_enc],
            'dedic_m' => [0 => '2'],
            'dedic_t' => [0 => '1'],
            'dedic_v' => [0 => ''],
        ]);

        $this->assertSame('', $result['error']);
        $this->assertSame('', $result['mensajes']);
    }
}
