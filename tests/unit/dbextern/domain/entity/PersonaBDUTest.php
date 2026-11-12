<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\domain\entity;

use src\dbextern\domain\entity\PersonaBDU;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\factories\dbextern\PersonaBDUFactory;
use Tests\myTest;

final class PersonaBDUTest extends myTest
{
    private PersonaBDU $persona;

    public function setUp(): void
    {
        parent::setUp();
        $this->persona = new PersonaBDU();
    }

    // ---- campos simples -------------------------------------------------

    public function test_valores_por_defecto(): void
    {
        $this->assertSame(0, $this->persona->getIdentif());
        $this->assertSame('', $this->persona->getApenom());
        $this->assertSame('', $this->persona->getDl());
        $this->assertSame('', $this->persona->getFecha_Naci());
        $this->assertNull($this->persona->getFecha_c_fic());
    }

    public function test_set_and_get_campos_simples(): void
    {
        $p = (new PersonaBDUFactory())->createSimple(990000001);
        $this->assertSame(990000001, $p->getIdentif());
        $this->assertSame('dlb', $p->getDl());
        $this->assertSame('Centro Test', $p->getCtr());
        $this->assertSame('Sevilla', $p->getLugar_Naci());
        $this->assertSame('test@example.org', $p->getEmail());
        $this->assertSame('600000000', $p->getTfno_Movil());
        $this->assertSame('Profesora', $p->getProfesion_cargo());
        $this->assertSame('Derecho', $p->getTitulo_Estudios());
        $this->assertSame('Ninguno', $p->getEncargos());
        $this->assertSame('mad', $p->getPertenece_r());
    }

    public function test_set_identif_acepta_string_numerico(): void
    {
        $this->persona->setIdentif('12345');
        $this->assertSame(12345, $this->persona->getIdentif());
    }

    public function test_setters_con_null_dejan_cadena_vacia(): void
    {
        $this->persona->setApenom('X');
        $this->persona->setApenom(null);
        $this->persona->setEmail(null);
        $this->persona->setCe(null);
        $this->persona->setCompartida_con_r(null);
        $this->assertSame('', $this->persona->getApenom());
        $this->assertSame('', $this->persona->getEmail());
        $this->assertSame('', $this->persona->getCe());
        $this->assertSame('', $this->persona->getCompartida_con_r());
    }

    public function test_set_null_all_atributes(): void
    {
        $p = (new PersonaBDUFactory())->createSimple(5);
        $p->setNullAllAtributes();
        $this->assertSame(0, $p->getIdentif());
        $this->assertSame('', $p->getApenom());
        $this->assertSame('', $p->getEncargos());
        $this->assertNull($p->getFecha_c_fic());
    }

    // ---- fechas ----------------------------------------------------------

    public function test_fecha_naci_iso_se_guarda_en_formato_local(): void
    {
        $this->persona->setFecha_Naci('1990-05-17');
        $this->assertSame('17/05/1990', $this->persona->getFecha_Naci());
    }

