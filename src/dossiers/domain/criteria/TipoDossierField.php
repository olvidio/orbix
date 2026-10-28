<?php

namespace src\dossiers\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\dossiers\domain\entity\TipoDossier} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TipoDossierField: string implements CriteriaField
{
    case IdTipoDossier = 'id_tipo_dossier';
    case Descripcion = 'descripcion';
    case TablaFrom = 'tabla_from';
    case TablaTo = 'tabla_to';
    case CampoTo = 'campo_to';
    case IdTipoDossierRel = 'id_tipo_dossier_rel';
    case PermisoLectura = 'permiso_lectura';
    case PermisoEscritura = 'permiso_escritura';
    case DependeModificar = 'depende_modificar';
    case App = 'app';
    case Class_ = 'class';
    case Db = 'db';
    case Codigo = 'codigo';
}
