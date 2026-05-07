<?php

namespace src\dbextern\application;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;

class VerDesaparecidosDeListasData
{
    private PersonaDlRepositoryInterface $personaDlRepository;

    public function __construct(PersonaDlRepositoryInterface $personaDlRepository)
    {
        $this->personaDlRepository = $personaDlRepository;
    }

    /**
     * Obtiene datos de personas de Orbix desaparecidas de la BDU.
     *
     * @param array $a_ids Array de IDs de personas Orbix
     * @return array Datos serializables
     */
    public function __invoke(string $tipo_persona, array $a_ids): array
    {
        $a_persona_orbix = [];
        $i = 0;
        foreach ($a_ids as $id_nom_orbix) {
            $i++;
            $oPersonaDl = $this->personaDlRepository->findById($id_nom_orbix);

            $a_persona_orbix[$i] = [
                'id_nom_orbix' => $id_nom_orbix,
                'ape_nom' => $oPersonaDl?->getPrefApellidosNombre() ?? '',
                'dl' => $oPersonaDl?->getDl() ?? '',
            ];
        }

        return ['personas' => $a_persona_orbix];
    }
}
