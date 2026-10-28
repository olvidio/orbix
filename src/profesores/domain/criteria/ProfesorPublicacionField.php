<?php

namespace src\profesores\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\profesores\domain\entity\ProfesorPublicacion} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ProfesorPublicacionField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdNom = 'id_nom';
    case TipoPublicacion = 'tipo_publicacion';
    case Titulo = 'titulo';
    case Editorial = 'editorial';
    case Coleccion = 'coleccion';
    case Pendiente = 'pendiente';
    case Referencia = 'referencia';
    case FPublicacion = 'f_publicacion';
    case Lugar = 'lugar';
    case Observ = 'observ';
}
