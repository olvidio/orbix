<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\SincroDB;

class VerOrbixData
{
    private IdMatchPersonaRepositoryInterface $idMatchRepository;

    public function __construct(IdMatchPersonaRepositoryInterface $idMatchRepository)
    {
        $this->idMatchRepository = $idMatchRepository;
    }

    /**
     * Obtiene la lista de personas Orbix sin unir a la BDU.
     *
     * @return array Datos serializables
     */
    public function __invoke(string $region, string $tipo_persona): array
    {
        $obj_pau = match ($tipo_persona) {
            'n' => 'GestorPersonaN',
            'a' => 'GestorPersonaAgd',
            's' => 'GestorPersonaS',
            'sssc' => 'GestorPersonaSSSC',
            default => '',
        };
        if (empty($obj_pau)) {
            return ['lista' => []];
        }

        $obj = 'personas\\model\\entity\\' . $obj_pau;
        $GesPersonas = new $obj();
        $cPersonasOrbix = $GesPersonas->getPersonas(['situacion' => 'A', '_ordre' => 'apellido1,apellido2,nom']);

        $a_lista = [];
        $i = 0;
        foreach ($cPersonasOrbix as $oPersonaOrbix) {
            $id_nom_orbix = $oPersonaOrbix->getId_nom();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_orbix' => $id_nom_orbix]);
            if (!empty($cIdMatch[0]) && !empty($cIdMatch)) {
                continue;
            }
            $i++;
            $a_lista[$i] = [
                'id_nom_orbix' => $id_nom_orbix,
                'ape_nom' => $oPersonaOrbix->getPrefApellidosNombre(),
                'nombre' => $oPersonaOrbix->getNom(),
                'apellido1' => $oPersonaOrbix->getApellido1(),
                'nx1' => $oPersonaOrbix->getNx1(),
                'apellido2' => $oPersonaOrbix->getApellido2(),
                'nx2' => $oPersonaOrbix->getNx2(),
                'f_nacimiento' => $oPersonaOrbix->getF_nacimiento()?->getFromLocal(),
            ];
        }

        return ['lista' => $a_lista];
    }

    /**
     * Obtiene los posibles matches BDU para una persona Orbix.
     */
    public function getPosiblesMatches(string $tipo_persona, string $region, string $dl, int $id_nom_orbix): array
    {
        $oSincroDB = new SincroDB();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl);

        return $oSincroDB->posiblesBDU($id_nom_orbix);
    }
}