    /**
     * @dataProvider fechasNaciVaciasProvider
     */
    public function test_fecha_naci_vacia_o_no_string(mixed $valor): void
    {
        $this->persona->setFecha_Naci('1990-05-17');
        $this->persona->setFecha_Naci($valor);
        $this->assertSame('', $this->persona->getFecha_Naci());
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function fechasNaciVaciasProvider(): iterable
    {
        yield 'null' => [null];
        yield 'cadena vacía' => [''];
        yield 'entero' => [19900517];
        yield 'array' => [[]];
    }

    public function test_fecha_naci_datetimelocal(): void
    {
        $f = new DateTimeLocal('1990-05-17');
        $this->persona->setFecha_Naci($f);
        $this->assertSame('17/05/1990', $this->persona->getFecha_Naci(), 'formato fijo j/m/Y, no el del idioma');
    }

    public function test_fecha_c_fic_vacia_o_no_string_es_null(): void
    {
        foreach ([null, '', 20200101, []] as $valor) {
            $this->persona->setFecha_c_fic(new DateTimeLocal('2020-01-01'));
            $this->persona->setFecha_c_fic($valor);
            $this->assertNull($this->persona->getFecha_c_fic());
        }
    }

    public function test_fecha_c_fic_datetimelocal_se_conserva(): void
    {
        $f = new DateTimeLocal('2020-01-01');
        $this->persona->setFecha_c_fic($f);
        $this->assertSame($f, $this->persona->getFecha_c_fic());
    }

    public function test_fecha_c_fic_desde_string_local(): void
    {
        $this->persona->setFecha_c_fic('17/5/2020');
        $f = $this->persona->getFecha_c_fic();
        $this->assertInstanceOf(DateTimeLocal::class, $f);
        $this->assertSame('2020-05-17', $f->format('Y-m-d'));
    }

    // ---- nombre completo --------------------------------------------------

    /**
     * @dataProvider nombresProvider
     * @param array{nombre: string, apellido1: string, apellido2: string, nx1: string, nx2: string} $esperado
     */
    public function test_dividir_nombre_completo(string $apenom, array $esperado): void
    {
        $this->persona->setApenom($apenom);
        $r = $this->persona->dividirNombreCompleto();

        $this->assertSame(
            ['nombre' => $esperado['nombre'], 'apellido1' => $esperado['apellido1'], 'apellido2' => $esperado['apellido2']],
            $r
        );
        $this->assertSame($esperado['nombre'], $this->persona->getNombre());
        $this->assertSame($esperado['apellido1'], $this->persona->getApellido1());
        $this->assertSame($esperado['apellido2'], $this->persona->getApellido2());
        $this->assertSame($esperado['nx1'], $this->persona->getNx1());
        $this->assertSame($esperado['nx2'], $this->persona->getNx2());
    }

    /**
     * @return iterable<string, array{string, array<string, string>}>
     */
    public static function nombresProvider(): iterable
    {
        yield 'dos apellidos simples' => ['García Pérez, Ana', ['nombre' => 'Ana', 'apellido1' => 'García', 'apellido2' => 'Pérez', 'nx1' => '', 'nx2' => '']];
        yield 'un solo apellido' => ['García, Ana', ['nombre' => 'Ana', 'apellido1' => 'García', 'apellido2' => '', 'nx1' => '', 'nx2' => '']];
        yield 'preposiciones en ambos apellidos' => ['de la Torre y Pérez, María José', ['nombre' => 'María José', 'apellido1' => 'de la Torre', 'apellido2' => 'y Pérez', 'nx1' => 'de la', 'nx2' => 'y']];
        yield 'tres tokens, preposición en el segundo' => ['Fernández de Córdoba Ruiz, Luis', ['nombre' => 'Luis', 'apellido1' => 'Fernández de Córdoba', 'apellido2' => 'Ruiz', 'nx1' => '', 'nx2' => '']];
        yield 'tres tokens, sin preposición' => ['López García Pérez, Juan', ['nombre' => 'Juan', 'apellido1' => 'López', 'apellido2' => 'García Pérez', 'nx1' => '', 'nx2' => '']];
        yield 'cuatro tokens' => ['López Gil García Pérez, Juan', ['nombre' => 'Juan', 'apellido1' => 'López Gil', 'apellido2' => 'García Pérez', 'nx1' => '', 'nx2' => '']];
        yield 'sin coma: todo son apellidos, nombre vacío' => ['García Pérez', ['nombre' => '', 'apellido1' => 'García', 'apellido2' => 'Pérez', 'nx1' => '', 'nx2' => '']];
        yield 'vacío' => ['', ['nombre' => '', 'apellido1' => '', 'apellido2' => '', 'nx1' => '', 'nx2' => '']];
        yield 'espacios alrededor' => ['  García Pérez ,  Ana  ', ['nombre' => 'Ana', 'apellido1' => 'García', 'apellido2' => 'Pérez', 'nx1' => '', 'nx2' => '']];
    }

    public function test_sin_prep(): void
    {
        $this->assertSame('Torre', $this->persona->sinPrep('de la Torre'));
        $this->assertSame('Pérez', $this->persona->sinPrep('Pérez'));
        $this->assertSame('Donald', $this->persona->sinPrep('Mac Donald'));
        $this->assertSame('', $this->persona->sinPrep(''));
    }

    public function test_sin_prep_solo_quita_la_preposicion_inicial(): void
    {
        $this->assertSame('Pérez de Gil', $this->persona->sinPrep('Pérez de Gil'));
    }

    public function test_apellidos_sin_preposicion(): void
    {
        $this->persona->setApenom('de la Torre y Pérez, María');
        $this->assertSame('Torre', $this->persona->getApellido1_sinprep());
        $this->assertSame('Pérez', $this->persona->getApellido2_sinprep());
    }

    // ---- CE (centro de estudios) ------------------------------------------

    public function test_ce_con_lugar_e_intervalo(): void
    {
        $this->persona->setCe('2Madrid: 05-08');
        $this->assertSame('2', $this->persona->getCe_num());
        $this->assertSame('Madrid', $this->persona->getCe_lugar());
        $this->assertSame(2005, $this->persona->getCe_ini());
        $this->assertSame(2008, $this->persona->getCe_fin());
    }

    public function test_ce_solo_intervalo(): void
    {
        $this->persona->setCe('95-98');
        $this->assertSame('', $this->persona->getCe_num());
        $this->assertSame('', $this->persona->getCe_lugar());
        $this->assertSame(1995, $this->persona->getCe_ini());
        $this->assertSame(1998, $this->persona->getCe_fin());
    }

    public function test_ce_umbrales_de_siglo(): void
    {
        // inicio: > 30 → 1900, resto → 2000. fin: > 60 → 1900, resto → 2000.
        $this->persona->setCe('30-60');
        $this->assertSame(2030, $this->persona->getCe_ini());
        $this->assertSame(2060, $this->persona->getCe_fin());

        $otra = new PersonaBDU();
        $otra->setCe('31-61');
        $this->assertSame(1931, $otra->getCe_ini());
        $this->assertSame(1961, $otra->getCe_fin());
    }

    public function test_ce_vacio_o_sin_formato(): void
    {
        foreach (['', 'sin formato'] as $ce) {
            $p = new PersonaBDU();
            $p->setCe($ce);
            $this->assertSame('', $p->getCe_num());
            $this->assertSame('', $p->getCe_lugar());
            $this->assertSame('', $p->getCe_ini());
            $this->assertSame('', $p->getCe_fin());
        }
    }

    public function test_ce_ini_y_fin_son_estables_al_leerlos_varias_veces(): void
    {
        $this->persona->setCe('2Madrid: 05-08');
        $this->assertSame(2005, $this->persona->getCe_ini());
        $this->assertSame(2005, $this->persona->getCe_ini());
        $this->assertSame(2008, $this->persona->getCe_fin());
        $this->assertSame(2008, $this->persona->getCe_fin());
    }

    // ---- incorporación -----------------------------------------------------

    public function test_incorporacion_con_formato(): void
    {
        $this->persona->setIncorporacion('Nu 15-03-2001');
        $this->assertSame('Nu', $this->persona->getInc());
        $this->assertSame('2001-03-15', $this->persona->getF_inc());
    }

    public function test_incorporacion_recorta_el_tipo_a_dos_caracteres(): void
    {
        $this->persona->setIncorporacion('elc 1-2-2010');
        $this->assertSame('el', $this->persona->getInc());
        $this->assertSame('2010-2-1', $this->persona->getF_inc());
    }

    public function test_incorporacion_vacia_o_sin_formato(): void
    {
        foreach (['', 'sin fecha'] as $inc) {
            $p = new PersonaBDU();
            $p->setIncorporacion($inc);
            $this->assertSame('', $p->getInc());
            $this->assertSame('', $p->getF_inc());
        }
    }

    public function test_incorporacion_sin_formato_no_borra_los_datos_del_ce(): void
    {
        $this->persona->setCe('2Madrid: 05-08');
        $this->persona->setIncorporacion('');

        // Leer primero la incorporación no debe impedir que el CE se calcule después.
        $this->persona->getInc();
        $this->assertSame('2', $this->persona->getCe_num());
        $this->assertSame('Madrid', $this->persona->getCe_lugar());
    }

    // ---- hidratación desde filas de tmp_bdu ----------------------------------

    public function test_from_array_con_columnas_de_tmp_bdu(): void
    {
        $p = PersonaBDU::fromArray([
            'identif' => 990000010,
            'apenom' => 'García Pérez, Ana',
            'dl' => 'dlb',
            'ctr' => 'Centro',
            'lugar_naci' => 'Sevilla',
            'fecha_naci' => '1990-05-17',
            'email' => 'a@b.c',
            'tfno_movil' => '600',
            'ce' => '2Madrid: 05-08',
            'prof_carg' => 'Profesora',
            'titu_estu' => 'Derecho',
            'encargos' => 'Ninguno',
            'incorp' => 'Nu 15-03-2001',
            'pertenece_r' => 'mad',
            'camb_fic' => '',
            'fecha_c_fic' => null,
            'compartida_con_r' => '',
        ]);

        $this->assertSame(990000010, $p->getIdentif());
        $this->assertSame('Sevilla', $p->getLugar_Naci());
        $this->assertSame('17/05/1990', $p->getFecha_Naci());
        $this->assertSame('600', $p->getTfno_Movil());
        $this->assertSame('Profesora', $p->getProfesion_cargo(), 'prof_carg debe hidratar profesion_cargo');
        $this->assertSame('Derecho', $p->getTitulo_Estudios(), 'titu_estu debe hidratar titulo_estudios');
        $this->assertSame('Nu 15-03-2001', $p->getIncorporacion(), 'incorp debe hidratar incorporacion');
        $this->assertSame('Nu', $p->getInc());
    }
}
