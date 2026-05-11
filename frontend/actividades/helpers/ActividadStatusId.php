<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

/**
 * Códigos de estado de actividad alineados con
 * {@see \src\actividades\domain\value_objects\StatusId}.
 *
 * Solo constantes enteras para UI y consultas; validación e instanciación del VO
 * siguen en dominio (`src`).
 */
final class ActividadStatusId
{
    public const PROYECTO = 1;
    public const ACTUAL = 2;
    public const TERMINADA = 3;
    public const BORRABLE = 4;
    public const ALL = 9;
}
