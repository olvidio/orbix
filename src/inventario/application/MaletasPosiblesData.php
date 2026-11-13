<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\criteria\LugarField as LugarF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Maletas que se pueden añadir a un equipaje (`lista_equipajes_posibles_maletas`): los lugares del
 * almacén que no están ya en un equipaje de fechas coincidentes, más la opción «nuevo» (valor 1), y el
 * número del grupo siguiente.
 *
 * Sucesor de la lógica del controlador (fase 5, paso 5.2). La respuesta es la misma. El almacén es el
 * mismo id fijo que en {@see DocumentosLibresData::ID_UBI_ALMACEN} (duda 12 del plan de tests).
 */
final class MaletasPosiblesData
{
    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
        private EgmRepositoryInterface $egmRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /** @return array{a_opciones: array<int, string>, new_id_grupo: int}|array{} */
    public function execute(int $idEquipaje): array
    {
        $oEquipaje = $this->equipajeRepository->findById($idEquipaje);
        if ($oEquipaje === null) {
            return [];
        }
        $coincidentes = $this->equipajeRepository->getEquipajesCoincidentes(
            (string) $oEquipaje->getF_ini()?->getIso(),
            (string) $oEquipaje->getF_fin()?->getIso(),
        );
        $ocupados = $this->egmRepository->getArrayIdFromIdEquipajes($coincidentes, 'lugar');

        $opciones = [];
        $cLugares = $this->lugarRepository->matching(Criteria::create()->where(Filter::eq(LugarF::IdUbi, DocumentosLibresData::ID_UBI_ALMACEN)));
        foreach ($cLugares as $oLugar) {
            if (!in_array($oLugar->getId_lugar(), $ocupados)) {
                $opciones[$oLugar->getId_lugar()] = $oLugar->getNom_lugar();
            }
        }
        $opciones[1] = _("nuevo");
        asort($opciones);

        return ['a_opciones' => $opciones, 'new_id_grupo' => $this->egmRepository->getUltimoGrupo($idEquipaje) + 1];
    }
}
