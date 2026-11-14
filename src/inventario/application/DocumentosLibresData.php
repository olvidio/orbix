<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\application\services\AlmacenInventarioService;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\inventario\domain\criteria\EgmField as EgmF;
use src\inventario\domain\criteria\WhereisField as WhF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Documentos de un tipo que hay en el almacén de la dl para añadirlos a un equipaje (`lista_docs_libres`).
 *
 * Sucesor de la lógica del controlador `lista_docs_libres.php` (fase 5, paso 5.2).
 * - El almacén es el centro de inventario definido en los parámetros de configuración
 *   ({@see AlmacenInventarioService}); si falta, la respuesta es solo un `aviso_txt`.
 * - Solo se ofrecen los documentos **libres**: se descartan los que ya van en este equipaje o en otro
 *   cuyas fechas se cruzan con las suyas (duda 13 de la sección 6b del plan de tests). Antes el
 *   controlador calculaba esos documentos ocupados y no los usaba, así que se ofrecían todos.
 */
final class DocumentosLibresData
{
    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private LugarRepositoryInterface $lugarRepository,
        private AlmacenInventarioService $almacen,
        private EgmRepositoryInterface $egmRepository,
        private WhereisRepositoryInterface $whereisRepository,
    ) {
    }

    /** @return array{a_valores: array<int, array<int, mixed>>}|array{aviso_txt: string}|array{} */
    public function execute(int $idEquipaje, int $idTipoDoc): array
    {
        $oEquipaje = $this->equipajeRepository->findById($idEquipaje);
        if ($oEquipaje === null) {
            return [];
        }
        $idUbiAlmacen = $this->almacen->idUbi();
        if ($idUbiAlmacen === null) {
            return ['aviso_txt' => $this->almacen->avisoSinDefinir()];
        }
        $ocupados = $this->documentosOcupados($idEquipaje, $oEquipaje->getF_ini()?->getIso(), $oEquipaje->getF_fin()?->getIso());
        $valores = [];
        $d = 0;
        $cDocumentos = $this->documentoRepository->matching(Criteria::create()
            ->where(Filter::eq(DocF::IdTipoDoc, $idTipoDoc))
            ->andWhere(Filter::eq(DocF::IdUbi, $idUbiAlmacen)));
        foreach ($cDocumentos as $oDocumento) {
            $d++;
            if (isset($ocupados[(int) $oDocumento->getId_doc()])) {
                continue;
            }
            $oLugar = $this->lugarRepository->findById((int) $oDocumento->getId_lugar());
            if ($oLugar === null) {
                continue;
            }
            $valores[$d] = [0 => $oDocumento->getId_doc(), 1 => $oLugar->getNom_lugar(), 2 => $oDocumento->getIdentificador()];
        }

        return ['a_valores' => $valores];
    }

    /**
     * Documentos que ya van en alguna maleta de este equipaje o de otro con fechas que se cruzan.
     *
     * @return array<int, true> id_doc => true
     */
    private function documentosOcupados(int $idEquipaje, ?string $fIniIso, ?string $fFinIso): array
    {
        $idsEquipaje = [$idEquipaje];
        if (!empty($fIniIso) && !empty($fFinIso)) {
            foreach ($this->equipajeRepository->getEquipajesCoincidentes($fIniIso, $fFinIso) as $id) {
                $idsEquipaje[] = (int) $id;
            }
        }
        $idsEgm = [];
        foreach ($this->egmRepository->matching(Criteria::create()->where(Filter::in(EgmF::IdEquipaje, array_values(array_unique($idsEquipaje))))) as $oEgm) {
            $idsEgm[] = (int) $oEgm->getId_item();
        }
        if ($idsEgm === []) {
            return [];
        }
        $ocupados = [];
        foreach ($this->whereisRepository->matching(Criteria::create()->where(Filter::in(WhF::IdItemEgm, $idsEgm))) as $oWhereis) {
            $ocupados[(int) $oWhereis->getId_doc()] = true;
        }

        return $ocupados;
    }
}
