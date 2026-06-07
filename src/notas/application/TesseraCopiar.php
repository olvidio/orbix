<?php

namespace src\notas\application;

use function src\shared\domain\helpers\input_int;
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

    public function __construct(
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_nom_org = input_int($input, 'id_nom_org');
        $id_nom_dst = input_int($input, 'id_nom_dst');

        if ($id_nom_org === 0 || $id_nom_dst === 0) {
            return _("No se han recibido las personas de origen y destino");
        }

        $PersonaNotaRepository = $this->personaNotaRepository;
        $cPersonaOrgNotas = $PersonaNotaRepository->getPersonaNotas(['id_nom' => $id_nom_org]);

        $error = '';
        foreach ($cPersonaOrgNotas as $oPersonaNota) {
            $oNueva = clone $oPersonaNota;
            $oNueva->setId_nom($id_nom_dst);
            if ($PersonaNotaRepository->Guardar($oNueva) === false) {
                $error .= '<br>' . _("no se ha guardado la nota");
            }
        }

        return $error;
    }
}
