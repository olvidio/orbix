<?php

namespace src\tablonanuncios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\tablonanuncios\domain\entity\Anuncio} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum AnuncioField: string implements CriteriaField
{
    case UuidItem = 'uuid_item';
    case UsuarioCreador = 'usuario_creador';
    case EsquemaEmisor = 'esquema_emisor';
    case EsquemaDestino = 'esquema_destino';
    case TextoAnuncio = 'texto_anuncio';
    case Idioma = 'idioma';
    case Tablon = 'tablon';
    case TAnotado = 't_anotado';
    case TEliminado = 't_eliminado';
    case Categoria = 'categoria';
}
