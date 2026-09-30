<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use src\asistentes\application\ListaAsisConjuntoActivData;
use src\configuracion\domain\value_objects\ConfigSnapshot;

/**
 * El contenedor devuelve una sola instancia de ListaPlazasConjuntoActividades.
 * Los dos bloques (dl/r propia y otras) deben conservar filtros distintos.
 */
final class ListaAsisConjuntoActivDataTest extends TestCase
{
    /** @var array<string, mixed>|null */
    private ?array $sessionPrevia = null;

    protected function setUp(): void
    {
        $this->sessionPrevia = $_SESSION ?? null;
        $_SESSION['session_auth'] = [
            'esquema' => 'H-dlbv',
            'sfsv' => 1,
        ];
        $_SESSION['oConfig'] = new ConfigSnapshot(
            gesCalendario: null,
            ceLugar: null,
            regionLatin: null,
            vstgr: null,
            lugarFirma: null,
            dirStgr: null,
            ambito: null,
            notaCorte: null,
            notaMax: null,
            caducaCursada: null,
            idiomaDefault: null,
            iniContadorCertificados: null,
            jefeCalendario: null,
            aCursoStgr: null,
            aCursoCrt: null,
        );
    }

    protected function tearDown(): void
    {
        if ($this->sessionPrevia === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->sessionPrevia;
        }
    }

    public function test_listas_de_la_dl_y_de_otras_no_comparten_filtro(): void
    {
        $compartida = new class {
            /** @var array<string, mixed> */
            public array $where = [];
            /** @var array<string, string> */
            public array $operador = [];

            public function setMi_dele(string $miDele): void
            {
            }

            /**
             * @param array<string, mixed> $where
             */
            public function setWhere(array $where): void
            {
                $this->where = $where;
            }

            /**
             * @param array<string, string> $operador
             */
            public function setOperador(array $operador): void
            {
                $this->operador = $operador;
            }

            public function setId_tipo_activ(string $idTipoActiv): void
            {
            }

            public function setSacd(bool $sacd): void
            {
            }

            public function getLista(): object
            {
                $where = $this->where;
                $operador = $this->operador;

                return new class($where, $operador) {
                    /**
                     * @param array<string, mixed> $where
                     * @param array<string, string> $operador
                     */
                    public function __construct(
                        private array $where,
                        private array $operador,
                    ) {
                    }

                    public function listaPaginada(): string
                    {
                        $dl = (string) ($this->where['dl_org'] ?? '');
                        $op = (string) ($this->operador['dl_org'] ?? '=');

                        return $op === '!=' ? '[OTRAS:' . $dl . ']' : '[PROPIA:' . $dl . ']';
                    }
                };
            }
        };

        $container = new class($compartida) implements ContainerInterface {
            public function __construct(private object $compartida)
            {
            }

            public function get(string $id): object
            {
                return $this->compartida;
            }

            public function has(string $id): bool
            {
                return true;
            }
        };

        $data = new ListaAsisConjuntoActivData($container);
        $html = $data->build([
            'id_tipo_activ' => '......',
            'periodo' => 'actual',
            'year' => 2026,
        ])['content_html'];

        $this->assertStringContainsString('dl/r', $html);
        $this->assertStringContainsString('[PROPIA:dlb]', $html);
        $this->assertStringContainsString('[OTRAS:dlb]', $html);
    }
}
