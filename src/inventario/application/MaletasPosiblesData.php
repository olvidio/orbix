<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\application\services\AlmacenInventarioService;
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
 * Sucesor de la lógica del controlador (fase 5, paso 5.2). El almacén es el centro de inventario
 * definido en los parámetros de configuración ({@see AlmacenInventarioService}); si falta, solo se
 * ofrece «nuevo» y la respuesta lleva además un `aviso_txt`.
 */
final class MaletasPosiblesData
{
    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
        private EgmRepositoryInterface $egmRepository,
        private LugarRepositoryInterface $lugarRepository,
        private AlmacenInventarioService $almacen,
    ) {
    }

    /** @return array{a_opciones: array<int, string>, new_id_grupo: int, aviso_txt?: string}|array{} */
    public function execute(int $idEquipaje): array
    {
        $oEquipaje = $this->equipajeRepository->findById($idEquipaje);
        if ($oEquipaje === null) {
            return [];
        }
        $opciones = [];
        $idUbiAlmacen = $this->almacen->idUbi();
        if ($idUbiAlmacen !== null) {
            $coincidentes = $this->equipajeRepository->getEquipajesCoincidentes(
                (string) $oEquipaje->getF_ini()?->getIso(),
                (string) $oEquipaje->getF_fin()?->getIso(),
            );
            $ocupados = $this->egmRepository->getArrayIdFromIdEquipajes($coincidentes, 'lugar');
            $cLugares = $this->lugarRepository->matching(Criteria::create()->where(Filter::eq(LugarF::IdUbi, $idUbiAlmacen)));
            foreach ($cLugares as $oLugar) {
                if (!in_array($oLugar->getId_lugar(), $ocupados)) {
                    $opciones[$oLugar->getId_lugar()] = $oLugar->getNom_lugar();
                }
            }
        }
        $opciones[1] = _("nuevo");
        asort($opciones);

        $respuesta = ['a_opciones' => $opciones, 'new_id_grupo' => $this->egmRepository->getUltimoGrupo($idEquipaje) + 1];
        if ($idUbiAlmacen === null) {
            $respuesta['aviso_txt'] = $this->almacen->avisoSinDefinir();
        }

        return $respuesta;
    }
}
