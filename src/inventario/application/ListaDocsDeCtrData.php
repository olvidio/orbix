<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\application\support\NombreDocumento;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\security\HashB;

/**
 * Documentos de un centro y/o lugar para trasladarlos (`lista_docs_de_ctr`, pantalla
 * `traslado_doc_lista`): cada fila lleva la cápsula para `traslado_doc_guardar`; orden por lugar, bajo
 * llave primero, colección y tipo.
 *
 * Sucesor de la lógica del controlador `lista_docs_de_ctr.php` (fase 5, paso 5.2). Corrige: el lugar
 * de cada documento salía siempre vacío (se usaba una variable que nunca se leía del documento), así que
 * no se ordenaba por lugar; el rango de números tampoco salía (mismas variables sin leer); y sin centro
 * ni lugar se devolvían todos los documentos de la dl.
 */
final class ListaDocsDeCtrData
{
    public function __construct(
        private DocumentoRepositoryInterface $documentoRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
        private ColeccionRepositoryInterface $coleccionRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /**
     * @return array{a_valores: list<array<int|string, mixed>>}
     */
    public function execute(int $idUbi, int $idLugar): array
    {
        if ($idUbi <= 0 && $idLugar <= 0) {
            return ['a_valores' => []];
        }
        $criteria = Criteria::create();
        if ($idUbi > 0) {
            $criteria = $criteria->where(Filter::eq(DocF::IdUbi, $idUbi));
        }
        if ($idLugar > 0) {
            $criteria = $criteria->andWhere(Filter::eq(DocF::IdLugar, $idLugar));
        }

        $colecciones = [];
        $lugares = [];
        $valores = [];
        $llave = [];
        $tipo = [];
        $lugar = [];
        $coleccion = [];
        foreach ($this->documentoRepository->matching($criteria) as $oDocumento) {
            $oTipoDoc = $this->tipoDocRepository->findById((int) $oDocumento->getId_tipo_doc());
            if ($oTipoDoc === null) {
                continue;
            }
            $nomColeccion = '';
            $idColeccion = $oTipoDoc->getId_coleccion();
            if (!empty($idColeccion)) {
                $colecciones[$idColeccion] ??= $this->coleccionRepository->findById((int) $idColeccion)?->getNom_coleccion();
                if ($colecciones[$idColeccion] === null) {
                    continue;
                }
                $nomColeccion = $colecciones[$idColeccion];
            }
            // Un lugar que ya no existe no esconde el documento (es justo el que hay que trasladar).
            $nomLugar = '';
            $idLugarDoc = $oDocumento->getId_lugar();
            if (!empty($idLugarDoc)) {
                $lugares[$idLugarDoc] ??= (string) $this->lugarRepository->findById($idLugarDoc)?->getNom_lugar();
                $nomLugar = $lugares[$idLugarDoc];
            }

            $valores[] = [
                'sel' => HashB::sign('traslado_doc_guardar', ['id_doc' => $oDocumento->getId_doc()]),
                1 => ['clase' => 'doc', 'valor' => NombreDocumento::compuesto($oTipoDoc, $oDocumento, ' ')],
                2 => $oDocumento->getObservVo()?->value(),
            ];
            $llave[] = $oTipoDoc->isBajo_llave();
            $tipo[] = $oTipoDoc->getSigla() . ' ' . $oTipoDoc->getNom_doc();
            $lugar[] = $nomLugar;
            $coleccion[] = $nomColeccion;
        }
        if ($valores !== []) {
            array_multisort($lugar, SORT_ASC, $llave, SORT_NUMERIC, SORT_DESC, $coleccion, SORT_ASC, $tipo, SORT_ASC, SORT_NATURAL, $valores);
        }

        return ['a_valores' => $valores];
    }
}
