<?php

declare(strict_types=1);

namespace Tests\unit\actividades\domain\entity;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\actividades\domain\entity\TiposActividades;
use Tests\myTest;

final class TiposActividadesTest extends myTest
{
    private function makeStub(): TipoDeActividadRepositoryInterface
    {
        return new class () implements TipoDeActividadRepositoryInterface {
            public function getArrayTiposActividad(string $sid_tipo_activ = '......'): array
            {
                return [];
            }

            public function getTiposDeProcesos(string $sid_tipo_activ = '......', bool $bdl_propia = true, string $sfsv = ''): array
            {
                return [];
            }

            public function getId_tipoPosibles(string $regexp, string $filtro_regexp_txt): array
            {
                return [];
            }

            public function getNom_tipoPosibles(int $num_digitos, string $filtro_regexp_txt): array
            {
                return ['tipo_nom' => [], 'nom_tipo' => []];
            }

            public function getAsistentesPosibles(array $aText, string $filtro_regex_txt): array
            {
                return [];
            }

            public function getActividadesPosibles(int $num_digitos, array $aText, string $expr_txt): array
            {
                return [];
            }

            public function getSfsvPosibles(array $aText): array
            {
                return [];
            }

            public function getTiposDeActividades(array $aWhere = [], array $aOperators = []): array
            {
                return [];
            }

            public function Eliminar(TipoDeActividad $TipoDeActividad): bool
            {
                return true;
            }

            public function Guardar(TipoDeActividad $TipoDeActividad): bool
            {
                return true;
            }

            public function getErrorTxt(): string
            {
                return '';
            }

            public function getNomTabla(): string
            {
                return '';
            }

            public function datosById(int $id_tipo_activ): array|false
            {
                return false;
            }

            public function findById(int $id_tipo_activ): ?TipoDeActividad
            {
                return null;
            }
        };
    }

    public function test_getId_tipo_activ_after_numeric_id(): void
    {
        $t = new TiposActividades('111001', false, $this->makeStub());
        $this->assertSame('111001', $t->getId_tipo_activ());
    }

    public function test_set_and_get_extendida(): void
    {
        $t = new TiposActividades('', false, $this->makeStub());
        $this->assertFalse($t->getExtendida());
        $t->setExtendida(true);
        $this->assertTrue($t->getExtendida());
    }

    public function test_sfsv_text_round_trip(): void
    {
        $t = new TiposActividades('', false, $this->makeStub());
        $t->setSfsvText('sv');
        $this->assertSame('sv', $t->getSfsvText());
        $this->assertSame(1, $t->getSfsvId());
    }
}
