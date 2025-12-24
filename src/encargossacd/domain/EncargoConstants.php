<?php

namespace src\encargossacd\domain;

class EncargoConstants
{

    function getOpcionesEncargos()
    {
        $opciones = [
            "5020" => _("estudio"),
            "5030" => _("descanso"),
            "1110" => _("retiro"),
            "6000" => _("otros"),
        ];
        return $opciones;
    }

    // -------------------------------------------- complejo ----------------------------
    // NO se usan, son solo para asegurar que exista la traducción
    function traduccion_c()
    {
        $txt =
            _("estudio") .
            _("descanso") .
            _("retiro") .
            _("otros") .
            _("lunes") .
            _("martes") .
            _("miércoles") .
            _("jueves") .
            _("viernes") .
            _("sábado") .
            _("domingo") .
            _("laborables") .
            _("festivos") .
            _("todos") .
            _("primer") .
            _("segundo") .
            _("tercer") .
            _("cuarto") .
            _("enero") .
            _("febrero") .
            _("marzo") .
            _("abril") .
            _("mayo") .
            _("junio") .
            _("julio") .
            _("agosto") .
            _("septiembre") .
            _("octubre") .
            _("noviembre") .
            _("diciembre");

        return $txt;
    }

    const OPCIONES_DIA_SEMANA = [
        '1' => "lunes",
        '2' => "martes",
        '3' => "miércoles",
        '4' => "jueves",
        '5' => "viernes",
        '6' => "sábado",
        '7' => "domingo",
        '8' => "laborables",
        '9' => "festivos",
        "A" => "todos",  // como es var char(1) no me cabe el 10.
    ];

    const OPCIONES_DIA_REF = [
        '1' => "lunes",
        '2' => "martes",
        '3' => "miércoles",
        '4' => "jueves",
        '5' => "viernes",
        '6' => "sábado",
        '7' => "domingo",
    ];

    const OPCIONES_ORDINALES = [
        '1' => "primer",
        '2' => "segundo",
        '3' => "tercer",
        '4' => "cuarto",
    ];

    const OPCIONES_MES = [
        '1' => "enero",
        '2' => "febrero",
        '3' => "marzo",
        '4' => "abril",
        '5' => "mayo",
        '6' => "junio",
        '7' => "julio",
        '8' => "agosto",
        '9' => "septiembre",
        '10' => "octubre",
        '11' => "noviembre",
        '12' => "diciembre",
    ];


}