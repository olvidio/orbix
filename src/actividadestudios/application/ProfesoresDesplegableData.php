<?php

namespace src\actividadestudios\application;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\profesores\domain\ProfesorActividad;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;

/**
 * Devuelve los datos (`id`, `opciones`, `selected`, `blanco`) para construir
 * un `<select>` de profesores en el form de `ActividadAsignatura`.
 */
final class ProfesoresDesplegableData
{
    public static function execute(array $input): array
    {
        $salida = (string) ($input['salida'] ?? '');
        $id_asignatura = (int) ($input['id_asignatura'] ?? 0);
        $id_activ = (int) ($input['id_activ'] ?? 0);
        $id_profesor = (int) ($input['id_profesor'] ?? 0);

        switch ($salida) {
            case 'asignatura':
                $ProfesorAsignaturaService = $GLOBALS['container']->get(ProfesorAsignaturaService::class);
                $aOpciones = $ProfesorAsignaturaService->getArrayTodosProfesoresAsignatura(new AsignaturaId($id_asignatura));
                break;
            case 'dl':
                $ProfesorActividad = new ProfesorActividad();
                $aOpciones = $ProfesorActividad->getArrayProfesoresActividad([$id_activ]);
                break;
            case 'todos':
                $ProfesorStgrService = $GLOBALS['container']->get(ProfesorStgrService::class);
                $aOpciones = $ProfesorStgrService->getArrayProfesoresPub();
                break;
            default:
                $aOpciones = [];
        }

        if ($id_profesor !== 0) {
            $aOpciones = self::conProfesorAsignadoSiFalta($aOpciones, $id_profesor);
        }

        return [
            'id' => 'id_profesor',
            // Lista [id, etiqueta]: en JSON un mapa se reordena por id en el navegador.
            'opciones' => self::opcionesEnOrden($aOpciones),
            'blanco' => true,
            'val_blanco' => '',
            'selected' => $id_profesor !== 0 ? $id_profesor : -1,
        ];
    }

    /**
     * @param array<int|string, string> $aOpciones
     * @return list<array{0: int|string, 1: string}>
     */
    private static function opcionesEnOrden(array $aOpciones): array
    {
        $ordenadas = [];
        foreach ($aOpciones as $id => $etiqueta) {
            $ordenadas[] = [(string) $id, $etiqueta];
        }

        return $ordenadas;
    }

    /**
     * @param array<int|string, string> $aOpciones
     * @return array<int|string, string>
     */
    public static function conProfesorAsignadoSiFalta(array $aOpciones, int $idProfesor): array
    {
        if (array_key_exists($idProfesor, $aOpciones)) {
            return $aOpciones;
        }

        $etiqueta = self::etiquetaProfesor($idProfesor);
        if ($etiqueta === null) {
            return $aOpciones;
        }

        if ($aOpciones === []) {
            return [$idProfesor => $etiqueta];
        }

        return [$idProfesor => $etiqueta] + [0 => '----------'] + $aOpciones;
    }

    private static function etiquetaProfesor(int $idProfesor): ?string
    {
        $personaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $oPersonaDl = $personaDlRepository->findById($idProfesor);
        if ($oPersonaDl !== null) {
            return $oPersonaDl->getPrefApellidosNombre();
        }

        $oPersona = Persona::findPersonaEnGlobal($idProfesor);

        return $oPersona?->getPrefApellidosNombre();
    }
}
