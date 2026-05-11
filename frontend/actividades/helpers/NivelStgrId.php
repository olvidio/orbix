<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

/**
 * Códigos de nivel STGR alineados con
 * {@see \src\actividades\domain\value_objects\NivelStgrId}.
 *
 * Solo constantes para valores por defecto y comparaciones en la capa front;
 * listas traducidas y validación del VO siguen en dominio.
 */
final class NivelStgrId
{
    public const B = 1;
    public const C1 = 2;
    public const C2 = 3;
    public const R = 4;
    public const CE = 5;
    public const NT = 6;
    public const X = 7;
    public const N = 9;
    public const E = 10;
    public const BC = 11;
}
