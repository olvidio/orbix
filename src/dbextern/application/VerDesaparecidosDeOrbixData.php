<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;

class VerDesaparecidosDeOrbixData
{
    private PersonaBDURepositoryInterface $personaBDURepository;

    public function __construct(PersonaBDURepositoryInterface $personaBDURepository)
    {
        $this->personaBDURepository = $personaBDURepository;
    }

    /**
     * Obtiene datos de personas BDU desaparecidas de Orbix.
     *
     * @param array $a_ids Array de IDs de personas en listas (BDU)
     * @return array Datos serializables
     */
    public function __invoke(string $tipo_persona, array $a_ids): array
    {
        $a_persona_listas = [];
        $i = 0;
        foreach ($a_ids as $id_nom_listas) {
            $i++;
            $oPersonaListas = $this->personaBDURepository->findById($id_nom_listas);

            $a_persona_listas[$i] = [
                'id_nom_listas' => $id_nom_listas,
                'ape_nom' => $oPersonaListas?->getApenom() ?? '',
                'dl' => $oPersonaListas?->getDl() ?? '',
            ];
        }

        return ['personas' => $a_persona_listas];
    }
}
