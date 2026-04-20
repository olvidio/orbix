<?php

namespace src\actividades\application;

use src\ubis\application\services\DelegacionDropdown;
use web\Desplegable;

/**
 * Devuelve el HTML del desplegable de filtros de lugar (delegaciones +
 * regiones). Portado del case `filtro_lugar` del dispatcher legacy.
 */
class ActividadTipoGetFiltroLugar
{
    public function execute(array $input = []): string
    {
        $sfsv = (string)($input['entrada'] ?? '');

        $opciones = DelegacionDropdown::dlURegionesFiltro($sfsv);
        $oDesplFiltroLugar = Desplegable::desdeOpciones($opciones, 'filtro_lugar');
        $oDesplFiltroLugar->setAction('fnjs_lugar()');

        return $oDesplFiltroLugar->desplegable();
    }
}
