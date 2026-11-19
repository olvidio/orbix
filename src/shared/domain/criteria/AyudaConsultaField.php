<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

/**
 * Campos de {@see \src\shared\domain\entity\AyudaConsulta} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum AyudaConsultaField: string implements CriteriaField
{
    case UuidItem = 'uuid_item';
    case FConsulta = 'f_consulta';
    case Pregunta = 'pregunta';
    case Esquema = 'esquema';
    case Pantalla = 'pantalla';
    case NResultados = 'n_resultados';
    case Puntuacion = 'puntuacion';
    case Fuente = 'fuente';
    case Util = 'util';
    case Estado = 'estado';
}
