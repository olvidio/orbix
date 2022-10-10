<?php

namespace pasarela\model;

class Parametros
{


    /** @var ContribucionNoDuerme */
    private $activacion;

    public function __construct()
    {
        $this->activacion = new ContribucionNoDuerme();
    }

    public function getDefaultActivacion()
    {
        return $this->activacion->getDefault();
    }

    public function getExcepcionesActivacion()
    {
        return $this->activacion->getExcepciones();
    }

    public function getExcepcionesPerfil()
    {
        $a_excepciones = ['111000' => 'hola',
            '111001' => 'adios',
            '111015' => 'res de rs',
        ];

        return $a_excepciones;
    }

    public function getExcepcionesNombre()
    {
        $a_excepciones = ['111000' => 'hola',
            '111001' => 'adios',
            '111015' => 'res de rs',
        ];

        return $a_excepciones;
    }

    public function getExcepcionesTipo()
    {
        $a_excepciones = ['111000' => 'hola',
            '111001' => 'adios',
            '111015' => 'res de rs',
        ];

        return $a_excepciones;
    }

}