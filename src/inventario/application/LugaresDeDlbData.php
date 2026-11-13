<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\LugarField as LugarF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Lugares de los centros de la dl que tienen lugares, agrupados por centro (`lista_docs_de_dlb`): para
 * asignar un tipo de documento a lugares o, con `inventario`, para elegir de cuáles imprimir el inventario.
 *
 * Sucesor de la lógica del controlador `lista_docs_de_dlb.php` (fase 5, paso 5.2). La respuesta es la misma.
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
        if ($paraInventario) {
            $nombreDoc = _("Para imprimir inventario");
        } else {
            $oTipoDoc = $this->tipoDocRepository->findById($idTipoDoc);
            if ($oTipoDoc === null) {
                return [];
            }
            $nomDoc = $oTipoDoc->getNom_doc();
            $nombreDoc = empty($nomDoc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nomDoc . ')';
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
}
