<?php

declare(strict_types=1);

namespace src\notas\application;

use src\personas\domain\entity\Persona;

/**
 * Dataset JSON para {@see frontend/notas/view/tesera_ver.phtml} (sin usar `Tesera` en frontend).
 */
final class TesseraVerData
{
    /**
     * @return array<string, mixed>
     */
    public static function execute(int $id_nom): array
    {
        if ($id_nom <= 0) {
            throw new \RuntimeException(_('persona no válida'));
        }
        if (Persona::findPersonaEnGlobal($id_nom) === null) {
            throw new \RuntimeException(sprintf(
                _('No encuentro persona con id_nom: %s'),
                (string)$id_nom
            ));
        }

        $tesera = new Tesera();
        return $tesera->datosParaVistaTesera($id_nom);
    }
}
