<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\criteria\EgmField as EgmF;
use src\inventario\domain\ListaDocsGrupo;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\security\HashB;

/**
 * Maletas (grupos) de un equipaje con sus documentos y las cápsulas para añadir documentos y eliminar
 * el grupo (`equipajes_egm`).
 *
 * Sucesor de la lógica del controlador `equipajes_egm.php` (fase 5, paso 5.2). La respuesta es la misma.
 */
final class EquipajeEgmData
{
    public function __construct(
        private EgmRepositoryInterface $egmRepository,
        private LugarRepositoryInterface $lugarRepository,
        private ListaDocsGrupo $listaDocsGrupo,
    ) {
    }

    /** @return array{a_egm: array<int, array<string, mixed>>} */
    public function execute(int $idEquipaje): array
    {
        $egms = [];
        $i = 0;
        $cEgm = $this->egmRepository->matching(Criteria::create()->where(Filter::eq(EgmF::IdEquipaje, $idEquipaje))->orderBy(EgmF::IdGrupo));
        foreach ($cEgm as $oEgm) {
            $i++;
            $idGrupo = $oEgm->getId_grupo();
            $idLugar = $oEgm->getId_lugar();
            $egms[$i] = [
                'id_grupo' => $idGrupo,
                'id_lugar' => $idLugar,
                'texto' => $oEgm->getTextoVo()?->value(),
                'nom_lugar' => (string) $this->lugarRepository->findById((int) $idLugar)?->getNom_lugar(),
            ];
            if ($idLugar === null || $idGrupo === null) {
                continue;
            }
            $datos = $this->listaDocsGrupo->listaDocsGrupo($idEquipaje, $idLugar, $idGrupo);
            $egms[$i]['a_valores'] = $datos['a_valores'];
            $egms[$i]['id_item_egm'] = $datos['id_item_egm'];
            $idItemEgm = (int) ($datos['id_item_egm'] ?? 0);
            if ($idItemEgm > 0) {
                $egms[$i]['ctx_add'] = HashB::sign('equipajes_add_doc', ['id_item_egm' => $idItemEgm]);
            }
            $egms[$i]['ctx_eliminar_grupo'] = HashB::sign('equipajes_eliminar_grupo', ['id_grupo' => $idGrupo, 'id_equipaje' => $idEquipaje]);
        }

        return ['a_egm' => $egms];
    }
}
