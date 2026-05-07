<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;

class VerOrbixOtraDlData
{
    private IdMatchPersonaRepositoryInterface $idMatchRepository;
    private PersonaBDURepositoryInterface $personaBDURepository;

    public function __construct(
        IdMatchPersonaRepositoryInterface $idMatchRepository,
        PersonaBDURepositoryInterface     $personaBDURepository
    )
    {
        $this->idMatchRepository = $idMatchRepository;
        $this->personaBDURepository = $personaBDURepository;
    }

    /**
     * Obtiene datos de personas BDU que están en otra DL en Orbix.
     *
     * @param array $a_ids_traslados_A Array de IDs de personas en listas
     * @return array Datos serializables
     */
    public function __invoke(string $tipo_persona, array $a_ids_traslados_A): array
    {
        $a_persona_listas = [];
        $i = 0;
        foreach ($a_ids_traslados_A as $id_nom_listas) {
            $i++;
            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_listas]);
            $id_nom_orbix = $cIdMatch[0]->getId_orbix();

            $oPersonaListas = $this->personaBDURepository->findById($id_nom_listas);

            $dl_listas = $oPersonaListas?->getDl() ?? '';
            preg_match('/(\w*)(cr)$/', $dl_listas, $matches);
            if (!empty($matches[2]) && $matches[2] === 'cr') {
                $dl = $matches[1];
            } else {
                $dl = "dl" . $dl_listas;
            }

            $a_persona_listas[$i] = [
                'id_nom_orbix' => $id_nom_orbix,
                'id_nom_listas' => $id_nom_listas,
                'ape_nom' => $oPersonaListas?->getApenom() ?? '',
                'dl' => $dl,
            ];
        }

        return ['personas' => $a_persona_listas];
    }
}
