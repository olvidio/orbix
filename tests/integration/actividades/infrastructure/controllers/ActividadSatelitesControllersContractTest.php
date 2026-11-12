<?php

declare(strict_types=1);

namespace Tests\integration\actividades\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los controladores HTTP de actividadcargos, actividadescentro,
 * actividadessacd, actividadplazas y actividadtarifas: token de las mutaciones,
 * qué datos salen del token y cuáles del POST, y envelope de `ContestarJson`.
 *
 * Fuera: `cargos_activ_resincronizar.php` (incluye un script CLI que arranca la
 * aplicación y recorre todos los esquemas).
 */
final class ActividadSatelitesControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const SRC = __DIR__ . '/../../../../../src/';

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    private function doble(string $id, mixed $respuesta): object
    {
        $doble = new class($respuesta) {
            /** @var list<array{string, array<int, mixed>}> */
            public array $llamadas = [];

            public function __construct(private mixed $respuesta)
            {
            }

            /** @param array<int, mixed> $args */
            public function __call(string $metodo, array $args): mixed
            {
                $this->llamadas[] = [$metodo, $args];
                return $this->respuesta;
            }
        };
        $this->sustituirServicio($id, $doble);
        return $doble;
    }

    /** @return array<string, mixed> */
    private function ejecutar(string $modulo, string $ctrl, array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::SRC . "$modulo/infrastructure/ui/http/controllers/$ctrl";
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$ctrl debe emitir solo JSON; emitió: " . substr($salida, 0, 200));
        return $json;
    }

    private static function data(array $json): mixed
    {
        return is_string($json['data'] ?? null) ? (json_decode($json['data'], true) ?? $json['data']) : ($json['data'] ?? null);
    }

    private static function uc(string $modulo, string $clase): string
    {
        return "src\\$modulo\\application\\$clase";
    }

    /**
     * Mutaciones con token: [módulo, controlador, caso de uso, campo, acción,
     * payload del token, POST extra (incluye intentos de suplantar el token),
     * entrada esperada en el caso de uso].
     *
     * @return iterable<string, array{string, string, string, string, string, array, array, array}>
     */
    public static function mutacionesProvider(): iterable
    {
        yield 'sacd asignar' => ['actividadessacd', 'sacd_asignar.php', 'SacdAsignar', 'ctx_asignar', 'sacd_asignar',
            ['id_activ' => 10, 'id_nom' => 20], ['id_activ' => '99', 'id_nom' => '99'], ['id_activ' => 10, 'id_nom' => 20]];
        yield 'sacd eliminar' => ['actividadessacd', 'sacd_eliminar.php', 'SacdEliminar', 'ctx_eliminar', 'sacd_eliminar',
            ['id_activ' => 10, 'id_cargo' => 35, 'id_nom' => 20], ['id_nom' => '99'], ['id_activ' => 10, 'id_cargo' => 35, 'id_nom' => 20]];
        yield 'sacd reordenar' => ['actividadessacd', 'sacd_reordenar.php', 'SacdReordenar', 'ctx_reordenar', 'sacd_reordenar',
            ['id_activ' => 10, 'id_nom' => 20], ['num_orden' => 'mas', 'id_activ' => '99'], ['id_activ' => 10, 'id_nom' => 20, 'num_orden' => 'mas']];
        yield 'sacd asignar auto' => ['actividadessacd', 'sacd_asignar_auto.php', 'SacdAsignarAuto', 'ctx_asignar_auto', 'sacd_asignar_auto',
            ['f_ini_iso' => '2027-01-01'], ['f_ini_iso' => '2001-01-01'], ['f_ini_iso' => '2027-01-01']];
        yield 'texto guardar' => ['actividadessacd', 'texto_comunicacion_guardar.php', 'TextoComunicacionGuardar', 'ctx_guardar', 'texto_comunicacion_guardar',
            ['clave' => 'com_sacd', 'idioma' => 'ca'], ['clave' => 'otra', 'texto' => 'Hola'], ['clave' => 'com_sacd', 'idioma' => 'ca', 'texto' => 'Hola']];
        yield 'plazas ceder' => ['actividadplazas', 'plazas_ceder.php', 'PlazasCeder', 'ctx_ceder', 'plazas_ceder',
            ['id_activ' => 10], ['id_activ' => '99', 'num_plazas' => '3', 'region_dl' => 'H-dlp'], ['id_activ' => 10, 'num_plazas' => 3, 'region_dl' => 'H-dlp']];
        yield 'peticiones guardar' => ['actividadplazas', 'peticiones_guardar.php', 'PeticionesGuardar', 'ctx_guardar', 'peticiones_guardar',
            ['id_nom' => 20, 'sactividad' => 'ca'], ['id_nom' => '99', 'actividades' => ['10', '11']], ['id_nom' => 20, 'sactividad' => 'ca', 'actividades' => ['10', '11']]];
        yield 'peticiones eliminar' => ['actividadplazas', 'peticiones_eliminar.php', 'PeticionesEliminar', 'ctx_eliminar', 'peticiones_eliminar',
            ['id_nom' => 20, 'sactividad' => 'ca'], ['id_nom' => '99'], ['id_nom' => 20, 'sactividad' => 'ca']];
        yield 'relacion eliminar' => ['actividadtarifas', 'relacion_tarifa_eliminar.php', 'RelacionTarifaEliminar', 'ctx_eliminar', 'relacion_tarifa_eliminar',
            ['id_item' => 5], ['id_item' => '99'], ['id_item' => 5]];
        yield 'relacion update' => ['actividadtarifas', 'relacion_tarifa_update.php', 'RelacionTarifaUpdate', 'ctx_update', 'relacion_tarifa_update',
            ['id_item' => '5'], ['id_item' => '99', 'id_tarifa' => '4', 'id_tipo_activ' => '112021'], ['id_item' => '5', 'id_tarifa' => 4, 'id_tipo_activ' => 112021]];
        yield 'relacion nueva' => ['actividadtarifas', 'relacion_tarifa_update.php', 'RelacionTarifaUpdate', 'ctx_update', 'relacion_tarifa_update',
            [], ['id_tarifa' => '4'], ['id_item' => 'nuevo', 'id_tarifa' => 4, 'id_tipo_activ' => 0]];
        yield 'tarifa ubi copiar' => ['actividadtarifas', 'tarifa_ubi_copiar.php', 'TarifaUbiCopiar', 'ctx_copiar', 'tarifa_ubi_copiar',
            ['id_ubi' => 200, 'year' => 2027], ['id_ubi' => '99'], ['id_ubi' => 200, 'year' => 2027]];
        yield 'tarifa ubi eliminar' => ['actividadtarifas', 'tarifa_ubi_eliminar.php', 'TarifaUbiEliminar', 'ctx_eliminar', 'tarifa_ubi_eliminar',
            ['id_item' => 7], ['id_item' => '99'], ['id_item' => 7]];
        yield 'tarifa ubi update' => ['actividadtarifas', 'tarifa_ubi_update.php', 'TarifaUbiUpdate', 'ctx_update', 'tarifa_ubi_update',
            ['id_item' => 7, 'id_ubi' => 200, 'year' => 2027], ['id_ubi' => '99', 'id_tarifa' => '4', 'id_serie' => '1', 'cantidad' => '30,5', 'observ' => 'o'],
            ['id_item' => 7, 'id_ubi' => 200, 'year' => 2027, 'id_tarifa' => 4, 'id_serie' => 1, 'cantidad' => '30,5', 'observ' => 'o']];
        yield 'tipo tarifa eliminar' => ['actividadtarifas', 'tipo_tarifa_eliminar.php', 'TipoTarifaEliminar', 'ctx_eliminar', 'tipo_tarifa_eliminar',
            ['id_tarifa' => 4], ['id_tarifa' => '99'], ['id_tarifa' => 4]];
        yield 'cargo nuevo desde la actividad' => ['actividadcargos', 'cargo_nuevo.php', 'ActividadCargoNuevo', 'ctx_guardar', 'cargo_nuevo',
            ['id_activ' => 10, 'id_nom' => 0], ['id_activ' => '99', 'id_nom' => '20', 'id_cargo' => '3', 'id_item' => '7'], ['id_activ' => 10, 'id_nom' => '20', 'id_cargo' => '3']];
        yield 'cargo nuevo desde la persona' => ['actividadcargos', 'cargo_nuevo.php', 'ActividadCargoNuevo', 'ctx_guardar', 'cargo_nuevo',
            ['id_activ' => 0, 'id_nom' => 20], ['id_activ' => '10', 'id_nom' => '99'], ['id_activ' => '10', 'id_nom' => 20]];
        yield 'cargo editar' => ['actividadcargos', 'cargo_editar.php', 'ActividadCargoEditar', 'ctx_guardar', 'cargo_editar',
            ['id_item' => 7, 'id_activ' => 10, 'id_nom' => 20], ['id_item' => '99', 'id_activ' => '99', 'id_nom' => '99', 'id_cargo' => '3'],
            ['id_item' => 7, 'id_activ' => 10, 'id_nom' => 20, 'id_cargo' => '3', 'asis_presente' => '']];
        yield 'cargo eliminar' => ['actividadcargos', 'cargo_eliminar.php', 'ActividadCargoEliminar', 'ctx_eliminar_cargo', 'cargo_eliminar',
            ['id_item' => 5, 'elim_asis' => 2], ['id_item' => '99', 'sel' => ['99#1']], ['id_item' => 5, 'elim_asis' => 2]];
        yield 'centro asignar' => ['actividadescentro', 'centro_encargado_asignar.php', 'CentroEncargadoAsignar', 'ctx', 'centro_encargado_asignar',
            ['id_activ' => 10, 'id_ubi' => 20], ['id_activ' => '99', 'id_ubi' => '99'], ['id_activ' => 10, 'id_ubi' => 20]];
        yield 'centro eliminar' => ['actividadescentro', 'centro_encargado_eliminar.php', 'CentroEncargadoEliminar', 'ctx', 'centro_encargado_eliminar',
            ['id_activ' => 10, 'id_ubi' => 20], ['id_ubi' => '99'], ['id_activ' => 10, 'id_ubi' => 20]];
        yield 'centro reordenar' => ['actividadescentro', 'centro_encargado_reordenar.php', 'CentroEncargadoReordenar', 'ctx', 'centro_encargado_reordenar',
            ['id_activ' => 10, 'id_ubi' => 20], ['id_ubi' => '99', 'num_orden' => 'mas'], ['id_activ' => 10, 'id_ubi' => 20, 'num_orden' => 'mas']];
        yield 'tipo tarifa nuevo' => ['actividadtarifas', 'tipo_tarifa_update.php', 'TipoTarifaUpdate', 'ctx_update', 'tipo_tarifa_update',
            [], ['letra' => 'A', 'modo' => '1', 'observ' => ''], ['id_tarifa' => 'nuevo', 'letra' => 'A', 'modo' => '1', 'observ' => '']];
    }

    #[DataProvider('mutacionesProvider')]
    public function test_mutacion_sin_token_valido_no_llama_al_caso_de_uso(string $modulo, string $ctrl, string $clase, string $campo, string $accion, array $ctx, array $post, array $esperado): void
    {
        $caso = $this->doble(self::uc($modulo, $clase), '');
        foreach (['sin token' => [], 'falso' => [$campo => 'x'], 'otra acción' => [$campo => HashB::sign('otra_accion', $ctx)]] as $txt => $tok) {
            $json = $this->ejecutar($modulo, $ctrl, $post + $tok);
            $this->assertFalse($json['success'], $txt);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $txt);
        }
        $this->assertSame([], $caso->llamadas);
    }

    #[DataProvider('mutacionesProvider')]
    public function test_mutacion_toma_los_ids_del_token(string $modulo, string $ctrl, string $clase, string $campo, string $accion, array $ctx, array $post, array $esperado): void
    {
        $caso = $this->doble(self::uc($modulo, $clase), '');
        $json = $this->ejecutar($modulo, $ctrl, $post + [$campo => HashB::sign($accion, $ctx)]);
        $this->assertTrue($json['success']);
        $this->assertSame($esperado, $caso->llamadas[0][1][0]);
    }

    #[DataProvider('mutacionesProvider')]
    public function test_mutacion_devuelve_el_error_del_caso_de_uso(string $modulo, string $ctrl, string $clase, string $campo, string $accion, array $ctx, array $post, array $esperado): void
    {
        $this->doble(self::uc($modulo, $clase), 'no se puede');
        $json = $this->ejecutar($modulo, $ctrl, $post + [$campo => HashB::sign($accion, $ctx)]);
        if ($clase === 'SacdAsignarAuto') {
            // Devuelve el resultado como datos, no como error.
            $this->assertTrue($json['success']);
            $this->assertSame('no se puede', self::data($json));
            return;
        }
        $this->assertFalse($json['success']);
        $this->assertSame('no se puede', $json['mensaje']);
    }

    public function test_comunicacion_enviar_toma_que_persona_y_propuesta_del_token(): void
    {
        $caso = $this->doble(self::uc('actividadessacd', 'ComunicacionActividadesSacdEnviar'), '');
        $this->ejecutar('actividadessacd', 'comunicacion_activ_sacd_enviar.php', [
            'ctx_enviar' => HashB::sign('comunicacion_activ_sacd_enviar', ['que' => 'un_sacd', 'id_nom' => 20, 'propuesta' => 'f']),
            'que' => 'nagd', 'id_nom' => '99', 'periodo' => 'trimestre_1',
        ]);
        $in = $caso->llamadas[0][1][0];
        $this->assertSame(['un_sacd', 20, 'f', 'trimestre_1'], [$in['que'], $in['id_nom'], $in['propuesta'], $in['periodo']]);
        $this->assertFalse($this->ejecutar('actividadessacd', 'comunicacion_activ_sacd_enviar.php', [])['success']);
    }

    public function test_gestion_plazas_update_toma_la_actividad_del_token_dentro_de_data(): void
    {
        $caso = $this->doble(self::uc('actividadplazas', 'GestionPlazasUpdate'), '');
        $data = json_encode(['id' => 99, 'dlb-c' => 5, 'ctx_update' => HashB::sign('gestion_plazas_update', ['id_activ' => 10])]);
        $this->assertTrue($this->ejecutar('actividadplazas', 'gestion_plazas_update.php', ['data' => $data, 'colName' => 'dlb-c'])['success']);
        $in = $caso->llamadas[0][1][0];
        $this->assertSame('dlb-c', $in['colName']);
        $this->assertSame(10, json_decode($in['data'], true)['id']);

        $sin = $this->ejecutar('actividadplazas', 'gestion_plazas_update.php', ['data' => '{"id":10}', 'colName' => 'x']);
        $this->assertSame('Operación no autorizada', $sin['mensaje']);
        $this->assertCount(1, $caso->llamadas);
    }

    public function test_incremento_de_tarifas_solo_aplica_a_los_items_del_token(): void
    {
        $caso = $this->doble(self::uc('actividadtarifas', 'TarifaUbiUpdateInc'), '');
        $this->ejecutar('actividadtarifas', 'tarifa_ubi_update_inc.php', [
            'ctx_inc' => HashB::sign('tarifa_ubi_update_inc', ['id_items' => [7, '8']]),
            'inc_cantidad' => ['2027#7' => '10', '2027#8' => '11', '2027#9' => '99', 'malo' => '1'],
        ]);
        $this->assertSame(['inc_cantidad' => ['2027#7' => '10', '2027#8' => '11']], $caso->llamadas[0][1][0]);
    }

    public function test_peticiones_incorporar(): void
    {
        $caso = $this->doble(self::uc('actividadplazas', 'PeticionesIncorporar'), ['error' => '', 'n' => 3]);
        $json = $this->ejecutar('actividadplazas', 'peticiones_incorporar.php', ['ctx_incorporar' => HashB::sign('peticiones_incorporar', []), 'sactividad' => 'ca', 'sasistentes' => 'n']);
        $this->assertSame(['sactividad' => 'ca', 'sasistentes' => 'n'], $caso->llamadas[0][1][0]);
        $this->assertSame(['n' => 3], self::data($json));
        $this->doble(self::uc('actividadplazas', 'PeticionesIncorporar'), ['error' => 'nada que incorporar']);
        $this->assertSame('nada que incorporar', $this->ejecutar('actividadplazas', 'peticiones_incorporar.php', ['ctx_incorporar' => HashB::sign('peticiones_incorporar', [])])['mensaje']);
    }

    public function test_tarifa_ubi_form_con_token_o_sin_el(): void
    {
        $caso = $this->doble(self::uc('actividadtarifas', 'TarifaUbiFormData'), ['form' => 1]);
        $this->ejecutar('actividadtarifas', 'tarifa_ubi_form_data.php', ['ctx_form' => HashB::sign('tarifa_ubi_form', ['id_item' => 7, 'id_ubi' => 200])]);
        $this->assertSame(['id_item' => 7, 'id_ubi' => 200], $caso->llamadas[0][1][0]);
        $this->ejecutar('actividadtarifas', 'tarifa_ubi_form_data.php', ['id_ubi' => '200', 'year' => '2027']);
        $this->assertSame(['id_item' => '', 'id_ubi' => 200, 'year' => 2027, 'letra' => ''], $caso->llamadas[1][1][0]);
        $this->assertSame('Operación no autorizada', $this->ejecutar('actividadtarifas', 'tarifa_ubi_form_data.php', ['ctx_form' => 'x'])['mensaje']);
        $this->assertCount(2, $caso->llamadas);
    }

    public function test_cargo_editar_marca_la_casilla_asiste_como_presente(): void
    {
        $caso = $this->doble(self::uc('actividadcargos', 'ActividadCargoEditar'), '');
        $ctx = ['ctx_guardar' => HashB::sign('cargo_editar', ['id_item' => 7, 'id_activ' => 10, 'id_nom' => 20])];
        $this->ejecutar('actividadcargos', 'cargo_editar.php', $ctx + ['asis' => 'on']);
        $this->ejecutar('actividadcargos', 'cargo_editar.php', $ctx + ['asis_presente' => '1']);
        $this->ejecutar('actividadcargos', 'cargo_editar.php', $ctx);
        $this->assertSame(['1', '1', ''], array_map(fn ($l) => $l[1][0]['asis_presente'], $caso->llamadas));
    }

    public function test_cargo_y_centro_con_capsula_sin_ids_no_estan_autorizados(): void
    {
        $cargo = $this->doble(self::uc('actividadcargos', 'ActividadCargoEliminar'), '');
        $centro = $this->doble(self::uc('actividadescentro', 'CentroEncargadoAsignar'), '');
        $this->assertSame('Operación no autorizada', $this->ejecutar('actividadcargos', 'cargo_eliminar.php', ['ctx_eliminar_cargo' => HashB::sign('cargo_eliminar', [])])['mensaje']);
        $this->assertSame('Operación no autorizada', $this->ejecutar('actividadescentro', 'centro_encargado_asignar.php', ['ctx' => HashB::sign('centro_encargado_asignar', ['id_activ' => 10])])['mensaje']);
        $this->assertSame([], $cargo->llamadas);
        $this->assertSame([], $centro->llamadas);
    }

    /**
     * Lecturas: [módulo, controlador, caso de uso, POST, clave esperada en la entrada, valor].
     *
     * @return iterable<string, array{string, string, string, array, ?string, mixed}>
     */
    public static function lecturasProvider(): iterable
    {
        yield 'form cargos' => ['actividadcargos', 'form_cargos_de_actividad_data.php', 'FormCargosDeActividadData', ['id_activ' => '10'], 'id_activ', '10'];
        yield 'form cargos personas' => ['actividadcargos', 'form_cargos_personas_en_actividad_data.php', 'FormCargosPersonasEnActividadData', ['id_nom' => '20'], 'id_nom', '20'];
        yield 'shell ctr' => ['actividadescentro', 'activ_ctr_shell_data.php', 'ActivCtrShellData', ['tipo' => 'n'], 'tipo', 'n'];
        yield 'centros disponibles' => ['actividadescentro', 'centros_disponibles_data.php', 'CentrosDisponiblesData', ['id_activ' => '10'], 'id_activ', 10];
        yield 'centros encargados' => ['actividadescentro', 'centros_encargados_data.php', 'CentrosEncargadosData', ['dl_org' => 'dlb'], 'dl_org', 'dlb'];
        yield 'lista ctr' => ['actividadescentro', 'lista_actividades_ctr_data.php', 'ListaActividadesCtrData', ['year' => '2027'], 'year', '2027'];
        yield 'com sacd data' => ['actividadessacd', 'comunicacion_activ_sacd_data.php', 'ComunicacionActividadesSacdData', ['que' => 'nagd'], 'que', 'nagd'];
        yield 'lista sacd' => ['actividadessacd', 'lista_actividades_sacd_data.php', 'ListaActividadesSacdData', ['tipo' => 'x'], 'tipo', 'x'];
        yield 'sacds disponibles' => ['actividadessacd', 'sacds_disponibles_data.php', 'SacdsDisponiblesData', ['seleccion' => '2'], 'seleccion', 2];
        yield 'sacds encargados' => ['actividadessacd', 'sacds_encargados_data.php', 'SacdsEncargadosData', ['id_activ' => '10'], 'id_activ', 10];
        yield 'solapes' => ['actividadessacd', 'solapes_sacd_data.php', 'SolapesSacdData', ['periodo' => 'curso'], 'periodo', 'curso'];
        yield 'texto' => ['actividadessacd', 'texto_comunicacion_data.php', 'TextoComunicacionData', ['clave' => 'com_sacd'], 'clave', 'com_sacd'];
        yield 'locales' => ['actividadessacd', 'locales_desplegable_data.php', 'LocalesDesplegableData', [], null, null];
        yield 'auto form' => ['actividadessacd', 'sacd_asignar_auto_form_data.php', 'SacdAsignarAutoFormData', [], null, null];
        yield 'gestion plazas' => ['actividadplazas', 'gestion_plazas_data.php', 'GestionPlazasData', ['sactividad2' => 'ca-est'], 'sactividad2', 'ca-est'];
        yield 'peticiones activ' => ['actividadplazas', 'peticiones_activ_data.php', 'PeticionesActivData', ['que' => 'ca'], 'sactividad', 'ca'];
        yield 'incorporar data' => ['actividadplazas', 'peticiones_incorporar_data.php', 'PeticionesIncorporarData', [], null, null];
        yield 'balance que' => ['actividadplazas', 'plazas_balance_que_data.php', 'PlazasBalanceQueData', ['sactividad' => 'ca'], 'sactividad', 'ca'];
        yield 'relacion form' => ['actividadtarifas', 'relacion_tarifa_form_data.php', 'RelacionTarifaFormData', ['id_item' => '5'], 'id_item', '5'];
        yield 'relacion lista' => ['actividadtarifas', 'relacion_tarifa_lista_data.php', 'RelacionTarifaListaData', [], null, null];
        yield 'tarifa ubi lista' => ['actividadtarifas', 'tarifa_ubi_lista_data.php', 'TarifaUbiListaData', ['year' => '2027'], 'year', 2027];
        yield 'tipo tarifa form' => ['actividadtarifas', 'tipo_tarifa_form_data.php', 'TipoTarifaFormData', ['id_tarifa' => '4'], 'id_tarifa', '4'];
        yield 'tipo tarifa lista' => ['actividadtarifas', 'tipo_tarifa_lista_data.php', 'TipoTarifaListaData', [], null, null];
    }

    #[DataProvider('lecturasProvider')]
    public function test_lectura_pasa_la_entrada_y_devuelve_el_payload(string $modulo, string $ctrl, string $clase, array $post, ?string $clave, mixed $valor): void
    {
        $caso = $this->doble(self::uc($modulo, $clase), ['k' => 'v']);
        $json = $this->ejecutar($modulo, $ctrl, $post);
        $this->assertTrue($json['success']);
        $this->assertSame(['k' => 'v'], self::data($json));
        if ($clave !== null) {
            $this->assertSame($valor, $caso->llamadas[0][1][0][$clave]);
        }
    }

    public function test_lecturas_que_separan_el_error(): void
    {
        foreach ([
            ['actividadcargos', 'form_cargos_de_actividad_data.php', 'FormCargosDeActividadData'],
            ['actividadplazas', 'plazas_balance_data.php', 'PlazasBalanceData'],
            ['actividadplazas', 'resumen_plazas_data.php', 'ResumenPlazasData'],
            ['actividadplazas', 'posibles_propietarios_data.php', 'PosiblesPropietariosData'],
        ] as [$modulo, $ctrl, $clase]) {
            $this->doble(self::uc($modulo, $clase), ['error' => 'falta parametro', 'x' => 1]);
            $json = $this->ejecutar($modulo, $ctrl, []);
            $this->assertFalse($json['success'], $ctrl);
            $this->assertSame('falta parametro', $json['mensaje'], $ctrl);

            $this->doble(self::uc($modulo, $clase), ['x' => 1]);
            $this->assertSame(['x' => 1], self::data($this->ejecutar($modulo, $ctrl, [])), $ctrl);
        }
    }

    public function test_pagina_de_comunicacion_convierte_la_excepcion_en_error(): void
    {
        $doble = new class {
            public function execute(): array
            {
                throw new \RuntimeException('sin periodo');
            }
        };
        $this->sustituirServicio(self::uc('actividadessacd', 'ComSacdActivPeriodoPageData'), $doble);
        $json = $this->ejecutar('actividadessacd', 'com_sacd_activ_periodo_page_data.php', []);
        $this->assertFalse($json['success']);
        $this->assertSame('sin periodo', $json['mensaje']);
    }

    public static function modulosProvider(): iterable
    {
        foreach (['actividades', 'actividadcargos', 'actividadescentro', 'actividadessacd', 'actividadplazas', 'actividadtarifas'] as $m) {
            yield $m => [$m];
        }
    }

    #[DataProvider('modulosProvider')]
    public function test_cada_ruta_apunta_a_un_controlador_y_cada_controlador_tiene_ruta(string $modulo): void
    {
        $rutas = (string) file_get_contents(self::SRC . "$modulo/config/routes.php");
        preg_match_all('#/infrastructure/ui/http/controllers/([a-z0-9_]+\.php)#', $rutas, $m);
        $enRutas = array_values(array_unique($m[1]));
        $this->assertNotSame([], $enRutas);
        foreach ($enRutas as $f) {
            $this->assertFileExists(self::SRC . "$modulo/infrastructure/ui/http/controllers/$f");
        }
        $ficheros = array_map('basename', glob(self::SRC . "$modulo/infrastructure/ui/http/controllers/*.php") ?: []);
        $this->assertSame([], array_values(array_diff($ficheros, $enRutas)), "controladores sin ruta en $modulo");
    }
}
