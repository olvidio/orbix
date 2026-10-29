<?php

namespace src\inventario\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\inventario\domain\entity\TipoDoc} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TipoDocField: string implements CriteriaField
{
    case IdTipoDoc = 'id_tipo_doc';
    case NomDoc = 'nom_doc';
    case Sigla = 'sigla';
    case Observ = 'observ';
    case IdColeccion = 'id_coleccion';
    case BajoLlave = 'bajo_llave';
    case Vigente = 'vigente';
    case Numerado = 'numerado';
}
