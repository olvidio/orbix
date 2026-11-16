<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use src\asistentes\application\ListaAsisConjuntoActivData;
use src\actividades\domain\criteria\ActividadAllField;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Operator;

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
            public ?Criteria $criteria = null;

            public function setMi_dele(string $miDele): void
            {
            }

            public function setCriteria(Criteria $criteria): void
            {
                $this->criteria = $criteria;
            }

            public function setId_tipo_activ(string $idTipoActiv): void
            {
            }

            public function setSacd(bool $sacd): void
            {
            }

            /** @return array<string, mixed> */
            public function getLista(): array
            {
                $dl = '';
                $op = Operator::Eq;
                foreach ($this->criteria?->filters() ?? [] as $filter) {
                    if ($filter instanceof Filter && $filter->field === ActividadAllField::DlOrg) {
                        $dl = (string) $filter->value;
                        $op = $filter->operator;
                    }
                }

                return ['marca' => $op === Operator::Neq ? '[OTRAS:' . $dl . ']' : '[PROPIA:' . $dl . ']'];
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

        $data = new ListaAsisConjuntoActivData($container, new \Tests\support\CalendarioEscolarFijo());
        $bloques = $data->build([
            'id_tipo_activ' => '......',
            'periodo' => 'actual',
            'year' => 2026,
        ])['bloques'];

        $this->assertSame(
            [
                [ucfirst(_("actividades de la dl/r")), '[PROPIA:dlb]'],
                [ucfirst(_("actividades de otras dl/r")), '[OTRAS:dlb]'],
            ],
            array_map(static fn (array $b): array => [$b['titulo'], $b['lista']['marca']], $bloques)
        );
    }
}
