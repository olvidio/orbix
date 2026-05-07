<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\entity\IdMatchPersona;

class UnirPersonaUseCase
{
    private IdMatchPersonaRepositoryInterface $idMatchRepository;

    public function __construct(IdMatchPersonaRepositoryInterface $idMatchRepository)
    {
        $this->idMatchRepository = $idMatchRepository;
    }

    /**
     * Vincula una persona de BDU con una persona de Orbix.
     *
     * @return string Error text (empty on success)
     */
    public function __invoke(int $id_nom_listas, int $id_orbix, string $tipo_persona): string
    {
        $oIdMatch = new IdMatchPersona();
        $oIdMatch->setId_listas($id_nom_listas);
        $oIdMatch->setId_orbix($id_orbix);
        $oIdMatch->setId_tabla($tipo_persona);

        if ($this->idMatchRepository->Guardar($oIdMatch) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $this->idMatchRepository->getErrorTxt();
        }

        return '';
    }
}
