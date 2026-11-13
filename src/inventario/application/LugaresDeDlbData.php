<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\LugarField as LugarF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Dónde asignar un tipo de documento o, con `inventario`, de dónde imprimir el inventario: los lugares de
 * los centros que tienen lugares, agrupados por centro (`lista_docs_de_dlb`), o los centros sin lugares
 * (`lista_de_ctr_con_docs`).
 *
 * Sucesor de la lógica de esos dos controladores (fase 5, paso 5.2). La respuesta es la misma.
 */
final class LugaresDeDlbData
{
    public function __construct(
        private TipoDocRepositoryInterface $tipoDocRepository,
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /** @return array{a_valores: array<int, array<int, array<int|string, mixed>>>, aGrupos: array<int, string>, nombreDoc: string}|array{} */
    public function execute(int $idTipoDoc, bool $paraInventario): array
    {
        $nombreDoc = $this->nombreDoc($idTipoDoc, $paraInventario);
        if ($nombreDoc === null) {
            return [];
        }
        $valores = [];
        $grupos = [];
        foreach ($this->ubiInventarioRepository->getUbisInventarioLugar(true) as $oUbi) {
            $idUbi = $oUbi->getId_ubi();
            $grupos[$idUbi] = $oUbi->getNom_ubi();
            $a = 0;
            $cLugares = $this->lugarRepository->matching(Criteria::create()->where(Filter::eq(LugarF::IdUbi, $idUbi))->orderBy(LugarF::NomLugar));
            foreach ($cLugares as $oLugar) {
                $a++;
                $valores[$idUbi][$a] = [
                    'sel' => $paraInventario ? ['id' => $oLugar->getId_lugar(), 'select' => ''] : $oLugar->getId_lugar(),
                    1 => $oLugar->getNom_lugar(),
                ];
            }
        }

        return ['a_valores' => $valores, 'aGrupos' => $grupos, 'nombreDoc' => $nombreDoc];
    }

    /**
     * Centros sin lugares (los ctr), ordenados sin acentos (`lista_de_ctr_con_docs`).
     *
     * @return array{a_valores: list<array<int|string, mixed>>, nombreDoc: string}|array{}
     */
    public function centros(int $idTipoDoc, bool $paraInventario): array
    {
        $nombreDoc = $this->nombreDoc($idTipoDoc, $paraInventario);
        if ($nombreDoc === null) {
            return [];
        }
        $valores = [];
        foreach ($this->ubiInventarioRepository->getUbisInventarioLugar(false) as $oUbi) {
            $valores[] = ['sel' => $oUbi->getId_ubi(), 1 => $oUbi->getNom_ubi()];
        }
        $claves = FuncTablasSupport::clavesOrdenSinAcentos(array_column($valores, 1));
        array_multisort($claves, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $valores);

        return ['a_valores' => $valores, 'nombreDoc' => $nombreDoc];
    }

    private function nombreDoc(int $idTipoDoc, bool $paraInventario): ?string
    {
        if ($paraInventario) {
            return _("Para imprimir inventario");
        }
        $oTipoDoc = $this->tipoDocRepository->findById($idTipoDoc);
        if ($oTipoDoc === null) {
            return null;
        }
        $nomDoc = $oTipoDoc->getNom_doc();

        return empty($nomDoc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nomDoc . ')';
    }
}
