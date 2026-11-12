<?php

declare(strict_types=1);

namespace Tests\unit\actividades\domain;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\NivelStgr;
use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\entity\TipoDeActividad;
use src\actividades\domain\entity\TiposActividades;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\RepeticionTipo;

/**
 * Huecos del dominio de actividades: nivel stgr por defecto según el tipo,
 * textos de TiposActividades, metadatos de NivelStgr/Repeticion y procesos
 * sv/sf de TipoDeActividad.
 */
final class ActividadesDominioRestoTest extends TestCase
{
    // --- NivelStgrId ---

    public static function providerNivelPorTipo(): \Generator
    {
        foreach ([112000, 112020, 133000, 133020] as $t) {
            yield "bienio $t" => [$t, 1];
        }
        yield 'cuadrienio' => [112021, 2];
        yield 'semestre n' => [112112, 2];
        yield 'cuadrienio agd' => [133021, 3];
        yield 'bienio y cuadrienio' => [133105, 10];
        foreach ([112023, 133023, 212023, 233023] as $t) {
            yield "repaso $t" => [$t, 4];
        }
        yield 'ceagd' => [133016, 5];
        yield 'otro tipo' => [141000, 0];
    }

    #[DataProvider('providerNivelPorTipo')]
    public function test_nivel_stgr_por_defecto_segun_el_tipo(int $tipo, int $nivel): void
    {
        $this->assertSame($nivel, NivelStgrId::generarNivelStgr($tipo));
        $this->assertSame($nivel, NivelStgrId::generarNivelStgr((string) $tipo));
    }

    public function test_nivel_stgr_lista_corta_e_invalidos(): void
    {
        $this->assertSame([NivelStgrId::B, NivelStgrId::C1, NivelStgrId::C2], array_keys(NivelStgrId::getArrayNivelStgrOn()));
        $this->expectException(\InvalidArgumentException::class);
        new NivelStgrId(99);
    }

    // --- TiposActividades ---

    public static function providerNomPasarela(): \Generator
    {
        yield 'crt n sv' => ['111', 'curso de retiro numerarios'];
        yield 'crt n sf' => ['211', 'curso de retiro numerarias'];
        yield 'ca nax' => ['222', 'curso anual numerarias auxiliares'];
        yield 'cv agd sv' => ['133', 'convivencia agregados'];
        yield 'cv agd sf' => ['233', 'convivencia agregadas'];
        yield 'cve sg sv' => ['154', 'convivencia coperadores'];
        yield 'cve sg sf' => ['254', 'convivencia coperadoras'];
        yield 'cv-crt s: sin texto de asistentes' => ['145', 'Actividad '];
    }

    #[DataProvider('providerNomPasarela')]
    public function test_nombre_para_la_pasarela(string $id, string $esperado): void
    {
        $this->assertSame($esperado, (new TiposActividades($id))->getNomPasarela());
    }

    private function repoTipos(): TipoDeActividadRepositoryInterface
    {
        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('getNom_tipoPosibles')->willReturn([
            'tipo_nom' => ['000' => 'general', '021' => 'cuadrienio'],
            'nom_tipo' => ['general' => '000', 'cuadrienio' => '021'],
        ]);
        $repo->method('getAsistentesPosibles')->willReturnCallback(
            fn (array $aText, string $re): array => $re === '^1.' ? [1 => 'n', 3 => 'agd'] : []
        );
        return $repo;
    }

    public function test_nombres_general_y_completo(): void
    {
        $t = new TiposActividades(112021, false, $this->repoTipos());
        $this->assertSame('sv n ca cuadrienio', $t->getNom());
        $this->assertSame('sv n ca cuadrienio', $t->getNomGral());
        $this->assertSame('^112021', $t->getNom_tipoRegexp());

        $general = new TiposActividades(112000, false, $this->repoTipos());
        $this->assertSame('sv n ca general', $general->getNom());
        // El nombre general omite el nom_tipo 000.
        $this->assertSame('sv n ca', $general->getNomGral());

        $todo = new TiposActividades('1', false, $this->repoTipos());
        $this->assertSame('sv', $todo->getNom());
    }

    public function test_nom_tipo_desconocido_sale_como_interrogante(): void
    {
        $this->assertSame('?', (new TiposActividades(112999, false, $this->repoTipos()))->getNom_tipoText());
    }

    public function test_asistentes_individuales_de_una_seccion(): void
    {
        $t = new TiposActividades('1', false, $this->repoTipos());
        $this->assertSame(['sv n' => '11', 'sv agd' => '13'], $t->getArrayAsistentesIndividual());
    }

    public function test_asistentes_multiples_forman_una_clase_de_caracteres(): void
    {
        $t = new TiposActividades('1');
        $t->setAsistentesText('s,sg');
        $this->assertSame('^1[45]', $t->getAsistentesRegexp());
        $t->setAsistentesText('');
        $this->assertSame('^1.', $t->getAsistentesRegexp());
        $t->setAsistentesId(3);
        $this->assertSame(3, $t->getAsistentesId());
        $this->assertSame('agd', $t->getAsistentesText());
    }

