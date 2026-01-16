<?php

namespace src\misas\domain\value_objects;

final class PlantillaConfig
{
    // Tipos de plantilla
    public const PLANTILLA_SEMANAL_UNO = 's1';
    public const PLANTILLA_DOMINGOS_UNO = 'd1';
    public const PLANTILLA_MENSUAL_UNO = 'm1';
    public const PLANTILLA_SEMANAL_TRES = 's3';
    public const PLANTILLA_DOMINGOS_TRES = 'd3';
    public const PLANTILLA_MENSUAL_TRES = 'm3';

    // Fechas e intervalos semanales
    public const INICIO_SEMANAL_UNO = '2001-01-01';
    public const FIN_SEMANAL_UNO = '2001-01-08';
    public const INICIO_SEMANAL_DOS = '2001-01-08';
    public const FIN_SEMANAL_DOS = '2001-01-15';
    public const INICIO_SEMANAL_TRES = '2001-01-15';
    public const FIN_SEMANAL_TRES = '2001-01-22';
    public const INTERVAL_SEMANAL = 'P7D';

    // Fechas e intervalos de domingos
    public const INICIO_DOMINGOS_UNO = '2001-10-01';
    public const FIN_DOMINGOS_UNO = '2001-10-12';
    public const INICIO_DOMINGOS_DOS = '2001-10-12';
    public const FIN_DOMINGOS_DOS = '2001-10-23';
    public const INICIO_DOMINGOS_TRES = '2001-10-23';
    public const FIN_DOMINGOS_TRES = '2001-11-03';
    public const INTERVAL_DOMINGOS = 'P11D';

    // Fechas e intervalos mensuales
    public const INICIO_MENSUAL_UNO = '2002-04-01';
    public const FIN_MENSUAL_UNO = '2002-05-06';
    public const INICIO_MENSUAL_DOS = '2002-05-06';
    public const FIN_MENSUAL_DOS = '2002-06-11';
    public const INICIO_MENSUAL_TRES = '2002-06-11';
    public const FIN_MENSUAL_TRES = '2002-07-15';
    public const INTERVAL_MENSUAL = 'P35D';

    // Plan de misas
    public const PLAN_DE_MISAS = 'p';

    private function __construct()
    {
        // Prevent instantiation - this is a constants-only class
    }
}
