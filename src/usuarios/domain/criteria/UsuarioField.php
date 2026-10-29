<?php

namespace src\usuarios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\usuarios\domain\entity\Usuario} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum UsuarioField: string implements CriteriaField
{
    case IdUsuario = 'id_usuario';
    case Usuario = 'usuario';
    case IdRole = 'id_role';
    case Password = 'password';
    case Email = 'email';
    case CsvIdPau = 'csv_id_pau';
    case NomUsuario = 'nom_usuario';
    case Has2fa = 'has_2fa';
    case Secret2fa = 'secret_2fa';
    case CambioPassword = 'cambio_password';
}
