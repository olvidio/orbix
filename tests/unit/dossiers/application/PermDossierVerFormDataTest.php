<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\application\PermDossierVerFormData;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\PermisoDossierBits;
use src\dossiers\domain\value_objects\TipoDossierCodigo;

/**
 * Cubre la composición del payload JSON del formulario de permisos por tipo de dossier.
 * Un fallo aquí (p. ej. llamar a un getter inexistente en {@see TipoDossier}) antes producía
 * HTML de error en lugar de JSON en el endpoint `perm_dossier_ver_data`.
 */
final class PermDossierVerFormDataTest extends TestCase
{
    private mixed $previousContainer = null;
    private mixed $previousOPerm = null;
    private bool $hadSessionSfsv = false;
    private mixed $previousSessionSfsv = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousOPerm = $_SESSION['oPerm'] ?? null;
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $this->hadSessionSfsv = array_key_exists('sfsv', $_SESSION['session_auth']);
        $this->previousSessionSfsv = $this->hadSessionSfsv ? $_SESSION['session_auth']['sfsv'] : null;
        $_SESSION['session_auth']['sfsv'] = 1;
        $_SESSION['oPerm'] = new class {
            public function have_perm_oficina(string $_perm): bool
            {
                return false;
            }
        };
    }

    protected function tearDown(): void
    {
        if ($this->hadSessionSfsv) {
            $_SESSION['session_auth']['sfsv'] = $this->previousSessionSfsv;
        } else {
            unset($_SESSION['session_auth']['sfsv']);
        }
        if ($this->previousOPerm === null) {
            unset($_SESSION['oPerm']);
        } else {
            $_SESSION['oPerm'] = $this->previousOPerm;
        }
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_build_incluye_codigo_vacio_si_no_hay_slug(): void
    {
        $tipo = $this->tipoDossierMinimo();
        $GLOBALS['container'] = $this->containerConTipoDossier($tipo);

        $data = PermDossierVerFormData::build($tipo->getId_tipo_dossier(), 'td_test');

        $this->assertArrayHasKey('codigo', $data);
        $this->assertSame('', $data['codigo']);
    }

    public function test_build_incluye_codigo_cuando_esta_definido(): void
    {
        $tipo = $this->tipoDossierMinimo();
        $tipo->setCodigoVo(new TipoDossierCodigo('mi_slug'));
        $GLOBALS['container'] = $this->containerConTipoDossier($tipo);

        $data = PermDossierVerFormData::build($tipo->getId_tipo_dossier(), 'td_test');

        $this->assertSame('mi_slug', $data['codigo']);
    }

    public function test_build_cumple_contrato_de_claves_y_estructura(): void
    {
        $tipo = $this->tipoDossierMinimo();
        $tipo->setDescripcion('D1');
        $tipo->setTabla_to('t2');
        $tipo->setCampo_to('c3');
        $tipo->setId_tipo_dossier_rel(5);
        $tipo->setApp('app1');
        $tipo->setClass('cls');
        $GLOBALS['container'] = $this->containerConTipoDossier($tipo);

        $id = $tipo->getId_tipo_dossier();
        $qTipo = 'td_contract';
        $data = PermDossierVerFormData::build($id, $qTipo);

        $expectedKeys = [
            'hash_config',
            'go_to_link_spec',
            'permiso_dossier_bit_map',
            'url_guardar',
            'url_eliminar',
            'txt_eliminar',
            'perm_admin',
            'id_tipo_dossier',
            'descripcion',
            'tabla_from',
            'tabla_to',
            'campo_to',
            'id_tipo_dossier_rel',
            'permiso_lectura',
            'permiso_escritura',
            'app',
            'class',
            'codigo',
            'chk',
            'botones',
        ];
        $this->assertSame($expectedKeys, array_keys($data), 'El contrato de claves del payload no debe cambiar sin aviso.');

        $this->assertSame(
            [
                'campos_form' => 'id_tipo_dossier!id_tipo_dossier_rel!tabla_from!tabla_to!campo_to!descripcion!app!class!codigo',
                'campos_no' => 'que!depende_modificar!permiso_lectura!permiso_escritura',
                'campos_hidden' => [
                    'campos_chk' => 'depende_modificar!permiso_lectura!permiso_escritura',
                ],
            ],
            $data['hash_config']
        );

        $this->assertSame(
            [
                'path' => 'frontend/dossiers/controller/perm_dossiers.php',
                'query' => ['tipo' => $qTipo],
            ],
            $data['go_to_link_spec']
        );

        $this->assertSame(PermisoDossierBits::labeledMap(), $data['permiso_dossier_bit_map']);

        $this->assertSame('/src/dossiers/tipo_dossier_guardar', $data['url_guardar']);
        $this->assertSame('/src/dossiers/tipo_dossier_eliminar', $data['url_eliminar']);
        $this->assertIsString($data['txt_eliminar']);
        $this->assertNotSame('', $data['txt_eliminar']);

        $this->assertFalse($data['perm_admin']);
        $this->assertSame($id, $data['id_tipo_dossier']);
        $this->assertSame('D1', $data['descripcion']);
        $this->assertSame('p', $data['tabla_from']);
        $this->assertSame('t2', $data['tabla_to']);
        $this->assertSame('c3', $data['campo_to']);
        $this->assertSame(5, $data['id_tipo_dossier_rel']);
        $this->assertSame(0, $data['permiso_lectura']);
        $this->assertSame(0, $data['permiso_escritura']);
        $this->assertSame('app1', $data['app']);
        $this->assertSame('cls', $data['class']);
        $this->assertSame('', $data['codigo']);
        $this->assertSame('', $data['chk']);
        $this->assertSame(0, $data['botones']);
    }

    private function tipoDossierMinimo(): TipoDossier
    {
        $o = new TipoDossier();
        $o->setId_tipo_dossier(9900101);
        $o->setTabla_from('p');
        $o->setPermiso_lectura(0);
        $o->setPermiso_escritura(0);
        $o->setDepende_modificar(false);
        return $o;
    }

    private function containerConTipoDossier(TipoDossier $tipo): object
    {
        return new class($tipo) {
            public function __construct(private readonly TipoDossier $tipo) {}

            public function get(string $key): object
            {
                if ($key !== TipoDossierRepositoryInterface::class) {
                    throw new \RuntimeException("Clave inesperada: $key");
                }
                return new class($this->tipo) {
                    public function __construct(private readonly TipoDossier $tipo) {}

                    public function findById(int $id): ?TipoDossier
                    {
                        return $this->tipo->getId_tipo_dossier() === $id ? $this->tipo : null;
                    }
                };
            }
        };
    }
}
