<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\application\support\SincroDBFactory;
use src\personas\application\support\PersonaRepositoryResolver;

class VerOrbixData
{
    public function __construct(
        private IdMatchPersonaRepositoryInterface $idMatchRepository,
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private SincroDBFactory $sincroDBFactory,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(string $region, string $tipo_persona): array
    {
        $obj_pau = match ($tipo_persona) {
            'n' => 'PersonaN',
            'a' => 'PersonaAgd',
            's' => 'PersonaS',
            'sssc' => 'PersonaSSSC',
            default => '',
        };
        if ($obj_pau === '') {
            return ['lista' => []];
        }

        try {
            $repoPersona = $this->personaRepositoryResolver->repositorio($obj_pau);
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }
        $cPersonasOrbix = $repoPersona->getPersonas(['situacion' => 'A', '_ordre' => 'apellido1,apellido2,nom']);

        $a_lista = [];
        $i = 0;
        foreach ($cPersonasOrbix as $oPersonaOrbix) {
            $id_nom_orbix = $oPersonaOrbix->getId_nom();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_orbix' => $id_nom_orbix]);
            if ($cIdMatch !== []) {
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
     * @return list<array<string, mixed>>
     */
    public function getPosiblesMatches(string $tipo_persona, string $region, string $dl, int $id_nom_orbix): array
    {
        $oSincroDB = $this->sincroDBFactory->create();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl);

        return $oSincroDB->posiblesBDU($id_nom_orbix);
    }
}
