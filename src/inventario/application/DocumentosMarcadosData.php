<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\inventario\domain\entity\Documento;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Documentos marcados como perdidos (`lista_docs_perdidos`), en búsqueda (`lista_docs_en_busqueda`) o
 * con observaciones (`lista_docs_con_observaciones`): centro (y lugar), documento, número de registro
 * (y fecha de pérdida u observaciones), ordenados por centro sin acentos.
 *
 * Sucesor de la lógica de esos tres controladores, que estaba duplicada (fase 5, paso 5.2). La respuesta
 * es la misma (salvo que, sin documentos con observaciones, `a_valores` era una variable sin definir).
 */
final class DocumentosMarcadosData
{
    public function __construct(
        private DocumentoRepositoryInterface $documentoRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /** @return array{a_valores: list<array<int, mixed>>} */
    public function perdidos(): array
    {
        return $this->lista(
            $this->documentoRepository->matching(Criteria::create()->where(Filter::eq(DocF::Perdido, true))),
            static fn (Documento $d): array => [4 => $d->getF_perdido()?->getFromLocal()],
        );
    }

    /** @return array{a_valores: list<array<int, mixed>>} */
    public function enBusqueda(): array
    {
        return $this->lista(
            $this->documentoRepository->matching(Criteria::create()->where(Filter::eq(DocF::EnBusqueda, true))),
            static fn (Documento $d): array => [],
        );
    }

    /**
     * Documentos no eliminados ni perdidos con observaciones de la dl o del centro (sin repetir).
     *
     * @return array{a_valores: list<array<int, mixed>>}
     */
    public function conObservaciones(): array
    {
        $documentos = [];
        foreach ([DocF::Observ, DocF::ObservCtr] as $campo) {
            $c = $this->documentoRepository->matching(Criteria::create()
                ->where(Filter::eq(DocF::Eliminado, false))
                ->andWhere(Filter::eq(DocF::Perdido, false))
                ->andWhere(Filter::isNotNull($campo)));
            foreach ($c as $oDocumento) {
                $documentos[$oDocumento->getId_doc()] ??= $oDocumento;
            }
        }

        return $this->lista($documentos, static fn (Documento $d): array => [
            4 => $d->getObservVo()?->value(),
            5 => $d->getObservCtrVo()?->value(),
        ]);
    }

    /**
     * @param iterable<Documento> $documentos
     * @param callable(Documento): array<int, mixed> $columnasExtra
     * @return array{a_valores: list<array<int, mixed>>}
     */
    private function lista(iterable $documentos, callable $columnasExtra): array
    {
        $valores = [];
        $nombres = [];
        foreach ($documentos as $oDocumento) {
            $oTipoDoc = $this->tipoDocRepository->findById((int) $oDocumento->getId_tipo_doc());
            if ($oTipoDoc === null) {
                continue;
            }
            $nomDoc = $oTipoDoc->getNom_doc();
            $nombreDoc = empty($nomDoc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nomDoc . ')';
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
            $valores[] = [1 => $nomUbi, 2 => $nombreDoc, 3 => $oDocumento->getNum_reg()] + $columnasExtra($oDocumento);
            $nombres[] = $nomUbi;
        }
        if ($valores !== []) {
            $claves = FuncTablasSupport::clavesOrdenSinAcentos($nombres);
            array_multisort($claves, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $valores);
        }

        return ['a_valores' => $valores];
    }
}
