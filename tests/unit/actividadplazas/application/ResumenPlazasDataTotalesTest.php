<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\application\ResumenPlazasData;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\shared\security\HashB;
use Tests\myTest;

/**
 * Resumen de plazas de una actividad: publicada / de otra dl, totales del
 * servicio de resumen, delegaciones posibles para ceder y cápsula de ceder.
 *
 * Extiende myTest porque las delegaciones salen de los esquemas de la BD comun.
 */
final class ResumenPlazasDataTotalesTest extends myTest
{
    private function uc(?ActividadAll $actividad, array $resumen): ResumenPlazasData
    {
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->method('findById')->willReturn($actividad);
        $svc = $this->createMock(ResumenPlazasService::class);
        $svc->expects($this->once())->method('setId_activ')->with(10);
        $svc->method('getResumen')->willReturn($resumen);
        return new ResumenPlazasData($repo, $svc);
    }

    private function actividad(string $dl, ?bool $publicado): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ(10);
        $a->setDl_org($dl);
        $a->setPublicado($publicado);
        return $a;
    }

    public function test_actividad_propia_publicada_con_totales(): void
    {
        $resumen = ['dlb' => ['calendario' => 20], 'total' => ['actividad' => 40, 'calendario' => 35, 'cedidas' => 5, 'conseguidas' => 3, 'disponibles' => 33, 'ocupadas' => 12]];
        $out = $this->uc($this->actividad('dlb', true), $resumen)->execute(['id_activ' => '10', 'nom_activ' => 'ca']);

        $this->assertArrayNotHasKey('error', $out);
        $this->assertSame([10, 'ca', true, false], [$out['id_activ'], $out['nom_activ'], $out['publicado'], $out['otra_dl']]);
        $this->assertSame([40, 35, 5, 3, 33, 12], [$out['plazas_totales'], $out['tot_calendario'], $out['tot_cedidas'], $out['tot_conseguidas'], $out['tot_disponibles'], $out['tot_ocupadas']]);
        $this->assertSame($resumen, $out['a_plazas']);
        $this->assertContains('H-dlb', array_keys($out['dl_opciones']) + array_values($out['dl_opciones']));
        $this->assertSame(['id_activ' => 10], HashB::open($out['ctx_ceder'], 'plazas_ceder'));
    }

    public function test_actividad_de_otra_dl_sin_publicar_y_plazas_desconocidas(): void
    {
        $out = $this->uc($this->actividad('dlp', null), ['total' => ['actividad' => '?']])->execute(['id_activ' => '10']);
        $this->assertSame([false, true, 0, 0], [$out['publicado'], $out['otra_dl'], $out['plazas_totales'], $out['tot_ocupadas']]);
    }

    public function test_actividad_inexistente_y_resumen_sin_totales(): void
    {
        $out = $this->uc(null, [])->execute(['id_activ' => '10']);
        $this->assertSame([false, false, 0], [$out['publicado'], $out['otra_dl'], $out['tot_calendario']]);
    }

    public function test_sin_actividad_devuelve_error(): void
    {
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');
        $out = (new ResumenPlazasData($repo, $this->createMock(ResumenPlazasService::class)))->execute(['nom_activ' => 'x']);
        $this->assertSame(['falta parametro id_activ', 0, '', []], [$out['error'], $out['id_activ'], $out['ctx_ceder'], $out['dl_opciones']]);
    }
}
