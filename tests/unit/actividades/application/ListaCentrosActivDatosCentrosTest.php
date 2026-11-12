<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\application\ListaCentrosActivDatos;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Actividades de los centros elegidos y los otros centros encargados de cada una. */
final class ListaCentrosActivDatosCentrosTest extends myTest
{
    private CentroDlRepositoryInterface&MockObject $centros;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    /** @var list<list<string>> */
    private array $consultas = [];
    /** @var list<string> */
    private array $condiciones = [];
    private mixed $sesion = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesion = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = array_merge(is_array($this->sesion) ? $this->sesion : [], ['idioma' => 'es_ES.UTF-8']);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centros->method('matching')->willReturnCallback(function ($c): array {
            $this->consultas[] = DescribeCriteria::filtros($c);
            return [$this->centro(10501, 'Centro A'), $this->centro(10502, 'Centro B')];
        });
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        if ($this->sesion === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesion;
        }
        parent::tearDown();
    }

    private function centro(int $id, string $nombre): CentroDl
    {
        $c = new CentroDl();
        $c->setId_ubi($id);
        $c->setNombre_ubi($nombre);
        return $c;
    }

    private function actividad(int $id, string $nom): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setNom_activ($nom);
        return $a;
    }

    private function uc(): ListaCentrosActivDatos
    {
        return new ListaCentrosActivDatos($this->centros, $this->encargados);
    }

    public function test_por_defecto_todos_los_centros_sin_sss_y_sus_actividades(): void
    {
        $this->encargados->method('getActividadesDeCentros')->willReturnCallback(function (int $id, string $cond): array {
            $this->condiciones[] = $cond;
            return $id === 10501 ? [$this->actividad(1, 'crt <uno>')] : [];
        });
        // Encargados de la actividad: el propio centro no se repite; el primero es el responsable.
        $this->encargados->method('getCentrosEncargadosActividad')->with(1)->willReturn([
            $this->centro(10600, 'Otro "ctr"'), $this->centro(10501, 'Centro A'), $this->centro(10601, 'Tercero'),
        ]);

        $html = $this->uc()->ejecutar(['periodo' => 'otro', 'empiezamin' => '1/3/2027', 'empiezamax' => '31/3/2027'])['html'];

        $this->assertSame([['tipo_ctr matches ^s[^s]']], $this->consultas);
        $this->assertSame(["f_ini BETWEEN '2027-03-01' AND '2027-03-31'", "f_ini BETWEEN '2027-03-01' AND '2027-03-31'"], $this->condiciones);
        $this->assertStringContainsString('<h3>Centro A</h3>', $html);
        $this->assertStringContainsString('<h3>Centro B</h3>', $html);
        $this->assertStringContainsString('<td>crt &lt;uno&gt;</td>', $html);
        $this->assertStringContainsString("<span class='responsable'> Otro &quot;ctr&quot;;</span>", $html);
        $this->assertStringContainsString('<span > Tercero;</span>', $html);
        $this->assertStringNotContainsString('<span > Centro', $html);
    }

    public function test_centros_elegidos_y_desde_hoy_filtra_por_fin(): void
    {
        $this->encargados->method('getActividadesDeCentros')->willReturnCallback(function (int $id, string $cond): array {
            $this->condiciones[] = $cond;
            return [];
        });
        $this->uc()->ejecutar(['id_ctr_num' => '2', 'id_ctr' => ['10501', '', '10502'], 'periodo' => 'desdeHoy']);
        $this->assertSame([['id_ubi in [10501,10502]']], $this->consultas);
        $this->assertStringStartsWith('f_fin BETWEEN', $this->condiciones[0]);
    }

    public function test_con_numero_de_centros_pero_sin_elegir_ninguno_salen_todos(): void
    {
        $this->encargados->method('getActividadesDeCentros')->willReturn([]);
        $this->uc()->ejecutar(['id_ctr_num' => '2', 'id_ctr' => ['', '']]);
        $this->assertSame([['tipo_ctr matches ^s[^s]']], $this->consultas);
    }
}
