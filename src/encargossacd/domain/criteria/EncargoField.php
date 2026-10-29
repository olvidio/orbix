<?php

namespace src\encargossacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\encargossacd\domain\entity\Encargo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EncargoField: string implements CriteriaField
{
    case IdEnc = 'id_enc';
    case IdTipoEnc = 'id_tipo_enc';
    case GrupoEncargo = 'grupo_encargo';
    case IdUbi = 'id_ubi';
    case IdZona = 'id_zona';
    case DescEnc = 'desc_enc';
    case IdiomaEnc = 'idioma_enc';
    case DescLugar = 'desc_lugar';
    case Observ = 'observ';
    case Orden = 'orden';
    case Prioridad = 'prioridad';
}
