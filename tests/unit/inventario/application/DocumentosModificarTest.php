<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\inventario\application\DocumentosModificar;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Modificación en lote de documentos ({@see DocumentosModificar}): solo los campos marcados, fechas
 * vacías que borran, fechas mal escritas que no tocan nada, eliminado sí/no, rango de números y errores.
 */
final class DocumentosModificarTest extends TestCase
{
    private DocumentoRepositoryInterface&MockObject $repo;
    /** @var array<int, Documento> */
    private array $docs = [];
    /** @var list<int> */
    private array $guardados = [];
    private mixed $sesion;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION ?? [];
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF-8';
        foreach ([11, 12] as $id) {
            $d = new Documento();
            $d->setId_doc($id);
            $d->setId_tipo_doc(1);
            $d->setId_ubi(1);
            $d->setF_recibido(new DateTimeLocal('2020-01-01'));
            $d->setF_asignado(new DateTimeLocal('2020-02-02'));
            $d->setEliminado(false);
            $d->setNum_ini(5);
            $this->docs[$id] = $d;
        }
        $this->repo = $this->createMock(DocumentoRepositoryInterface::class);
        $this->repo->method('findById')->willReturnCallback(fn (int $id): ?Documento => $this->docs[$id] ?? null);
        $this->repo->method('Guardar')->willReturnCallback(function (Documento $d): bool {
            $this->guardados[] = $d->getId_doc();
            return $d->getId_doc() !== 13;
        });
        $this->repo->method('getErrorTxt')->willReturn('fallo de BD');
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesion;
    }

    private static function claves(int ...$ids): string
    {
        return implode('#', array_map(static fn (int $id): string => FuncTablasSupport::urlsafeB64encode((string) json_encode(['id_doc' => $id])), $ids));
    }

    /** @param array<string, mixed> $in */
    private function ejecutar(array $in): string
    {
        return (new DocumentosModificar($this->repo))->execute($in);
    }

    public function test_solo_se_cambian_los_campos_marcados(): void
    {
        $error = $this->ejecutar([
            'documentos' => self::claves(11, 12) . '#',
            'chk_f_recibido' => 'on', 'f_recibido' => '15/6/2031',
            'f_asignado' => '1/1/2031',                        // sin marcar: no se toca
            'chk_eliminado' => 'on', 'eliminado' => '1',
            'chk_num_ini' => 'on', 'num_ini' => '',
            'chk_num_fin' => 'on', 'num_fin' => '9',
        ]);

        $this->assertSame('', $error);
        $this->assertSame([11, 12], $this->guardados);
        $d = $this->docs[11];
        $this->assertSame('2031-06-15', $d->getF_recibido()?->getIso());
        $this->assertSame('2020-02-02', $d->getF_asignado()?->getIso());
        $this->assertTrue($d->isEliminado());
        $this->assertSame([null, 9], [$d->getNum_ini(), $d->getNum_fin()]);
    }

    public function test_fecha_vacia_borra_la_que_habia_y_eliminado_2_la_desmarca(): void
    {
        $this->docs[11]->setEliminado(true);

        $this->assertSame('', $this->ejecutar(['documentos' => self::claves(11), 'chk_f_recibido' => '1', 'f_recibido' => '', 'chk_eliminado' => '1', 'eliminado' => '2']));
        $this->assertNull($this->docs[11]->getF_recibido());
        $this->assertFalse($this->docs[11]->isEliminado());
    }

    public function test_una_fecha_mal_escrita_no_toca_nada(): void
    {
        $error = $this->ejecutar(['documentos' => self::claves(11), 'chk_f_recibido' => 'on', 'f_recibido' => 'ayer', 'chk_f_eliminado' => 'on', 'f_eliminado' => '31/2/2031']);

        $this->assertSame('Fecha no válida: ayer, 31/2/2031', $error);
        $this->assertSame([], $this->guardados);
    }

    public function test_avisos_sin_documentos_sin_campos_o_sin_ningun_documento_valido(): void
    {
        $this->assertSame('No ha seleccionado ningún documento', $this->ejecutar(['chk_num_ini' => 'on']));
        $this->assertSame('Debe marcar al menos un campo a modificar', $this->ejecutar(['documentos' => self::claves(11), 'num_ini' => '3']));
        $this->assertSame('No se pudo aplicar la modificación a los documentos seleccionados', $this->ejecutar(['documentos' => 'zz#' . self::claves(99), 'chk_num_ini' => 'on']));
        $this->assertSame([], $this->guardados);
    }

    public function test_un_fallo_al_guardar_se_devuelve_con_el_error_del_repositorio(): void
    {
        $d = new Documento();
        $d->setId_doc(13);
        $d->setId_tipo_doc(1);
        $d->setId_ubi(1);
        $this->docs[13] = $d;

        $error = $this->ejecutar(['documentos' => self::claves(11, 13), 'chk_num_ini' => 'on', 'num_ini' => '2']);

        $this->assertSame("hay un error, no se ha guardado\nfallo de BD", $error);
        $this->assertSame([11, 13], $this->guardados);
    }
}
