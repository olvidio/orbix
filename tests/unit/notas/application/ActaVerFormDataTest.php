<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\application\ActaVerFormData;
use src\notas\application\support\ActaContenidoImpreso;
use src\notas\application\DatosActa;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\ActaTribunal;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

final class ActaVerFormDataTest extends myTest
{
    private ActaRepositoryInterface&MockObject $actas;
    private ActividadAsignaturaDlRepositoryInterface&MockObject $aaDl;
    private ActividadAsignaturaRepositoryInterface&MockObject $aa;
    private PersonaDlRepositoryInterface&MockObject $personas;
    private ActaTribunalRepositoryInterface&MockObject $tribunalComun;
    private ActaTribunalDlRepositoryInterface&MockObject $tribunalDl;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private ActaVerFormData $useCase;
    private mixed $configAnterior;
    private string $miDl = '';
    private string $any = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->conAmbito('dl');
        $this->actas = $this->createMock(ActaRepositoryInterface::class);
        $this->actas->method('getUltimaActa')->willReturn(7);
        $this->aaDl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $this->aa = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $this->personas = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->tribunalComun = $this->createMock(ActaTribunalRepositoryInterface::class);
        $this->tribunalComun->method('matching')->willReturn([]);
        $this->tribunalDl = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->tribunalDl->method('matching')->willReturn([]);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('matching')->willReturn([]);
        $this->miDl = ConfigGlobal::mi_delef();
        $this->any = date('y');
        $this->useCase = $this->construir();
    }

    public function tearDown(): void
    {
        if ($this->configAnterior === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    private function conAmbito(string $ambito): void
    {
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, $ambito, null, null, null, null, null, null, ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8], null);
    }

    private function construir(): ActaVerFormData
    {
        $contenido = new ActaContenidoImpreso($this->tribunalComun, new DatosActa($this->createMock(PersonaNotaRepositoryInterface::class)));

        return new ActaVerFormData($this->actas, $this->aaDl, $this->aa, $this->personas, $this->tribunalComun, $this->tribunalDl, $this->asignaturas, $contenido);
    }

    private function acta(string $texto = ''): Acta
    {
        $a = new Acta();
        $a->setActa($texto !== '' ? $texto : "{$this->miDl} 12/24");
        $a->setId_asignatura(1101);
        $a->setId_activ(3001);
        $a->setF_acta(new DateTimeLocal('2024-06-15'));
        $a->setLibro(2);
        $a->setPagina(15);
        $a->setLinea(3);
        $a->setLugar('Aula 1');
        $a->setObserv('nota');

        return $a;
    }

    private function tribunal(string $examinador): ActaTribunal
    {
        $t = new ActaTribunal();
        $t->setExaminador($examinador);

        return $t;
    }

    private function asignatura(string $nombre): Asignatura
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getNombre_asignatura')->willReturn($nombre);

        return $a;
    }

    // ---- ver un acta existente ---------------------------------------------------------------------------

    public function test_ver_un_acta_por_la_seleccion_carga_sus_datos(): void
    {
        $this->actas->method('findById')->with("{$this->miDl} 12/24")->willReturn($this->acta());
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('matching')->willReturn([$this->asignatura('Lógica')]);
        $this->useCase = $this->construir();

        $out = $this->useCase->execute(['sel' => [urlencode("{$this->miDl} 12/24") . '#resto']]);

        $this->assertSame("{$this->miDl} 12/24", $out['acta_actual']);
        $this->assertSame((new DateTimeLocal('2024-06-15'))->getFromLocal(), $out['f_acta']);
        $this->assertSame(['2', '15', '3'], [$out['libro'], $out['pagina'], $out['linea']]);
        $this->assertSame('Aula 1', $out['lugar']);
        $this->assertSame('nota', $out['observ']);
        $this->assertSame(3001, $out['id_activ']);
        $this->assertSame(1101, $out['id_asignatura_actual']);
        $this->assertSame('Lógica', $out['nombre_asignatura']);
        $this->assertSame(["{$this->miDl} 12/24"], $out['a_actas']);
    }

    public function test_sin_seleccion_toma_el_acta_del_parametro_acta(): void
    {
        $this->actas->expects($this->atLeastOnce())->method('findById')->with("{$this->miDl} 5/24")->willReturn($this->acta("{$this->miDl} 5/24"));

        $out = $this->useCase->execute(['acta' => urlencode("{$this->miDl} 5/24")]);

        $this->assertSame("{$this->miDl} 5/24", $out['acta_actual']);
    }

    public function test_un_acta_inexistente_deja_los_campos_vacios(): void
    {
        $this->actas->method('findById')->willReturn(null);

        $out = $this->useCase->execute(['acta' => "{$this->miDl} 99/24"]);

        $this->assertSame(['', '', '', '', ''], [$out['f_acta'], $out['libro'], $out['pagina'], $out['linea'], $out['lugar']]);
        $this->assertNull($out['id_asignatura_actual']);
        $this->assertSame('', $out['nombre_asignatura']);
        $this->assertFalse($out['has_pdf']);
        $this->assertFalse($out['pendiente_imprimir']);
    }

    public function test_el_permiso_por_defecto_es_3_y_en_region_stgr_es_0(): void
    {
        $this->actas->method('findById')->willReturn(null);

        $this->assertSame(3, $this->useCase->execute(['acta' => 'x'])['permiso']);

        $this->conAmbito('rstgr');
        $this->assertSame(0, $this->useCase->execute(['acta' => 'x'])['permiso']);
    }

    public function test_el_permiso_recibido_se_respeta_salvo_en_region_stgr(): void
    {
        $this->actas->method('findById')->willReturn(null);

        $this->assertSame(1, $this->useCase->execute(['acta' => 'x', 'scope_permiso' => '1'])['permiso']);

        $this->conAmbito('rstgr');
        $this->assertSame(0, $this->useCase->execute(['acta' => 'x', 'scope_permiso' => '1'])['permiso']);
    }

    public function test_un_acta_con_pdf_firmado_no_esta_pendiente_de_imprimir(): void
    {
        $firmada = $this->createMock(Acta::class);
        $firmada->method('tienePdfFirmado')->willReturn(true);
        $firmada->method('getF_acta')->willReturn(null);
        $this->actas->method('findById')->willReturn($firmada);

        $out = $this->useCase->execute(['acta' => "{$this->miDl} 12/24"]);

        $this->assertTrue($out['has_pdf']);
        $this->assertFalse($out['pendiente_imprimir']);
    }

    public function test_un_acta_sin_pdf_y_sin_hash_de_impresion_esta_pendiente_de_imprimir(): void
    {
        $this->actas->method('findById')->willReturn($this->acta());

        $out = $this->useCase->execute(['acta' => "{$this->miDl} 12/24"]);

        $this->assertFalse($out['has_pdf']);
        $this->assertTrue($out['pendiente_imprimir']);
    }

    public function test_el_token_para_guardar_va_firmado_con_el_acta(): void
    {
        $this->actas->method('findById')->willReturn($this->acta());

        $out = $this->useCase->execute(['acta' => "{$this->miDl} 12/24"]);

        $this->assertSame(['acta' => "{$this->miDl} 12/24"], HashB::open($out['ctx_guardar'], 'acta_modificar'));
        $this->assertSame('', $out['ctx_nuevo']);
    }

    // ---- tribunal --------------------------------------------------------------------------------------------

    public function test_los_examinadores_salen_del_tribunal_de_la_dl_ordenados(): void
    {
        $this->actas->method('findById')->willReturn($this->acta());
        $this->tribunalDl = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->tribunalDl->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(["acta eq {$this->miDl} 12/24"], DescribeCriteria::filtros($c));
            $this->assertSame(['orden asc'], DescribeCriteria::orden($c));

            return true;
        }))->willReturn([$this->tribunal('Perez'), $this->tribunal('Gil')]);
        $this->useCase = $this->construir();

        $out = $this->useCase->execute(['acta' => "{$this->miDl} 12/24"]);

        $this->assertSame(['Perez', 'Gil'], $out['examinadores']);
    }

    public function test_en_region_stgr_el_tribunal_sale_del_repositorio_comun(): void
    {
        $this->conAmbito('rstgr');
        $this->actas->method('findById')->willReturn($this->acta());
        $this->tribunalComun = $this->createMock(ActaTribunalRepositoryInterface::class);
        $this->tribunalComun->method('matching')->willReturn([$this->tribunal('Comun')]);
        $this->tribunalDl = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->tribunalDl->expects($this->never())->method('matching');
        $this->useCase = $this->construir();

        $this->assertSame(['Comun'], $this->useCase->execute(['acta' => "{$this->miDl} 12/24"])['examinadores']);
    }

    public function test_sin_tribunal_hay_un_examinador_vacio(): void
    {
        $this->actas->method('findById')->willReturn(null);

        $this->assertSame([''], $this->useCase->execute(['acta' => 'x'])['examinadores']);
    }

    // ---- acta nueva ---------------------------------------------------------------------------------------------

    public function test_acta_nueva_propone_el_siguiente_numero_de_la_dl(): void
    {
        $out = $this->useCase->execute(['mod' => 'nueva']);

        $this->assertSame("8/{$this->any}", $out['acta_new_num']);
        $this->assertSame("{$this->miDl} 8/{$this->any}", $out['acta_new']);
        $this->assertSame($this->miDl, $out['acta_sigla']);
        $this->assertStringContainsString("{$this->miDl} 7/{$this->any}", $out['ult_acta']);
        $this->assertSame('', $out['acta_actual']);
    }

    public function test_acta_nueva_pide_la_ultima_acta_del_anio_y_la_dl(): void
    {
        $actas = $this->createMock(ActaRepositoryInterface::class);
        $actas->expects($this->once())->method('getUltimaActa')->with($this->any, $this->miDl)->willReturn(0);
        $this->actas = $actas;
        $this->useCase = $this->construir();

        $out = $this->useCase->execute(['mod' => 'nueva']);

        $this->assertSame("1/{$this->any}", $out['acta_new_num']);
    }

    public function test_acta_nueva_firma_el_token_de_alta_y_avisa_si_falta_la_actividad(): void
    {
        $out = $this->useCase->execute(['mod' => 'nueva']);

        $this->assertNotSame('', $out['ctx_nuevo']);
        $this->assertSame('', $out['ctx_guardar']);
        $this->assertTrue($out['warn_no_id_activ']);

        $this->assertFalse($this->useCase->execute(['mod' => 'nueva', 'notas' => 'nuevo', 'scope_notas' => 'nuevo', 'id_activ' => '3001'])['warn_no_id_activ']);
    }

    public function test_notas_nuevo_toma_la_actividad_y_la_asignatura_del_ambito_antes_que_las_enviadas(): void
    {
        $this->aaDl->method('findById')->willReturn(null);

        $out = $this->useCase->execute([
            'scope_notas' => 'nuevo', 'id_activ' => '1', 'id_activ_scope' => '3001', 'id_asignatura' => '1101', 'id_asignatura_scope' => '1102',
        ]);

        $this->assertSame(3001, $out['id_activ']);
        $this->assertSame(1102, $out['id_asignatura_actual']);
    }

    public function test_notas_nuevo_sin_ambito_usa_las_enviadas_y_sin_asignatura_queda_nula(): void
    {
        $this->aaDl->method('findById')->willReturn(null);

        $con = $this->useCase->execute(['scope_notas' => 'nuevo', 'id_activ' => '3001', 'id_asignatura' => '1101']);
        $sin = $this->useCase->execute(['scope_notas' => 'nuevo', 'id_activ' => '3001']);

        $this->assertSame([3001, 1101], [$con['id_activ'], $con['id_asignatura_actual']]);
        $this->assertNull($sin['id_asignatura_actual']);
    }

    private function actividadAsignatura(?int $idProfesor): ActividadAsignatura
    {
        $a = $this->createMock(ActividadAsignatura::class);
        $a->method('getId_profesor')->willReturn($idProfesor);

        return $a;
    }

    public function test_notas_nuevo_propone_como_examinador_al_profesor_de_la_actividad(): void
    {
        $this->aaDl->expects($this->once())->method('findById')->with(3001, 1101)->willReturn($this->actividadAsignatura(10050));
        $profesor = $this->createMock(PersonaDl::class);
        $profesor->method('getTituloNombre')->willReturn('Dr. Ana Perez');
        $this->personas->method('findById')->with(10050)->willReturn($profesor);

        $out = $this->useCase->execute(['scope_notas' => 'nuevo', 'id_activ' => '3001', 'id_asignatura' => '1101']);

        $this->assertSame(['Dr. Ana Perez'], $out['examinadores']);
    }

    public function test_si_el_profesor_no_existe_se_avisa_en_el_examinador(): void
    {
        $this->aaDl->method('findById')->willReturn($this->actividadAsignatura(10050));
        $this->personas->method('findById')->willReturn(null);

        $out = $this->useCase->execute(['scope_notas' => 'nuevo', 'id_activ' => '3001', 'id_asignatura' => '1101']);

        $this->assertCount(1, $out['examinadores']);
        $this->assertNotSame('', $out['examinadores'][0]);
    }

    public function test_si_la_actividad_no_tiene_profesor_no_se_busca_a_nadie(): void
    {
        $this->aaDl->method('findById')->willReturn($this->actividadAsignatura(null));
        $this->personas->expects($this->never())->method('findById');

        $this->assertSame([''], $this->useCase->execute(['scope_notas' => 'nuevo', 'id_activ' => '3001', 'id_asignatura' => '1101'])['examinadores']);
    }

    public function test_con_esquema_en_el_ambito_la_actividad_se_busca_en_el_repositorio_comun_filtrando_por_esquema(): void
    {
        $this->aaDl->expects($this->never())->method('findById');
        $this->aa->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_activ eq 3001', 'id_asignatura eq 1101', 'id_schema eq 55'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $out = $this->useCase->execute(['scope_notas' => 'nuevo', 'id_activ' => '3001', 'id_asignatura' => '1101', 'id_schema_scope' => '55']);

        $this->assertSame(55, $out['id_schema']);
    }

    // ---- varias actas y datos enviados --------------------------------------------------------------------------------

    public function test_con_notas_y_acta_los_datos_vienen_del_formulario_y_el_acta_solo_se_lee_para_las_marcas(): void
    {
        // Una sola lectura: la de las marcas de PDF y de impresión; los campos salen del formulario aunque el acta exista.
        $this->actas->expects($this->once())->method('findById')->willReturn($this->acta("{$this->miDl} 12/24"));

        $out = $this->useCase->execute([
            'notas' => 'x', 'acta' => "{$this->miDl} 12/24", 'sa_actas' => rtrim(strtr(base64_encode(json_encode(["{$this->miDl} 12/24", "{$this->miDl} 13/24"])), '+/', '-_'), '='),
            'id_asignatura_actual' => '1101', 'id_activ' => '3001', 'f_acta' => '15/06/2024', 'libro' => '7', 'pagina' => '8', 'linea' => '9',
            'lugar' => 'Otro lugar', 'observ' => 'otra nota', 'permiso' => '1',
        ]);

        $this->assertSame("{$this->miDl} 12/24", $out['acta_actual']);
        $this->assertSame(["{$this->miDl} 12/24", "{$this->miDl} 13/24"], $out['a_actas']);
        $this->assertSame(1101, $out['id_asignatura_actual']);
        $this->assertSame('15/06/2024', $out['f_acta']);
        $this->assertSame(['7', '8', '9', 'Otro lugar', 'otra nota'], [$out['libro'], $out['pagina'], $out['linea'], $out['lugar'], $out['observ']], 'mandan los del formulario, no los del acta guardada');
        $this->assertSame(1, $out['permiso']);
    }

    public function test_varias_actas_en_json_toman_la_primera_como_actual_y_descartan_lo_que_no_es_texto(): void
    {
        $this->actas->method('findById')->willReturn(null);

        $out = $this->useCase->execute(['scope_notas' => 'x', 'acta_notas_a_actas_json' => json_encode(["{$this->miDl} 1/24", 5, null, "{$this->miDl} 2/24"])]);

        $this->assertSame(["{$this->miDl} 1/24", "{$this->miDl} 2/24"], $out['a_actas']);
        $this->assertSame("{$this->miDl} 1/24", $out['acta_actual']);
    }

    public function test_un_json_de_actas_invalido_deja_la_lista_vacia(): void
    {
        $out = $this->useCase->execute(['scope_notas' => 'x', 'acta_notas_a_actas_json' => 'no es json']);

        $this->assertSame([], $out['a_actas']);
        $this->assertSame('', $out['acta_actual']);
    }

    public function test_con_notas_sin_actas_la_lista_queda_vacia(): void
    {
        $out = $this->useCase->execute(['scope_notas' => 'x']);

        $this->assertSame([], $out['a_actas']);
        $this->assertSame('', $out['acta_actual']);
    }

    public function test_con_notas_y_seleccion_busca_el_acta_por_actividad_asignatura_y_esquema(): void
    {
        $this->actas->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_activ eq 3001', 'id_asignatura eq 1101', 'id_schema eq 9'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([$this->acta()]);

        $out = $this->useCase->execute(['scope_notas' => 'x', 'sel' => ['3001#1101#2#9']]);

        $this->assertSame('Aula 1', $out['lugar']);
        $this->assertSame(1101, $out['id_asignatura_actual']);
    }

    public function test_con_notas_y_seleccion_sin_esquema_no_filtra_por_esquema(): void
    {
        $this->actas->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_activ eq 3001', 'id_asignatura eq 1101'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->useCase->execute(['scope_notas' => 'x', 'sel' => ['3001#1101#2']]);
    }

    public function test_el_contrato_de_claves_de_la_respuesta(): void
    {
        $this->actas->method('findById')->willReturn(null);

        $out = $this->useCase->execute(['acta' => 'x']);

        $this->assertSame([
            'notas', 'permiso', 'mod', 'acta_actual', 'acta_new', 'acta_sigla', 'acta_new_num', 'ult_acta', 'f_acta', 'libro', 'ult_lib',
            'pagina', 'ult_pag', 'linea', 'ult_lin', 'lugar', 'observ', 'id_activ', 'id_schema', 'id_asignatura_actual', 'nombre_asignatura',
            'examinadores', 'a_actas', 'has_pdf', 'pendiente_imprimir', 'warn_no_id_activ', 'ctx_nuevo', 'ctx_guardar',
        ], array_keys($out));
    }
}
