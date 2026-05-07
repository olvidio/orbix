<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\SincroDB;

class CrearTodosDesdeListasUseCase
{
    private IdMatchPersonaRepositoryInterface $idMatchRepository;
    private CrearPersonaDesdeListasUseCase $crearPersona;

    public function __construct(
        IdMatchPersonaRepositoryInterface $idMatchRepository,
        CrearPersonaDesdeListasUseCase    $crearPersona
    )
    {
        $this->idMatchRepository = $idMatchRepository;
        $this->crearPersona = $crearPersona;
    }

    /**
     * Crea todos los personas no vinculadas en Orbix desde la BDU.
     *
     * @return array{count: int, errors: string[]}
     */
    public function __invoke(string $region, string $dl, string $tipo_persona): array
    {
        $oSincroDB = new SincroDB();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl);

        $cPersonasListas = $oSincroDB->getPersonasBDU();

        $count = 0;
        $errors = [];
        foreach ($cPersonasListas as $oPersonaListas) {
            $id_nom_listas = $oPersonaListas->getIdentif();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_listas]);
            if (!empty($cIdMatch[0]) && count($cIdMatch) > 0) {
                continue;
            }

            $count++;
            $error = ($this->crearPersona)($id_nom_listas, $tipo_persona);
            if (!empty($error)) {
                $errors[] = $error;
            }
        }

        return ['count' => $count, 'errors' => $errors];
    }
}
