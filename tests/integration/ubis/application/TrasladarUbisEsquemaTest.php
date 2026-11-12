<?php

declare(strict_types=1);

namespace Tests\integration\ubis\application;

use src\shared\infrastructure\GlobalPdo;
use src\ubis\application\TrasladarUbis;
use src\ubis\domain\contracts\TrasladoUbiRepositoryInterface;
use src\ubis\infrastructure\persistence\postgresql\PgTrasladoUbiRepository;
use Tests\myTest;

/**
 * Trasladar centros y casas a otra dl: la dl de destino llega del formulario y
 * acaba dentro del SQL del traslado, así que solo se aceptan nombres de esquema
 * válidos; y si un traslado falla, se avisa (antes siempre respondía éxito).
 * El traslado real no se ejecuta (repositorio simulado salvo en la validación).
 */
final class TrasladarUbisEsquemaTest extends myTest
{
    private function idCentro(): int
    {
        $id = GlobalPdo::get('oDB')->query('SELECT id_ubi FROM u_centros_dl ORDER BY id_ubi LIMIT 1')->fetchColumn();
        if ($id === false) {
            $this->markTestSkipped('Sin centros');
        }
        return (int) $id;
    }

    public function test_dl_destino_que_no_es_un_esquema_no_traslada_nada(): void
    {
        $repo = $this->createMock(TrasladoUbiRepositoryInterface::class);
        $repo->expects($this->never())->method($this->anything());
        $uc = new TrasladarUbis($repo);
        foreach (['dlp', 'H-dlp".u_cdc_dl; DROP TABLE x; --', '', 'H dlp'] as $dl) {
            $this->assertSame('Esquema no válido', $uc->execute(['dl_dst' => $dl, 'sel' => [$this->idCentro()]]), "«{$dl}»");
        }
    }

    public function test_si_el_traslado_falla_se_avisa(): void
    {
        $id = $this->idCentro();
        $repo = $this->createMock(TrasladoUbiRepositoryInterface::class);
        $repo->expects($this->once())->method('trasladoCtr')->with($id, $this->anything(), 'H-dlp')->willReturn(false);
        $msg = (new TrasladarUbis($repo))->execute(['dl_dst' => 'H-dlp', 'sel' => [$id]]);
        $this->assertStringContainsString("id_ubi: $id", $msg);

        $repo = $this->createMock(TrasladoUbiRepositoryInterface::class);
        $repo->method('trasladoCtr')->willReturn(true);
        $this->assertSame('', (new TrasladarUbis($repo))->execute(['dl_dst' => 'H-dlp', 'sel' => [$id]]));
    }

    public function test_el_repositorio_rechaza_esquemas_no_validos_antes_de_conectar(): void
    {
        $repo = new PgTrasladoUbiRepository();
        $malo = 'H-dlp".u_cdc_dl; DROP TABLE x; --';
        $this->assertFalse($repo->trasladoCdc(1, 'H-dlb', $malo));
        $this->assertFalse($repo->trasladoCtr(1, $malo, 'H-dlp'));
        $this->assertFalse($repo->trasladoCdc(1, 'H-dlb', 'dlp'), 'sin región');
    }
}
