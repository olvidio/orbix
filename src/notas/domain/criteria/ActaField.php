<?php

namespace src\notas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\notas\domain\entity\Acta} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActaField: string implements CriteriaField
{
    case Acta = 'acta';
    case IdAsignatura = 'id_asignatura';
    case IdActiv = 'id_activ';
    case FActa = 'f_acta';
    case Libro = 'libro';
    case Pagina = 'pagina';
    case Linea = 'linea';
    case Lugar = 'lugar';
    case Observ = 'observ';
    case HashImpreso = 'hash_impreso';
    case IdSchema = 'id_schema'; // columna de las vistas `*_all`; no es propiedad de la entidad
}
