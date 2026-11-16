<?php

declare(strict_types=1);

namespace Tests\integration\personas\application;

use src\personas\application\services\PersonaFinderService;
use src\personas\application\TrasladoFormData;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use Tests\myTest;

/**
 * Centros que ofrece el formulario de traslado, contra la BD de pruebas: los activos que no son cgi ni iglesias.
 */
final class TrasladoFormDataCentrosTest extends myTest
{
    public function test_ofrece_los_centros_activos_que_no_son_cgi_ni_iglesias(): void
    {
        $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $esperados = [];
        $tipos = [];
        foreach (array_keys($repo->getArrayCentros('WHERE true')) as $id) {
            $c = $repo->findById((int) $id);
            $tipo = (string) $c?->getTipo_ctr();
            if ($c !== null && $c->isActive() && !preg_match('/^(cgi|igl)/', $tipo)) {
                $esperados[] = (int) $id;
                $tipos[$tipo] = true;
            }
        }
        if ($esperados === []) {
            $this->markTestSkipped('La BD de pruebas no tiene centros.');
        }

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->willReturn($this->createMock(PersonaDl::class));
        $uc = new TrasladoFormData(
            $finder,
            $repo,
            $GLOBALS['container']->get(SituacionRepositoryInterface::class),
            $GLOBALS['container']->get(DelegacionDropdown::class),
        );

        $ofrecidos = array_map('intval', array_keys($uc->execute(['id_pau' => '1'])['opciones_centros']));

        sort($esperados);
        sort($ofrecidos);
        $this->assertSame($esperados, $ofrecidos, 'tipos esperados: ' . implode(',', array_keys($tipos)));
    }
}
