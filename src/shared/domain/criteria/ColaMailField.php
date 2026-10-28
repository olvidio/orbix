<?php

namespace src\shared\domain\criteria;

/**
 * Campos de {@see \src\shared\domain\entity\ColaMail} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ColaMailField: string implements CriteriaField
{
    case UuidItem = 'uuid_item';
    case MailTo = 'mail_to';
    case Message = 'message';
    case Subject = 'subject';
    case Headers = 'headers';
    case WritedBy = 'writed_by';
    case Sended = 'sended';
}
