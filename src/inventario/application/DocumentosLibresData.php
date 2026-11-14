<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\application\services\AlmacenInventarioService;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Documentos de un tipo que hay en el almacén de la dl para añadirlos a un equipaje (`lista_docs_libres`).
 *
 * Sucesor de la lógica del controlador `lista_docs_libres.php` (fase 5, paso 5.2).
 * - El almacén es el centro de inventario definido en los parámetros de configuración
 *   ({@see AlmacenInventarioService}); si falta, la respuesta es solo un `aviso_txt`.
 * - Pendiente de decisión (duda 13 de la sección 6b del plan de tests): pese al nombre, **no** se
 *   descartan los documentos que ya están en otro equipaje de las mismas fechas: el controlador
 *   calculaba esos documentos ocupados y no los usaba (así desde que se creó el módulo). Ese cálculo,
 *   que no tenía efecto, se ha quitado.
 */
final class DocumentosLibresData
{
    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private LugarRepositoryInterface $lugarRepository,
        private AlmacenInventarioService $almacen,
    ) {
    }

    /** @return array{a_valores: array<int, array<int, mixed>>}|array{aviso_txt: string}|array{} */
    public function execute(int $idEquipaje, int $idTipoDoc): array
    {
        if ($this->equipajeRepository->findById($idEquipaje) === null) {
            return [];
        }
        $idUbiAlmacen = $this->almacen->idUbi();
        if ($idUbiAlmacen === null) {
            return ['aviso_txt' => $this->almacen->avisoSinDefinir()];
        }
        $valores = [];
        $d = 0;
        $cDocumentos = $this->documentoRepository->matching(Criteria::create()
            ->where(Filter::eq(DocF::IdTipoDoc, $idTipoDoc))
            ->andWhere(Filter::eq(DocF::IdUbi, $idUbiAlmacen)));
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
