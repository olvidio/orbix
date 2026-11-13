<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Por tipo de documento: centros (y lugares) que lo tienen, con su número (`lista_docs_asignados_por_tipo`),
 * o centros que no lo tienen (`lista_docs_no_asignados_por_tipo`), ordenados sin acentos.
 *
 * Sucesor de la lógica de esos dos controladores (fase 5, paso 5.2). La respuesta es la misma.
 */
final class DocumentosPorTipoData
{
    public function __construct(
        private TipoDocRepositoryInterface $tipoDocRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /** @return array<string, mixed> */
    public function asignados(int $idTipoDoc): array
    {
        $nombreDoc = $this->nombreDoc($idTipoDoc);
        if ($nombreDoc === null) {
            return [];
        }
        $valores = [];
        foreach ($this->documentoRepository->matching(Criteria::create()->where(Filter::eq(DocF::IdTipoDoc, $idTipoDoc))) as $oDocumento) {
            $oUbi = $this->ubiInventarioRepository->findById((int) $oDocumento->getId_ubi());
            if ($oUbi === null) {
                continue;
            }
            $nomUbi = $oUbi->getNom_ubi();
            $idLugar = $oDocumento->getId_lugar();
            if (!empty($idLugar)) {
                $oLugar = $this->lugarRepository->findById((int) $idLugar);
                if ($oLugar === null) {
                    continue;
                }
                $nomUbi .= ' --> ' . $oLugar->getNom_lugar();
            }
            $valores[] = [1 => $nomUbi, 2 => $oDocumento->getNum_reg()];
        }

        return $this->respuesta($valores, [ucfirst(_("centro - lugar")), ucfirst(_("número"))], $nombreDoc);
    }

    /** @return array<string, mixed> */
    public function noAsignados(int $idTipoDoc): array
    {
        $nombreDoc = $this->nombreDoc($idTipoDoc);
        if ($nombreDoc === null) {
            return [];
        }
        $valores = [];
        foreach ($this->ubiInventarioRepository->matching(Criteria::create()) as $oUbi) {
            $tiene = $this->documentoRepository->matching(Criteria::create()
                ->where(Filter::eq(DocF::IdTipoDoc, $idTipoDoc))
                ->andWhere(Filter::eq(DocF::IdUbi, $oUbi->getId_ubi())));
            if ($tiene !== []) {
                continue;
            }
            $valores[] = [1 => $oUbi->getNom_ubi()];
        }

        return $this->respuesta($valores, [ucfirst(_("centro"))], $nombreDoc);
    }

    private function nombreDoc(int $idTipoDoc): ?string
    {
        $oTipoDoc = $this->tipoDocRepository->findById($idTipoDoc);
        if ($oTipoDoc === null) {
            return null;
        }
        $nomDoc = $oTipoDoc->getNom_doc();

        return empty($nomDoc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nomDoc . ')';
    }

    /**
     * @param list<array<int, mixed>> $valores
     * @param list<string> $cabeceras
     * @return array<string, mixed>
     */
    private function respuesta(array $valores, array $cabeceras, string $nombreDoc): array
    {
        $claves = FuncTablasSupport::clavesOrdenSinAcentos(array_column($valores, 1));
        array_multisort($claves, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $valores);

        return ['a_cabeceras' => $cabeceras, 'a_botones' => [], 'a_valores' => $valores, 'nombreDoc' => $nombreDoc];
    }
}
