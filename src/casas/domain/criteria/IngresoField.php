<?php

namespace src\casas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\casas\domain\entity\Ingreso} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum IngresoField: string implements CriteriaField
{
    case IdActiv = 'id_activ';
    case Ingresos = 'ingresos';
    case NumAsistentes = 'num_asistentes';
    case IngresosPrevistos = 'ingresos_previstos';
    case NumAsistentesPrevistos = 'num_asistentes_previstos';
    case Observ = 'observ';
}
