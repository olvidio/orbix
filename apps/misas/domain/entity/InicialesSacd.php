<?php

namespace misas\domain\entity;


use misas\domain\repositories\InicialesSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;

class InicialesSacd extends InicialesSacdDB
{

    public function iniciales($id_nom): string
    {
        $InicialesSacdRepository = $GLOBALS['container']->get(InicialesSacdRepositoryInterface::class);
        $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
        if ($InicialesSacd === null) {
//            if ($id_nom > 0) {
                $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
                $PersonaSacd = $PersonaSacdRepository->findById($id_nom);
                // iniciales
                $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
                $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
                $a2=$PersonaSacd->getApellido2();
                if ($a2 == null)
                    $a2='';
                $ap2 = mb_substr($a2, 0, 1);
//            } else {
//                $PersonaEx = new PersonaEx($id_nom);
//                $sacdEx = $PersonaEx->getNombreApellidos();
//                // iniciales
//                $nom = mb_substr($PersonaEx->getNom(), 0, 1);
//                $ap1 = mb_substr($PersonaEx->getApellido1(), 0, 1);
//                $ap2 = mb_substr($PersonaEx->getApellido2(), 0, 1);
//            }
            $iniciales = strtoupper($nom . $ap1 . $ap2);
        } else {
            $iniciales = $InicialesSacd->getIniciales();
        }
        if ($id_nom==0)
        {
            $iniciales=' -- ';
        }

        return $iniciales;
    }
    public function nombre_sacd($id_nom): string
    {
        $nombre_sacd='';
        if ($id_nom>0) {
            $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
            $PersonaSacd = $PersonaSacdRepository->findById($id_nom);
            $nombre_sacd = $PersonaSacd->getNombreApellidos().' ('.$this->iniciales($id_nom).')';
        }
//        if ($id_nom<0) {
//            $PersonaEx = new PersonaEx($id_nom);
//            $nombre_sacd = $PersonaEx->getNombreApellidos().' ('.$this->iniciales($id_nom).')';
//        }
        return $nombre_sacd;
    }
}