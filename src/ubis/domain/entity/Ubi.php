<?php

namespace src\ubis\domain\entity;

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbiFactory;


class Ubi
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe vuit.
     */
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public static function NewUbi(int|string $id_ubi): Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos|null
    {
        return DependencyResolver::get(UbiFactory::class)->newUbi($id_ubi);
    }
}