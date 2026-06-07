<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\application\support\SincroDBFactory;

class VerListasData
{
    public function __construct(
        private IdMatchPersonaRepositoryInterface $idMatchRepository,
        private SincroDBFactory $sincroDBFactory,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(string $region, string $dl, string $tipo_persona, bool $first_load): array
    {
        $oSincroDB = $this->sincroDBFactory->create();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl);

        $cPersonasBDU = $oSincroDB->getPersonasBDU();

        $cont_sync = 0;
        $a_lista = [];
        $i = 0;

        foreach ($cPersonasBDU as $oPersonaBDU) {
            $id_nom_bdu = $oPersonaBDU->getIdentif();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_bdu]);
            if ($cIdMatch !== []) {
                continue;
            }
            if ($first_load && $oSincroDB->union_automatico($oPersonaBDU)) {
                $cont_sync++;
                continue;
            }

            $i++;
            $a_lista[$i] = [
                'id_nom_listas' => $id_nom_bdu,
                'ape_nom' => $oPersonaBDU->getApenom(),
                'nombre' => $oPersonaBDU->getNombre(),
                'apellido1' => $oPersonaBDU->getApellido1(),
                'apellido1_sinprep' => $oPersonaBDU->getApellido1_sinprep(),
                'apellido2' => $oPersonaBDU->getApellido2(),
                'apellido2_sinprep' => $oPersonaBDU->getApellido2_sinprep(),
                'f_nacimiento' => $oPersonaBDU->getFecha_Naci(),
            ];
        }

        return [
            'lista' => $a_lista,
            'cont_sync' => $cont_sync,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getPosiblesMatches(string $tipo_persona, string $region, string $dl, int $id_nom_bdu): array
    {
        $oSincroDB = $this->sincroDBFactory->create();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl);

        $a_lista_orbix = $oSincroDB->posiblesOrbix($id_nom_bdu);
        $a_lista_orbix_otradl = [];
        if ($a_lista_orbix === []) {
            $a_lista_orbix_otradl = $oSincroDB->posiblesOrbixOtrasDl($id_nom_bdu);
        }

        return [
            'posibles_misma_dl' => $a_lista_orbix,
            'posibles_otra_dl' => $a_lista_orbix_otradl,
        ];
    }
}
