<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\value_objects\ActividadTipoIdTxt;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XResto;

/**
 * El listado actividad_select (id_tipo_activ=17, SR) oculta el SACD si la
 * clave de permiso no coincide con el patrón con puntos al subir el árbol.
 */
final class PermisosActividadesTipoTxtLookupTest extends TestCase
{
    public function test_sr_activity_sees_sacd_when_db_key_was_unpadded_17(): void
    {
        $perm = $this->harnessWithSacdAt(ActividadTipoIdTxt::canonicalize('17'));

        $perm->setActividad(42, '171001', 'dlb');
        $perm->setFasesCompletadas([10]);
        $perm->forcePropia(true);

        $oPermSacd = $perm->getPermisoActual('sacd');
        $this->assertTrue($oPermSacd->have_perm_activ('ver'));
    }

    public function test_sr_activity_sees_sacd_when_session_key_was_zero_padded(): void
    {
        $raw = new XResto('000017');
        $raw->setOmplir(PermisosActividades::AFECTA['sacd'], 10, 3, 3);

        $perm = new PermisosActividadesHarness();
        $perm->replaceDlMap(['000017' => $raw]);

        $perm->setActividad(42, '171234', 'dlb');
        $perm->setFasesCompletadas([10]);
        $perm->forcePropia(true);

        $oPermSacd = $perm->getPermisoActual('sacd');
        $this->assertTrue($oPermSacd->have_perm_activ('ver'));
    }

    public function test_lookup_finds_php_integer_key_17_without_restore(): void
    {
        $resto = new XResto('17');
        $resto->setOmplir(PermisosActividades::AFECTA['sacd'], 10, 3, 3);

        $perm = new PermisosActividadesHarness();
        $ref = new \ReflectionClass(PermisosActividades::class);
        $propDl = $ref->getProperty('aPermDl');
        $propDl->setAccessible(true);
        $propDl->setValue($perm, ['17' => $resto]);
        $propOtras = $ref->getProperty('aPermOtras');
        $propOtras->setAccessible(true);
        $propOtras->setValue($perm, ['17' => $resto]);

        $perm->setActividad(42, '171001', 'dlb');
        $perm->setFasesCompletadas([10]);
        $perm->forcePropia(true);

        $oPermSacd = $perm->getPermisoActual('sacd');
        $this->assertTrue($oPermSacd->have_perm_activ('ver'));
    }

    public function test_unnormalized_key_17_does_not_match_without_canonicalize(): void
    {
        $this->assertNotSame('17', ActividadTipoIdTxt::canonicalize('17'));
        $this->assertSame('17....', ActividadTipoIdTxt::canonicalize('17'));
    }

    private function harnessWithSacdAt(string $tipoKey): PermisosActividadesHarness
    {
        $resto = new XResto($tipoKey);
        $resto->setOmplir(PermisosActividades::AFECTA['sacd'], 10, 3, 3);

        $perm = new PermisosActividadesHarness();
        $perm->replaceDlMap([$tipoKey => $resto]);

        return $perm;
    }
}

/**
 * Evita SQL del constructor; permite sembrar aPermDl.
 */
final class PermisosActividadesHarness extends PermisosActividades
{
    public function __construct()
    {
        $this->idUsuario = 1;
        $this->bpropia = true;
    }

    /**
     * @param array<string, XResto> $map
     */
    public function replaceDlMap(array $map): void
    {
        $ref = new \ReflectionClass(PermisosActividades::class);
        $method = $ref->getMethod('restoreXRestoMap');
        $method->setAccessible(true);
        $normalized = $method->invoke(null, $map);

        $propDl = $ref->getProperty('aPermDl');
        $propDl->setAccessible(true);
        $propDl->setValue($this, $normalized);

        $propOtras = $ref->getProperty('aPermOtras');
        $propOtras->setAccessible(true);
        $propOtras->setValue($this, $normalized);
    }

    public function forcePropia(bool $propia): void
    {
        $this->setPropia($propia);
    }
}
