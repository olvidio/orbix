<?php

declare(strict_types=1);

namespace src\notas\application\support;

/**
 * Única pregunta que {@see PersonaNotaInputParser} necesita hacer sobre las siglas de acta:
 * si la sigla puede aparecer en un acta de esta DL (la propia y las delegaciones sin esquema
 * Orbix; el resto va como certificado).
 *
 * La implementación real es {@see ActaPersonaFormListas}, que resuelve las listas con
 * `ConfigGlobal` + `ConfigDB`. Este contrato deja el seam para probar el parser sin sesión
 * ni ficheros de configuración.
 */
interface SiglaActaPermitida
{
    public function siglaPermitidaEnActa(string $sigla): bool;
}
