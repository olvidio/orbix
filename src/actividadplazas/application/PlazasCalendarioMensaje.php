<?php

namespace src\actividadplazas\application;

/**
 * Texto de ayuda cuando falta el registro de plazas «calendario» de una actividad.
 */
final class PlazasCalendarioMensaje
{
    public static function faltaRegistro(): string
    {
        return (string)_(
            "No puede guardar estas plazas todavía.\n\n"
            . "En Gestión de plazas se muestran datos del calendario común (todas las delegaciones), "
            . "pero sus cambios se guardan en los datos de su delegación.\n\n"
            . "Antes de editar concedidas (-c), pedidas (-p) o ceder plazas, la actividad debe tener "
            . "plazas en el calendario (resumen de la actividad → columna «calendario»).\n\n"
            . "Qué hacer:\n"
            . "1. Abra la actividad desde el listado o calendario de actividades.\n"
            . "2. Si su delegación organiza la actividad: indique plazas totales, publíquela "
            . "o impórtela (por ejemplo calendario CA).\n"
            . "3. En la actividad, menú «Plazas»: compruebe que hay plazas en calendario.\n"
            . "4. Vuelva a Gestión de plazas y edite de nuevo.\n\n"
            . "Si la actividad es de otra delegación, espere a que la publique y asigne plazas en calendario."
        );
    }
}
