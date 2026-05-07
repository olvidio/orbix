<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\entity\IdMatchPersona;

class DesunirPersonaUseCase
{
    private IdMatchPersonaRepositoryInterface $idMatchRepository;

    public function __construct(IdMatchPersonaRepositoryInterface $idMatchRepository)
    {
        $this->idMatchRepository = $idMatchRepository;
    }

    public function __invoke(int $id_nom_listas, string $tipo_persona): string
    {
        $oIdMatch = $this->idMatchRepository->findById($id_nom_listas);
        if ($oIdMatch === null) {
            return _("no se encontró el registro a desunir");
        }

        if ($this->idMatchRepository->Eliminar($oIdMatch) === false) {
            return _("hay un error, no se ha eliminado") . "\n" . $this->idMatchRepository->getErrorTxt();
        }

        return '';
    }
}
