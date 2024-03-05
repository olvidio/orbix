<?php

namespace misas\domain\entity;

use misas\domain\repositories\InicialesSacdRepository;
use personas\model\entity\PersonaEx;
use personas\model\entity\PersonaSacd;

class InicialesSacd extends InicialesSacdDB
{

    public function iniciales($id_nom): string
    {
        $InicialesSacdRepository = new InicialesSacdRepository();
        $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
        if ($InicialesSacd === null) {
            if ($id_nom > 0) {
                $PersonaSacd = new PersonaSacd($id_nom);
                // iniciales
                $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
                $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
                $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
            } else {
                $PersonaEx = new PersonaEx($id_nom);
                $sacdEx = $PersonaEx->getNombreApellidos();
                // iniciales
                $nom = mb_substr($PersonaEx->getNom(), 0, 1);
                $ap1 = mb_substr($PersonaEx->getApellido1(), 0, 1);
                $ap2 = mb_substr($PersonaEx->getApellido2(), 0, 1);
            }
            $iniciales = strtoupper($nom . $ap1 . $ap2);
        } else {
            $iniciales = $InicialesSacd->getIniciales();
        }

        return $iniciales;
    }
}