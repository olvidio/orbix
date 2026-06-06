<?php

namespace src\actividadestudios\application;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\profesores\domain\ProfesorActividad;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Devuelve los datos (`id`, `opciones`, `selected`, `blanco`) para construir
 * un `<select>` de profesores en el form de `ActividadAsignatura`.
 */
final class ProfesoresDesplegableData
{
    public function __construct(
        private ProfesorAsignaturaService $profesorAsignaturaService,
        private ProfesorStgrService $profesorStgrService,
        private PersonaDlRepositoryInterface $personaDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: int|string, 1: string}>, blanco: bool, val_blanco: string, selected: int}
     */
    public function execute(array $input): array
    {
        $salida = input_string($input, 'salida');
        $id_asignatura = input_int($input, 'id_asignatura');
        $id_activ = input_int($input, 'id_activ');
        $id_profesor = input_int($input, 'id_profesor');

        switch ($salida) {
            case 'asignatura':
                $aOpciones = $this->profesorAsignaturaService->getArrayTodosProfesoresAsignatura(new AsignaturaId($id_asignatura));
                break;
            case 'dl':
                $ProfesorActividad = new ProfesorActividad();
                $aOpciones = $ProfesorActividad->getArrayProfesoresActividad([$id_activ]);
                break;
            case 'todos':
                $aOpciones = $this->profesorStgrService->getArrayProfesoresPub();
                break;
            default:
                $aOpciones = [];
        }

        if ($id_profesor !== 0) {
            $aOpciones = $this->conProfesorAsignadoSiFalta($aOpciones, $id_profesor);
        }

        return [
            'id' => 'id_profesor',
            'opciones' => $this->opcionesEnOrden($aOpciones),
            'blanco' => true,
            'val_blanco' => '',
            'selected' => $id_profesor !== 0 ? $id_profesor : -1,
        ];
    }

    /**
     * @param array<int|string, string> $aOpciones
     * @return list<array{0: int|string, 1: string}>
     */
    private function opcionesEnOrden(array $aOpciones): array
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
    public function conProfesorAsignadoSiFalta(array $aOpciones, int $idProfesor): array
    {
        if (array_key_exists($idProfesor, $aOpciones)) {
            return $aOpciones;
        }

        $etiqueta = $this->etiquetaProfesor($idProfesor);
        if ($etiqueta === null) {
            return $aOpciones;
        }

        if ($aOpciones === []) {
            return [$idProfesor => $etiqueta];
        }

        return [$idProfesor => $etiqueta] + [0 => '----------'] + $aOpciones;
    }

    private function etiquetaProfesor(int $idProfesor): ?string
    {
        $oPersonaDl = $this->personaDlRepository->findById($idProfesor);
        if ($oPersonaDl !== null) {
            return $oPersonaDl->getPrefApellidosNombre();
        }

        $oPersona = Persona::findPersonaEnGlobal($idProfesor);

        return $oPersona?->getPrefApellidosNombre();
    }
}
