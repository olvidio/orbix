<?php

namespace src\personas\application\services;

use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaPub;

/**
 * Resolución de persona en listados con caché y un solo aviso de error por alumno.
 *
 * Punto único para afinar la búsqueda cuando no se encuentra a la persona.
 */
final class PersonaListadoLookup
{
    public function __construct(
        private PersonaFinderService $personaFinderService,
    ) {
    }

    /** @var array<int, PersonaDl|PersonaPub|null> */
    private array $cache = [];

    /** @var array<int, true> */
    private array $erroresAlumno = [];

    /**
     * @param array<string, array<string, string>> $problemasRegionStgr
     */
    public function resolver(
        int $idNom,
        string &$msgErr,
        array &$problemasRegionStgr = [],
        string $etiqueta = '',
    ): PersonaDl|PersonaPub|null {
        if (!array_key_exists($idNom, $this->cache)) {
            $this->cache[$idNom] = $this->buscarPersona($idNom, $problemasRegionStgr);
        }

        $persona = $this->cache[$idNom];
        if ($persona === null) {
            $this->reportarErrorAlumno($idNom, $msgErr, self::mensajeNoEncontrada($idNom, $etiqueta));
        }

        return $persona;
    }

    public function reportarErrorAlumno(int $idNom, string &$msgErr, string $mensaje): void
    {
        if (isset($this->erroresAlumno[$idNom])) {
            return;
        }
        $this->erroresAlumno[$idNom] = true;
        $msgErr .= str_starts_with($mensaje, '<br>') ? $mensaje : '<br>' . $mensaje;
    }

    public function tieneErrorAlumno(int $idNom): bool
    {
        return isset($this->erroresAlumno[$idNom]);
    }

    public static function mensajeNoEncontrada(int $idNom, string $etiqueta = ''): string
    {
        $sufijo = $etiqueta !== '' ? " ($etiqueta)" : '';

        return sprintf(_('No encuentro a nadie con id_nom: %d%s'), $idNom, $sufijo);
    }

    public static function mensajeActividadNoEncontrada(int $idActiv, int $idNom): string
    {
        return sprintf(_('No encuentro ninguna actividad con id: %d (alumno id_nom: %d)'), $idActiv, $idNom);
    }

    /**
     * @param array<string, array<string, string>> $problemasRegionStgr
     */
    private function buscarPersona(int $idNom, array &$problemasRegionStgr): PersonaDl|PersonaPub|null
    {
        try {
            $marcaRegionStgr = false;

            return $this->personaFinderService->findPersonaParaListado(
                $idNom,
                $problemasRegionStgr,
                $marcaRegionStgr,
            );
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf(
                _('Error al validar nombre o apellidos de persona (id_nom=%1$d): %2$s'),
                $idNom,
                $e->getMessage(),
            ), 0, $e);
        }
    }
}
