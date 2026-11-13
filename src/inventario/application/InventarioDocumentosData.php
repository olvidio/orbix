<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\application\support\NombreDocumento;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\TipoDoc;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Inventario de documentos para imprimir (`doc_imprimir_ctr` / `doc_imprimir_dlb`): los documentos no
 * eliminados de cada centro ({@see self::porUbis()}) o de cada lugar de la dl ({@see self::porLugares()}),
 * ordenados por lugar, bajo llave primero, colección y tipo.
 *
 * Sucesor de la lógica que había en los controladores `inventario_ctr.php` e `inventario_dlb.php`
 * (fase 5, paso 5.2, del plan de revisión de tests). La respuesta es la misma que daban.
 */
final class InventarioDocumentosData
{
    /** @var array<int, array{tipo: TipoDoc, coleccion: string}> */
    private array $tiposDoc = [];

    public function __construct(
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
        private ColeccionRepositoryInterface $coleccionRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /**
     * Documentos de cada centro, por nombre del centro. Columnas: casilla, documento, observaciones
     * del centro, observaciones.
     *
     * @param list<mixed> $idsUbi
     * @return array{a_valores: array<string, mixed>, a_llave: array<string, mixed>, a_tipo: array<string, mixed>, a_lugar: array<string, mixed>, a_nom_coleccion: array<string, mixed>}
     */
    public function porUbis(array $idsUbi): array
    {
        $out = self::vacio();
        foreach ($idsUbi as $raw) {
            if (!is_numeric($raw)) {
                continue;
            }
            $oUbi = $this->ubiInventarioRepository->findById((int) $raw);
            if ($oUbi === null) {
                continue;
            }
            $cDocumentos = $this->documentoRepository->matching(Criteria::create()
                ->where(Filter::eq(DocF::IdUbi, (int) $raw))
                ->andWhere(Filter::eq(DocF::Eliminado, false)));

            $filas = [];
            foreach ($cDocumentos as $oDocumento) {
                $lugar = '';
                $idLugar = $oDocumento->getId_lugar();
                if (!empty($idLugar)) {
                    $oLugar = $this->lugarRepository->findById((int) $idLugar);
                    if ($oLugar === null) {
                        continue;
                    }
                    $lugar = $oLugar->getNom_lugar();
                }
                $fila = $this->fila($oDocumento, $lugar);
                if ($fila === null) {
                    continue;
                }
                $fila['valores'][3] = $oDocumento->getObservCtrVo()?->value();
                $fila['valores'][4] = $oDocumento->getObservVo()?->value();
                $filas[] = $fila;
            }
            $this->anyadir($out, $oUbi->getNom_ubi(), $filas, null);
        }

        return $out;
    }

    /**
     * Documentos de cada lugar de la dl, por nombre del lugar (`a_lugar` lleva el nombre del centro).
     * Columnas: casilla, documento, identificador, observaciones, observaciones del centro.
     *
     * @param list<mixed> $idsLugar
     * @return array{a_valores: array<string, mixed>, a_llave: array<string, mixed>, a_tipo: array<string, mixed>, a_lugar: array<string, mixed>, a_nom_coleccion: array<string, mixed>}
     */
    public function porLugares(array $idsLugar): array
    {
        $out = self::vacio();
        foreach ($idsLugar as $raw) {
            if (!is_numeric($raw)) {
                continue;
            }
            $oLugar = $this->lugarRepository->findById((int) $raw);
            if ($oLugar === null) {
                continue;
            }
            $lugar = $oLugar->getNom_lugar();
            $oUbi = $this->ubiInventarioRepository->findById((int) $oLugar->getId_ubi());
            if ($oUbi === null) {
                continue;
            }
            $cDocumentos = $this->documentoRepository->matching(Criteria::create()
                ->where(Filter::eq(DocF::IdLugar, (int) $raw))
                ->andWhere(Filter::eq(DocF::Eliminado, false)));

            $filas = [];
            foreach ($cDocumentos as $oDocumento) {
                $fila = $this->fila($oDocumento, $lugar);
                if ($fila === null) {
                    continue;
                }
                $fila['valores'][3] = ['clase' => 'doc', 'valor' => $oDocumento->getIdentificadorVo()?->value()];
                $fila['valores'][4] = $oDocumento->getObservVo()?->value();
                $fila['valores'][5] = $oDocumento->getObservCtrVo()?->value();
                $filas[] = $fila;
            }
            $this->anyadir($out, $lugar, $filas, $oUbi->getNom_ubi());
        }

        return $out;
    }

    /**
     * Columnas comunes (casilla y documento) y claves de orden. Null si falta el tipo o su colección.
     *
     * @return array{valores: array<int, mixed>, llave: ?bool, tipo: string, lugar: string, coleccion: string}|null
     */
    private function fila(Documento $oDocumento, string $lugar): ?array
    {
        $tipo = $this->tipoDoc((int) $oDocumento->getId_tipo_doc());
        if ($tipo === null) {
            return null;
        }
        $oTipoDoc = $tipo['tipo'];
        $nom = NombreDocumento::compuesto($oTipoDoc, $oDocumento);

        return [
            'valores' => [
                1 => ['clase' => 'check', 'valor' => '<input type=checkbox>'],
                2 => ['clase' => 'doc', 'valor' => $nom],
            ],
            'llave' => $oTipoDoc->isBajo_llave(),
            'tipo' => $oTipoDoc->getSigla() . ' ' . $oTipoDoc->getNom_doc(),
            'lugar' => $lugar,
            'coleccion' => $tipo['coleccion'],
        ];
    }

    /**
     * Tipo de documento y nombre de su colección (con caché). Null si no existe el tipo o la colección.
     *
     * @return array{tipo: TipoDoc, coleccion: string}|null
     */
    private function tipoDoc(int $idTipoDoc): ?array
    {
        if (isset($this->tiposDoc[$idTipoDoc])) {
            return $this->tiposDoc[$idTipoDoc];
        }
        $oTipoDoc = $this->tipoDocRepository->findById($idTipoDoc);
        if ($oTipoDoc === null) {
            return null;
        }
        $coleccion = '';
        $idColeccion = $oTipoDoc->getId_coleccion();
        if (!empty($idColeccion)) {
            $oColeccion = $this->coleccionRepository->findById((int) $idColeccion);
            if ($oColeccion === null) {
                return null;
            }
            $coleccion = $oColeccion->getNom_coleccion();
        }

        return $this->tiposDoc[$idTipoDoc] = ['tipo' => $oTipoDoc, 'coleccion' => $coleccion];
    }

    /**
     * Ordena las filas de un grupo (por lugar, bajo llave primero, colección y tipo, con el mismo
     * `array_multisort` que el código original: deja las filas como lista desde 0) y las añade.
     *
     * @param array{a_valores: array<string, mixed>, a_llave: array<string, mixed>, a_tipo: array<string, mixed>, a_lugar: array<string, mixed>, a_nom_coleccion: array<string, mixed>} $out
     * @param-out array{a_valores: array<string, mixed>, a_llave: array<string, mixed>, a_tipo: array<string, mixed>, a_lugar: array<string, mixed>, a_nom_coleccion: array<string, mixed>} $out
     * @param list<array{valores: array<int, mixed>, llave: ?bool, tipo: string, lugar: string, coleccion: string}> $filas
     * @param string|null $lugarDelGrupo en `a_lugar`: null → el lugar de cada fila; texto → ese texto
     */
    private function anyadir(array &$out, string $clave, array $filas, ?string $lugarDelGrupo): void
    {
        if ($filas === []) {
            return;
        }
        $valores = array_column($filas, 'valores');
        $llave = array_column($filas, 'llave');
        $tipo = array_column($filas, 'tipo');
        $lugar = array_column($filas, 'lugar');
        $coleccion = array_column($filas, 'coleccion');
        array_multisort($lugar, SORT_ASC, $llave, SORT_NUMERIC, SORT_DESC, $coleccion, SORT_ASC, $tipo, SORT_ASC, SORT_NATURAL, $valores);

        $out['a_valores'][$clave] = $valores;
        $out['a_llave'][$clave] = $llave;
        $out['a_tipo'][$clave] = $tipo;
        $out['a_lugar'][$clave] = $lugarDelGrupo ?? $lugar;
        $out['a_nom_coleccion'][$clave] = $coleccion;
    }

    /** @return array{a_valores: array<string, mixed>, a_llave: array<string, mixed>, a_tipo: array<string, mixed>, a_lugar: array<string, mixed>, a_nom_coleccion: array<string, mixed>} */
    private static function vacio(): array
    {
        return ['a_valores' => [], 'a_llave' => [], 'a_tipo' => [], 'a_lugar' => [], 'a_nom_coleccion' => []];
    }
}
