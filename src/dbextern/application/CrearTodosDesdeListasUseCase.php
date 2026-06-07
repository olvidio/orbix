<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\application\support\SincroDBFactory;

class CrearTodosDesdeListasUseCase
{
    public function __construct(
        private IdMatchPersonaRepositoryInterface $idMatchRepository,
        private CrearPersonaDesdeListasUseCase $crearPersona,
        private SincroDBFactory $sincroDBFactory,
    ) {
    }

    /**
     * @return array{count: int, errors: list<string>}
     */
    public function __invoke(string $region, string $dl, string $tipo_persona): array
    {
        $oSincroDB = $this->sincroDBFactory->create();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl);

        $cPersonasListas = $oSincroDB->getPersonasBDU();

        $count = 0;
        $errors = [];
        foreach ($cPersonasListas as $oPersonaListas) {
            $id_nom_listas = $oPersonaListas->getIdentif();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_listas]);
            if ($cIdMatch !== []) {
                continue;
            }

            $count++;
            $error = ($this->crearPersona)($id_nom_listas, $tipo_persona);
            if ($error !== '') {
                $errors[] = $error;
            }
        }

        return ['count' => $count, 'errors' => $errors];
    }
}
