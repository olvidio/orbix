<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Documentos de un tipo que hay en el almacén de la dl para añadirlos a un equipaje (`lista_docs_libres`).
 *
 * Sucesor de la lógica del controlador `lista_docs_libres.php` (fase 5, paso 5.2). La respuesta es la
 * misma. Dos cosas pendientes de decisión (dudas 12 y 13 de la sección 6b del plan de tests):
 * - el almacén es fijo ({@see self::ID_UBI_ALMACEN}, «dlb-Magatzem»): solo vale para esa dl;
 * - pese al nombre, **no** se descartan los documentos que ya están en otro equipaje de las mismas
 *   fechas: el controlador calculaba esos documentos ocupados y no los usaba (así desde que se creó el
 *   módulo). Ese cálculo, que no tenía efecto, se ha quitado.
 */
final class DocumentosLibresData
{
    public const ID_UBI_ALMACEN = 40;

    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /** @return array{a_valores: array<int, array<int, mixed>>}|array{} */
    public function execute(int $idEquipaje, int $idTipoDoc): array
    {
        if ($this->equipajeRepository->findById($idEquipaje) === null) {
            return [];
        }
        $valores = [];
        $d = 0;
        $cDocumentos = $this->documentoRepository->matching(Criteria::create()
            ->where(Filter::eq(DocF::IdTipoDoc, $idTipoDoc))
            ->andWhere(Filter::eq(DocF::IdUbi, self::ID_UBI_ALMACEN)));
        foreach ($cDocumentos as $oDocumento) {
            $d++;
            $oLugar = $this->lugarRepository->findById((int) $oDocumento->getId_lugar());
            if ($oLugar === null) {
                continue;
            }
            $valores[$d] = [0 => $oDocumento->getId_doc(), 1 => $oLugar->getNom_lugar(), 2 => $oDocumento->getIdentificador()];
        }

        return ['a_valores' => $valores];
    }
}
