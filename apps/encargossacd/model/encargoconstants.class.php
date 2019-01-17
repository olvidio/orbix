<?php
namespace encargossacd\model;

class EncargoConstants {
    
    
    const ARRAY_IDIOMAS = [
        "ca_ES"=> "catalán",
        "es_ES"=> "castellano",
    ];
    
    /**
     * No funciona con el traducir normal, porque la traducción depende del destinatario,
     * no del usuario que está logeado
     *
     */
    const ARRAY_DEDICACION_IDIOMA = [
        "cat" => [ 	"m1"=> "matí",
            "t1"=> "tarda 1ª hora",
            "v1"=> "tarda 2ª hora",
            "m"=> "matins",
            "t"=> "tardes 1ª hora",
            "v"=> "tardes 2ª hora",
        ],
        "cas" => [	"m1"=> "mañana",
            "t1"=> "tarde 1ª hora",
            "v1"=> "tarde 2ª hora",
            "m"=> "mañanas",
            "t"=> "tardes 1ª hora",
            "v"=> "tardes 2ª hora",
        ],
        "*" => [	"m1"=> "mañana",
            "t1"=> "tarde 1ª hora",
            "v1"=> "tarde 2ª hora",
            "m"=> "mañanas",
            "t"=> "tardes 1ª hora",
            "v"=> "tardes 2ª hora",
        ],
    ];
    
    const ARRAY_TRAD=array(
        "cat" => array( "estudio" => "estudi", "descanso" => "descans" , "otros" => "altres"),
        "ing" => array( "estudio" => "study", "descanso" => "holiday" , "otros" => "others"),
        "cas" => array( "estudio" => "estudio", "descanso" => "descanso" , "otros" => "otros")
    );
    
    const ARRAY_OPCIONES_ENCARGOS = [
        "5020" => "estudio",
        "5030" => "descanso",
        "1110" => "rtm",
        "6000" => "otros",
    ];
    
    // -------------------------------------------- complejo ----------------------------
    // NO se usan, son solo para asegurar que exista la traducción
    function traduccion () {
        $txt =  _("lunes").
        _("martes").
        _("miércoles").
        _("jueves").
        _("viernes").
        _("sábado").
        _("domingo").
        _("laborables").
        _("festivos").
        _("todos").
        _("primer").
        _("segundo").
        _("tercer").
        _("cuarto").
        _("enero").
        _("febrero").
        _("marzo").
        _("abril").
        _("mayo").
        _("junio").
        _("julio").
        _("agosto").
        _("septiembre").
        _("octubre").
        _("noviembre").
        _("diciembre")	;
    }
    
    const OPCIONES_DIA_SEMANA = [
        '1'=> "lunes",
        '2'=>  "martes",
        '3'=>  "miércoles",
        '4'=>  "jueves",
        '5'=>  "viernes",
        '6'=>  "sábado",
        '7'=>  "domingo",
        '8'=>  "laborables",
        '9'=>  "festivos",
        "A"=>  "todos",  // como es var char(1) no me cabe el 10.
    ];
    
    const OPCIONES_DIA_REF = [
        '1'=> "lunes",
        '2'=>  "martes",
        '3'=>  "miércoles",
        '4'=>  "jueves",
        '5'=>  "viernes",
        '6'=>  "sábado",
        '7'=>  "domingo",
    ];
    
    const OPCIONES_ORDINALES = [
        '1'=>  "primer",
        '2'=>  "segundo",
        '3'=>  "tercer",
        '4'=>  "cuarto",
    ];
    
    const OPCIONES_MES = [
        '1'=>  "enero",
        '2'=>  "febrero",
        '3'=>  "marzo",
        '4'=>  "abril",
        '5'=>  "mayo",
        '6'=>  "junio",
        '7'=>  "julio",
        '8'=>  "agosto",
        '9'=>  "septiembre",
        '10'=>  "octubre",
        '11'=>  "noviembre",
        '12'=>  "diciembre",
    ];
    
    
}