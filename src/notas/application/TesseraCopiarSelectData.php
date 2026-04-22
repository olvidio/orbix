<?php

namespace src\notas\application;

use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;

/**
 * Prepara los datos para elegir a que persona (con el mismo primer
 * apellido) se copiara la tessera de otra persona.
 *
 * Devuelve `['nom' => string, 'posibles_personas' => [id_nom => nombre]]`.
 * Lanza `RuntimeException` si no encuentra la persona origen ni como
 * numerario ni como agregado.
 */
final class TesseraCopiarSelectData
{
    public static function execute(int $id_nom): array
    {
        $repoN = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
        $oPersona = $repoN->findById($id_nom);
        $repo = $repoN;
        if ($oPersona === null) {
            $repoAgd = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
            $oPersona = $repoAgd->findById($id_nom);
            $repo = $repoAgd;
            if ($oPersona === null) {
                throw new \RuntimeException(sprintf(_("No existe una persona con id_nom: %s"), $id_nom));
            }
        }
        $apellido1 = $oPersona->getApellido1();
        $nom = $oPersona->getNombreApellidos();

        $cPersonas = $repo->getPersonas(['apellido1' => $apellido1]);
        $posibles = [];
        foreach ($cPersonas as $oPer) {
            $posibles[$oPer->getId_nom()] = $oPer->getNombreApellidos();
        }

        return [
            'nom' => $nom,
            'posibles_personas' => $posibles,
        ];
    }
}
