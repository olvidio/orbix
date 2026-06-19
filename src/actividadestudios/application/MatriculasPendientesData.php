<?php

namespace src\actividadestudios\application;

use RuntimeException;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\personas\application\services\PersonaFinderService;
use src\personas\application\services\PersonaListadoLookup;
use src\ubis\domain\RegionStgrAviso;
use function frontend\shared\helpers\is_true;

/**
 * Filas para `frontend/actividadestudios/controller/matriculas_pendientes.php`.
 *
 * Una fila por matrícula (asignatura pendiente de nota).
 *
 * @return array{msg_err: string, aviso: string, a_valores: array<int|string, array<string|int, mixed>>}
 */
final readonly class MatriculasPendientesData
{
    public function __construct(
        private MatriculaDlRepositoryInterface  $matriculaDlRepository,
        private AsignaturaRepositoryInterface   $asignaturaRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private PersonaFinderService            $personaFinderService,
    ) {
    }

    /**
     * @return array{msg_err: string, aviso: string, a_valores: array<int|string, array<string|int, mixed>>}
     */
    public function execute(): array
    {
        $cMatriculasPendientes = $this->matriculaDlRepository->getMatriculasPendientes();

        $msgErr = '';
        /** @var array<string, array<int|string, string>> $problemasRegionStgr */
        $problemasRegionStgr = [];
        $personaLookup = new PersonaListadoLookup($this->personaFinderService);

        $aValores = [];
        $aNombre = [];
        $i = 0;
        /** @var array<int, string> $actividadesCache */
        $actividadesCache = [];
        /** @var array<int, string> $asignaturasCache */
        $asignaturasCache = [];

        foreach ($cMatriculasPendientes as $oMatricula) {
            $i++;
            $idNom = $oMatricula->getId_nom();
            $idActiv = $oMatricula->getId_activ();
            $idAsignatura = $oMatricula->getIdAsignaturaVo()->value();
            $preceptorTxt = is_true($oMatricula->isPreceptor()) ? 'x' : '';

            $oPersona = $personaLookup->resolver($idNom, $msgErr, $problemasRegionStgr);
            if ($oPersona === null) {
                // borrar la matricula
                $this->matriculaDlRepository->Eliminar($oMatricula);
                continue;
            }

            $nomActiv = $this->nombreActividad($idActiv, $idNom, $actividadesCache, $personaLookup, $msgErr);
            if ($nomActiv === null) {
                continue;
            }

            $nombreCorto = $this->nombreAsignatura($idAsignatura, $asignaturasCache);
            if ($nombreCorto === null) {
                throw new RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), (string) $idAsignatura));
            }

            $apellidosNombre = $oPersona->getPrefApellidosNombre();
            $aValores[$i]['sel'] = "$idActiv#$idAsignatura#$idNom";
            $aValores[$i][1] = $nomActiv;
            $aValores[$i][2] = $nombreCorto;
            $aValores[$i][3] = $apellidosNombre;
            $aValores[$i][4] = $preceptorTxt;
            $aNombre[$i] = $apellidosNombre;
        }

        if ($aValores !== [] && $aNombre !== []) {
            array_multisort($aNombre, SORT_STRING, $aValores);
        }

        return [
            'msg_err' => $msgErr,
            'aviso' => RegionStgrAviso::formatear($problemasRegionStgr),
            'a_valores' => $aValores,
        ];
    }

    /**
     * @param array<int, string> $actividadesCache
     */
    private function nombreActividad(
        int $idActiv,
        int $idNom,
        array &$actividadesCache,
        PersonaListadoLookup $personaLookup,
        string &$msgErr,
    ): ?string {
        if (isset($actividadesCache[$idActiv])) {
            return $actividadesCache[$idActiv];
        }

        $oActividad = $this->actividadAllRepository->findById($idActiv);
        if ($oActividad === null) {
            $personaLookup->reportarErrorAlumno(
                $idNom,
                $msgErr,
                PersonaListadoLookup::mensajeActividadNoEncontrada($idActiv, $idNom),
            );

            return null;
        }

        $nomActiv = $oActividad->getNomActivVo()->value();
        $actividadesCache[$idActiv] = $nomActiv;

        return $nomActiv;
    }

    /**
     * @param array<int, string> $asignaturasCache
     */
    private function nombreAsignatura(int $idAsignatura, array &$asignaturasCache): ?string
    {
        if (isset($asignaturasCache[$idAsignatura])) {
            return $asignaturasCache[$idAsignatura];
        }

        $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
        if ($oAsignatura === null) {
            return null;
        }

        $nombreCorto = $oAsignatura->getNombre_corto();
        if ($nombreCorto === null) {
            return null;
        }
        $asignaturasCache[$idAsignatura] = $nombreCorto;

        return $nombreCorto;
    }
}
