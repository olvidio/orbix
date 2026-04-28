<?php

namespace src\certificados\application;

use src\personas\domain\entity\Persona;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Datos para el formulario “adjuntar certificado emitido” (solo lectura inicial).
 */
final class CertificadoEmitidoAdjuntarFormData
{
    /**
     * @return array{nom: string, f_enviado: string}
     */
    public static function execute(int $id_nom): array
    {
        if ($id_nom <= 0) {
            throw new \RuntimeException(_('persona no válida'));
        }
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            throw new \RuntimeException(_('persona no encontrada'));
        }

        return [
            'nom' => $oPersona->getApellidosNombre(),
            'f_enviado' => (new DateTimeLocal())->getFromLocal(),
        ];
    }
}
