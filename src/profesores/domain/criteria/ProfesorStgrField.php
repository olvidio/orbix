<?php

namespace src\profesores\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\profesores\domain\entity\ProfesorStgr} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ProfesorStgrField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdNom = 'id_nom';
    case IdTipoProfesor = 'id_tipo_profesor';
    case IdDepartamento = 'id_departamento';
    case EscritoNombramiento = 'escrito_nombramiento';
    case FNombramiento = 'f_nombramiento';
    case EscritoCese = 'escrito_cese';
    case FCese = 'f_cese';
}
