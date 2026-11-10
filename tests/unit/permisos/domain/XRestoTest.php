<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain;

use PHPUnit\Framework\TestCase;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XResto;

final class XRestoTest extends TestCase
{
    private const SACD = PermisosActividades::AFECTA['sacd']; // 4
    private const DATOS = PermisosActividades::AFECTA['datos']; // 1

    public function test_set_omplir_y_get_perm(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 10, 3, 7);

        $this->assertSame(3, $x->getPerm(self::SACD, 10, 'on'));
        $this->assertSame(7, $x->getPerm(self::SACD, 10, 'off'));
    }

    public function test_get_perm_de_lo_no_definido_es_cero(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 10, 3, 7);

        $this->assertSame(0, $x->getPerm(self::SACD, 99, 'on'), 'fase distinta');
        $this->assertSame(0, $x->getPerm(self::DATOS, 10, 'on'), 'afecta distinto');
        $this->assertSame(0, $x->getPerm(self::SACD, 10, 'otro'), 'on/off desconocido');
    }

    public function test_un_permiso_a_cero_se_lee_como_cero(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 10, 0, 0);

        $this->assertSame(0, $x->getPerm(self::SACD, 10, 'on'));
    }

    public function test_set_omplir_sobrescribe_el_valor_anterior(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 10, 3, 7);
        $x->setOmplir(self::SACD, 10, 15, 31);

        $this->assertSame(15, $x->getPerm(self::SACD, 10, 'on'));
        $this->assertSame(31, $x->getPerm(self::SACD, 10, 'off'));
    }

    public function test_la_fase_como_string_numerico_equivale_al_entero(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, '10', 3, 7);

        $this->assertSame(3, $x->getPerm(self::SACD, 10, 'on'));
    }

    public function test_has_afecta_compara_por_bits(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 10, 3, 3);

        $this->assertTrue($x->hasAfecta(self::SACD));
        $this->assertFalse($x->hasAfecta(self::DATOS));
        $this->assertFalse($x->hasAfecta(0));
    }

    public function test_has_afecta_con_una_suma_de_bits_en_la_clave(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::DATOS | self::SACD, 10, 3, 3);

        $this->assertTrue($x->hasAfecta(self::DATOS));
        $this->assertTrue($x->hasAfecta(self::SACD));
        $this->assertFalse($x->hasAfecta(PermisosActividades::AFECTA['ctr']));
    }

    public function test_has_afecta_vacio(): void
    {
        $this->assertFalse((new XResto('17....'))->hasAfecta(self::DATOS));
    }

    public function test_get_fase_ref_es_la_primera_fase_del_afecta(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 12, 3, 3);
        $x->setOmplir(self::SACD, 10, 3, 3);

        $this->assertSame(12, $x->getFaseRef(self::SACD), 'orden de inserción: primero 12');
        $this->assertNull($x->getFaseRef(self::DATOS));
    }

    public function test_get_fases_recoge_las_de_todos_los_afecta(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 10, 3, 3);
        $x->setOmplir(self::DATOS, 2, 3, 3);
        $x->setOmplir(self::DATOS, 5, 3, 3);

        $this->assertSame([10, 2, 5], $x->getFases());
        $this->assertSame([], (new XResto('1.....'))->getFases());
    }

    public function test_set_ordenar_ordena_por_afecta(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 10, 3, 3);
        $x->setOmplir(self::DATOS, 2, 3, 3);

        $x->setOrdenar();

        $this->assertSame([2, 10], $x->getFases(), 'datos (1) antes que sacd (4)');
    }

    public function test_merge_from_se_queda_con_el_mayor_permiso(): void
    {
        $a = new XResto('17....');
        $a->setOmplir(self::SACD, 10, 3, 31);
        $b = new XResto('17');
        $b->setOmplir(self::SACD, 10, 15, 7);

        $a->mergeFrom($b);

        $this->assertSame(15, $a->getPerm(self::SACD, 10, 'on'));
        $this->assertSame(31, $a->getPerm(self::SACD, 10, 'off'));
    }

    public function test_merge_from_incorpora_afecta_y_fases_nuevos_y_ordena(): void
    {
        $a = new XResto('17....');
        $a->setOmplir(self::SACD, 10, 3, 3);
        $b = new XResto('17');
        $b->setOmplir(self::DATOS, 2, 7, 7);
        $b->setOmplir(self::SACD, 11, 15, 15);

        $a->mergeFrom($b);

        $this->assertSame(7, $a->getPerm(self::DATOS, 2, 'on'));
        $this->assertSame(15, $a->getPerm(self::SACD, 11, 'on'));
        $this->assertSame(3, $a->getPerm(self::SACD, 10, 'on'), 'lo que ya tenía se conserva');
        $this->assertSame([2, 10, 11], $a->getFases());
    }

    public function test_merge_from_no_modifica_el_origen(): void
    {
        $a = new XResto('17....');
        $b = new XResto('17');
        $b->setOmplir(self::SACD, 10, 15, 15);

        $a->mergeFrom($b);

        $this->assertSame(15, $b->getPerm(self::SACD, 10, 'on'));
        $this->assertSame([10], $b->getFases());
    }

    public function test_serializacion_conserva_los_datos(): void
    {
        $x = new XResto('17....');
        $x->setOmplir(self::SACD, 10, 3, 7);

        $copia = unserialize(serialize($x));

        $this->assertInstanceOf(XResto::class, $copia);
        $this->assertSame(3, $copia->getPerm(self::SACD, 10, 'on'));
        $this->assertSame(7, $copia->getPerm(self::SACD, 10, 'off'));
    }
}
