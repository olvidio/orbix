<?php

namespace src\notas\application;

use src\notas\domain\contracts\PersonaNotaRepositoryInterface;

/**
 * Copia todas las `PersonaNota` de una persona origen hacia una persona
 * destino. Utilizado por `personas_select.phtml` (pagina de traslado
 * de tessera entre numerarios / supernumerarios).
 *
 * Devuelve una cadena con los errores (separados por `<br>`) o vacia
 * si todo ha ido bien.
 */
final class TesseraCopiar
{
    public static function execute(array $input): string
    {
        $id_nom_org = (int)($input['id_nom_org'] ?? 0);
        $id_nom_dst = (int)($input['id_nom_dst'] ?? 0);

        if ($id_nom_org === 0 || $id_nom_dst === 0) {
            return _("No se han recibido las personas de origen y destino");
        }

        $PersonaNotaRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $cPersonaOrgNotas = $PersonaNotaRepository->getPersonaNotas(['id_nom' => $id_nom_org]);

        $error = '';
        foreach ($cPersonaOrgNotas as $oPersonaNota) {
            $oNueva = clone $oPersonaNota;
            $oNueva->setId_nom($id_nom_dst);
            if ($oNueva->DBGuardar() === false) {
                $error .= '<br>' . _("no se ha guardado la nota");
            }
        }

        return $error;
    }
}
