<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\DocumentosAsignar;
use src\inventario\application\DocumentosTrasladar;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\support\DescribeCriteria;

/**
 * Guardar la asignación de un tipo de documento a centros o lugares ({@see DocumentosAsignar}) y el
 * traslado de documentos ({@see DocumentosTrasladar}).
 */
final class DocumentosAsignarYTrasladarTest extends TestCase
{
    /** @var array<string, list<Documento>> «id_ubi» o «id_ubi#id_lugar» => documentos existentes */
    private array $existentes = [];
    /** @var list<Documento> */
    private array $guardados = [];
    private bool $falla = false;
    private mixed $sesion;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION ?? [];
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF-8';
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesion;
    }

    private static function doc(int $id, int $ubi, ?int $lugar = null): Documento
    {
        $d = new Documento();
        $d->setId_doc($id);
        $d->setId_tipo_doc(1);
        $d->setId_ubi($ubi);
        $d->setId_lugar($lugar);
        $d->setF_recibido(new DateTimeLocal('2020-01-01'));
        return $d;
    }

    private function repo(): DocumentoRepositoryInterface
    {
        $repo = $this->createMock(DocumentoRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $ubi = substr($f[0], strlen('id_ubi eq '));
            $lugar = str_starts_with($f[1], 'id_lugar eq ') ? '#' . substr($f[1], strlen('id_lugar eq ')) : '';
            return $this->existentes[$ubi . $lugar] ?? [];
        });
        $repo->method('findById')->willReturnCallback(static fn (int $id): ?Documento => $id === 99 ? null : self::doc($id, 6, 20));
        $repo->method('getNewId')->willReturn(500);
        $repo->method('Guardar')->willReturnCallback(function (Documento $d): bool {
            $this->guardados[] = $d;
            return !$this->falla;
        });
        $repo->method('getErrorTxt')->willReturn('fallo de BD');
        return $repo;
    }

    private function asignar(bool $numerado = true): DocumentosAsignar
    {
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (int $id) use ($numerado): ?TipoDoc {
            if ($id !== 1) {
                return null;
            }
            $t = new TipoDoc();
            $t->setId_tipo_doc(1);
            $t->setSigla('B');
            $t->setNumerado($numerado);
            return $t;
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            if ($id === 66) {
                return null;
            }
            $l = new Lugar();
            $l->setId_lugar($id);
            $l->setId_ubi(40);
            $l->setNom_lugar("Lugar $id");
            return $l;
        });

        return new DocumentosAsignar($tipos, $this->repo(), $lugares);
    }

    public function test_a_centros_crea_el_documento_si_falta_y_actualiza_el_que_hay(): void
    {
        $this->existentes['7'] = [self::doc(70, 7)];

        $error = $this->asignar()->aCentros(1, [6 => '905', 7 => '906', 'x' => '1'], '15/6/2031', '');

        $this->assertSame('', $error);
        $this->assertSame([[500, 6, 905], [70, 7, 906]], array_map(static fn (Documento $d): array => [$d->getId_doc(), $d->getId_ubi(), $d->getNum_reg()], $this->guardados));
        $this->assertSame('2031-06-15', $this->guardados[1]->getF_recibido()?->getIso());
        $this->assertNull($this->guardados[0]->getF_asignado(), 'la fecha vacía no se toca');
    }

    public function test_tipo_no_numerado_guarda_el_numero_de_ejemplares(): void
    {
        $this->asignar(false)->aCentros(1, [6 => '3'], '', '');

        $this->assertSame([3, null], [$this->guardados[0]->getNum_ejemplares(), $this->guardados[0]->getNum_reg()]);
    }

    /** Antes: una fecha mal escrita borraba la que tenía el documento. */
    public function test_una_fecha_mal_escrita_no_guarda_nada(): void
    {
        $this->existentes['7'] = [self::doc(70, 7)];

        $this->assertSame('Fecha no válida: ayer', $this->asignar()->aCentros(1, [7 => '906'], 'ayer', ''));
        $this->assertSame('Fecha no válida: 31/2/2031', $this->asignar()->aLugares(1, [20 => '1'], '', '31/2/2031'));
        $this->assertSame([], $this->guardados);
    }

    /** Duda 14: antes, sin documento en el lugar respondía «No se encuentra el documento» y no creaba nada. */
    public function test_a_lugares_crea_el_documento_si_falta_actualiza_el_que_hay_y_avisa_si_hay_varios(): void
    {
        $this->existentes['40#20'] = [self::doc(80, 40, 20)];
        $this->existentes['40#21'] = [self::doc(81, 40, 21), self::doc(82, 40, 21)];

        $error = $this->asignar()->aLugares(1, [20 => '905', 21 => '906', 22 => '907', 66 => '908'], '', '');

        $this->assertSame("No se encuentra el documento\n", $error, 'solo el lugar con dos documentos; el lugar inexistente se salta');
        $this->assertSame(
            [[80, 40, 20, 905], [500, 40, 22, 907]],
            array_map(static fn (Documento $d): array => [$d->getId_doc(), $d->getId_ubi(), $d->getId_lugar(), $d->getNum_reg()], $this->guardados),
            'actualiza el del lugar 20 y crea el del 22, en el centro del lugar'
        );
        $this->assertSame(1, $this->guardados[1]->getId_tipo_doc());
    }

    public function test_tipo_inexistente_y_fallo_al_guardar(): void
    {
        $this->assertSame('No se encuentra el documento', $this->asignar()->aCentros(2, [6 => '1'], '', ''));
        $this->falla = true;
        $this->assertSame("hay un error, no se ha guardado\nfallo de BD", $this->asignar()->aCentros(1, [6 => '1'], '', ''));
    }

    public function test_trasladar_a_otro_centro_y_lugar_y_sin_centro_nuevo_es_un_error(): void
    {
        $uc = new DocumentosTrasladar($this->repo());

        $this->assertSame('', $uc->execute([11, 99, 12], 8, 30));
        $this->assertSame([[11, 8, 30], [12, 8, 30]], array_map(static fn (Documento $d): array => [$d->getId_doc(), $d->getId_ubi(), $d->getId_lugar()], $this->guardados));

        $this->guardados = [];
        $uc->execute([11], 8, 0);
        $this->assertSame([8, 20], [$this->guardados[0]->getId_ubi(), $this->guardados[0]->getId_lugar()], 'sin lugar nuevo conserva el suyo');

        $this->guardados = [];
        $this->assertSame('Faltan parámetros: id_ubi_new', $uc->execute([11], 0, 30), 'antes: excepción al dejar el documento sin centro');
        $this->assertSame([], $this->guardados);

        $this->falla = true;
        $this->assertSame("hay un error, no se ha guardado\nfallo de BD", $uc->execute([11], 8, 0));
    }
}
