<?php

namespace src\certificados\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\certificados\domain\entity\CertificadoEmitido} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 * No incluye `documento` (bytea): no se filtra ni se ordena por él.
 */
enum CertificadoEmitidoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdNom = 'id_nom';
    case Nom = 'nom';
    case Idioma = 'idioma';
    case Destino = 'destino';
    case Certificado = 'certificado';
    case FCertificado = 'f_certificado';
    case EsquemaEmisor = 'esquema_emisor';
    case Firmado = 'firmado';
    case FEnviado = 'f_enviado';
}
