<?php

namespace src\misas\application\services;

use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaGlobal;
use src\personas\domain\entity\PersonaPub;

class InicialesSacdService
{
    private PersonaGlobal|PersonaPub|null $persona;

    public function __construct(
        private InicialesSacdRepositoryInterface $inicialesRepo,
        private PersonaSacdRepositoryInterface   $personaRepo
    )
    {
    }

    public function obtenerIniciales(int $id_nom): string
    {
        if ($id_nom <= 0) {
            return 'de paso';
        }

        $InicialesSacd = $this->inicialesRepo->findById($id_nom);
        if ($InicialesSacd === null) {
            if ($this->persona === null) {
                $this->persona = $this->personaRepo->findById($id_nom);
            }
            if ($this->persona === null) {
                return "no encuentro a nadie con id_nom: $id_nom";
            }

            // iniciales
            $nom = mb_substr($this->persona->getNom(), 0, 1);
            $ap1 = mb_substr($this->persona->getApellido1(), 0, 1);
            $a2 = $this->persona->getApellido2();
            if ($a2 === null) {
                $a2 = '';
            }
            $ap2 = mb_substr($a2, 0, 1);
            return strtoupper($nom . $ap1 . $ap2);
        }

        return $InicialesSacd->getIniciales()?? '---'; // Podría ser que se haya borrado en la DB.
    }

    public function obtenerNombreConIniciales(int $id_nom): string
    {
        if ($id_nom <= 0) {
            return '-?-';
        }

        $this->persona = $this->personaRepo->findById($id_nom);
        if ($this->persona === null) {
            return "no encuentro a nadie con id_nom: $id_nom";
        }

        return $this->persona->getNombreApellidos() . ' (' . $this->obtenerIniciales($id_nom) . ')';
    }
}
