<?php

namespace src\cambios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\cambios\domain\entity\CambioUsuario} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CambioUsuarioField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdSchemaCambio = 'id_schema_cambio';
    case IdItemCambio = 'id_item_cambio';
    case IdUsuario = 'id_usuario';
    case Sfsv = 'sfsv';
    case AvisoTipo = 'aviso_tipo';
    case Avisado = 'avisado';
}
