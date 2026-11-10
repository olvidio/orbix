<?php

namespace Tests\factories\dbextern;

use src\dbextern\domain\entity\PersonaBDU;

/**
 * Factory de PersonaBDU para tests. `identif` es la clave de la tabla tmp_bdu.
 * Ids de test 990000001..990999999: los reales empiezan por el tipo de persona (1-4).
 */
class PersonaBDUFactory
{
    public const ID_BASE = 990000000;

    public function createSimple(?int $identif = null): PersonaBDU
    {
        $o = new PersonaBDU();
        $o->setIdentif($identif ?? (self::ID_BASE + random_int(1, 999999)));
        $o->setApenom('de la Torre y Pérez, María José');
        $o->setDl('dlb');
        $o->setCtr('Centro Test');
        $o->setLugar_Naci('Sevilla');
        $o->setFecha_Naci('1990-05-17');
        $o->setEmail('test@example.org');
        $o->setTfno_Movil('600000000');
        $o->setCe('2Madrid: 05-08');
        $o->setProfesion_cargo('Profesora');
        $o->setTitulo_Estudios('Derecho');
        $o->setEncargos('Ninguno');
        $o->setIncorporacion('Nu 15-03-2001');
        $o->setPertenece_r('mad');
        $o->setCamb_fic('');
        $o->setCompartida_con_r('');

        return $o;
    }
}