    public function test_id_con_clase_de_caracteres_se_separa_bien(): void
    {
        $t = new TiposActividades('1[45]1');
        $this->assertSame('1', (string) $t->getSfsvId());
        $this->assertSame('^1[45]1...', $t->getNom_tipoRegexp());
        $this->assertSame('crt', $t->getActividadText());
        $this->assertSame('all', $t->getAsistentesText(), 'una clase de varios asistentes no tiene texto');
    }

    public function test_seccion_y_actividad_por_texto_e_id(): void
    {
        $t = new TiposActividades();
        $t->setSfsvText(null);
        $this->assertSame('all', $t->getSfsvText());
        $t->setSfsvId(2);
        $this->assertSame('sf', $t->getSfsvText());
        $t->setActividadText('');
        $this->assertSame('.', $t->getActividadId());
        $t->setActividadId(3);
        $this->assertSame('cv', $t->getActividadText());

        $ext = new TiposActividades('', true);
        $ext->setActividadText('ca');
        $this->assertSame('20', $ext->getActividadId());
        $ext->setActividad2DigitosText('');
        $this->assertSame('..', $ext->getActividadId());
        $this->assertSame('all', $ext->getActividad2DigitosText());
    }

    public function test_nom_tipo_por_texto(): void
    {
        $t = new TiposActividades(112, false, $this->repoTipos());
        $t->getNom_tipoPosibles3Digitos();
        $t->setNom_tipoText('cuadrienio');
        $this->assertSame(21, $t->getNom_tipoId());
        $t->setNom_tipoText('045');
        $this->assertSame(45, $t->getNom_tipoId(), 'un texto que no está en la lista se toma como id');
    }

    /**
     * Hallazgo: setPosiblesAll(false) quita 'all' del mapa, pero el mapa invertido ya
     * se calculó en el constructor y es el que se usa: no tiene efecto.
     */
    public function test_set_posibles_all_no_tiene_efecto_en_los_textos(): void
    {
        $t = new TiposActividades('.');
        $t->setPosiblesAll(false);
        $this->assertSame('all', $t->getSfsvText());
    }

    // --- NivelStgr / Repeticion / TipoDeActividad ---

    public function test_nivel_stgr_entidad_y_metadatos(): void
    {
        $n = new NivelStgr();
        $n->setNivelStgrVo(2);
        $n->setDescNivelVo('Cuadrienio');
        $n->setDescBreveVo('C');
        $n->setOrdenVo(3);
        $this->assertSame(2, $n->getNivel_stgr());
        $this->assertSame('Cuadrienio', $n->getDesc_nivel());
        $this->assertSame('C', $n->getDesc_breve());
        $this->assertSame(3, $n->getOrden());
        $n->setDescBreveVo(null);
        $n->setOrdenVo(null);
        $this->assertNull($n->getDesc_breve());
        $this->assertNull($n->getOrden());
        $this->assertSame('nivel_stgr', $n->getPrimary_key());
        $campos = $n->getDatosCampos();
        $this->assertSame(['desc_nivel', 'desc_breve', 'orden'], array_map(fn ($c) => $c->getNom_camp(), $campos));
        $this->assertSame(['getDesc_nivel', 'getDesc_breve', 'getOrden'], array_map(fn ($c) => $c->getMetodoGet(), $campos));
    }

    public function test_repeticion_entidad_y_metadatos(): void
    {
        $r = new Repeticion();
        $r->setId_repeticion(5);
        $r->setRepeticionVo('cada año');
        $r->setTemporadaVo('V');
        $r->setTipoRepeticionVo(2);
        $this->assertSame('cada año', $r->getRepeticion());
        $this->assertSame('V', $r->getTemporada());
        $this->assertSame(2, $r->getTipo());
        $r->setTipoRepeticionVo(new RepeticionTipo(3));
        $this->assertSame(3, $r->getTipoRepeticion());
        $r->setTipoRepeticionVo(null);
        $this->assertNull($r->getTipoRepeticionVo());
        $this->assertSame('id_repeticion', $r->getPrimary_key());
        $this->assertSame(['repeticion', 'temporada', 'tipo'], array_map(fn ($c) => $c->getNom_camp(), $r->getDatosCampos()));
    }

    public function test_tipo_de_actividad_procesos_por_seccion(): void
    {
        $t = new TipoDeActividad();
        $t->setId_tipo_proceso(10, 1);
        $t->setId_tipo_proceso(20, 2);
        $t->setId_tipo_proceso_ex(30, 1);
        $t->setId_tipo_proceso_ex(40, 2);
        $this->assertSame(10, $t->getId_tipo_proceso(1));
        $this->assertSame(20, $t->getId_tipo_proceso(2));
        $this->assertSame(30, $t->getId_tipo_proceso_ex(1));
        $this->assertSame(40, $t->getId_tipo_proceso_ex(2));
        $t->setNombreVo('nombre');
        $this->assertSame('nombre', $t->getNombre());
    }
}
