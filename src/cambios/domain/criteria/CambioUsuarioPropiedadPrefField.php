<?php

namespace src\cambios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\cambios\domain\entity\CambioUsuarioPropiedadPref} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CambioUsuarioPropiedadPrefField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdItemUsuarioObjeto = 'id_item_usuario_objeto';
    case Propiedad = 'propiedad';
    case Valor = 'valor';
    case ValorOld = 'valor_old';
    case ValorNew = 'valor_new';
}
