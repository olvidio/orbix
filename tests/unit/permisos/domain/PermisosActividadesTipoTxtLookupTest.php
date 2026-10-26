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

    public function test_otra_dl_tipo_especifico_sin_afecta_cae_al_tipo_padre(): void
    {
        // '11....' solo define asistentesSacd; 'datos' debe resolverse en '1.....'.
        $padre = new XResto('1.....');
        $padre->setOmplir(PermisosActividades::AFECTA['datos'], 2, 31, 31);
        $hijo = new XResto('11....');
        $hijo->setOmplir(PermisosActividades::AFECTA['asistentesSacd'], 16, 7, 0);

        $perm = new PermisosActividadesHarness();
        $perm->replaceDlMap(['1.....' => $padre, '11....' => $hijo]);

        $perm->setActividad(42, '111008', 'dlmE');
        $perm->setFasesCompletadas([2, 16]);
        $perm->forcePropia(false);

        $this->assertTrue($perm->getPermisoActual('datos')->have_perm_activ('ver'));
        $this->assertTrue($perm->getPermisoActual('asistentesSacd')->have_perm_activ('ver'));
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
