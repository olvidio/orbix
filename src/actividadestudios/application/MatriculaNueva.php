<?php

namespace src\actividadestudios\application;

use src\actividadestudios\application\support\AsignaturaNombreDlPrefix;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;

/**
 * Crea una matricula (asignatura de una persona en una actividad) y
 * ajusta los dossiers 1303 (persona) y 3103 (actividad) + la asignatura
 * impartida (`ActividadAsignatura`).
 *
 * Desde **añadir asignatura** (catálogo del plan): si ya está en el CA pide
 * confirmación y, al continuar, crea una nueva oferta en esta dl.
 * Desde **matricular en una asignatura** (`modo=matricular_ca`) no avisa:
 * solo matricula en una oferta que ya existe.
 *
 * Sustituye al case `nuevo` de `update_3103.php`.
 */
final class MatriculaNueva
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
        private ActividadAsignaturaRepositoryInterface $actividadAsignaturaRepository,
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
        private DossierRepositoryInterface $dossierRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string, requiere_confirmacion: bool, mensaje: string}
     */
    public function execute(array $input): array
    {
        $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
        if ($Qid_nom <= 0) {
            $Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        }
        $Qid_asignatura = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_asignatura');
        if ($Qid_asignatura <= 0) {
            $Qid_asignatura = AsignaturaNombreDlPrefix::idAsignaturaDeOpcion(
                \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_asignatura'),
            );
        }
        $Qid_nivel = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nivel');
        $Qid_situacion = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_situacion');
        $Qpreceptor = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'preceptor');
        $Qid_preceptor = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_preceptor');
        $modo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'modo');
        $matricularEnCa = $modo === FormMatriculasDeUnaPersonaData::MODO_MATRICULAR_CA;
        $confirmar = \src\shared\domain\helpers\FuncTablasSupport::isTrue($input['confirmar_duplicado'] ?? false) === true;

        if ($Qid_activ <= 0 || $Qid_nom <= 0) {
            return self::resultado(_("falta id_activ o id_nom"));
        }

        if ($Qid_asignatura === 1) {
            $cAsignaturas = $this->asignaturaRepository->getAsignaturas(['id_nivel' => $Qid_nivel]);
            if (empty($cAsignaturas)) {
                return self::resultado(_("no encuentro asignatura para ese nivel"));
            }
            $Qid_asignatura = $cAsignaturas[0]->getId_asignatura();
        }

        if ($Qid_asignatura <= 0) {
            return self::resultado(_("debe poner una asignatura"));
        }

        if ($Qid_nivel <= 0) {
            $oAsignaturaNivel = $this->asignaturaRepository->findById($Qid_asignatura);
            if ($oAsignaturaNivel === null) {
                return self::resultado(sprintf(_("No se ha encontrado la asignatura con id: %s"), (string) $Qid_asignatura));
            }
            $Qid_nivel = $oAsignaturaNivel->getId_nivel();
        }

        if (!$matricularEnCa && !$confirmar) {
            $existentes = $this->actividadAsignaturaRepository->getActividadAsignaturas([
                'id_activ' => $Qid_activ,
                'id_asignatura' => $Qid_asignatura,
            ]);
            if ($existentes !== []) {
                return [
                    'error' => '',
                    'requiere_confirmacion' => true,
                    'mensaje' => ActividadAsignaturaNueva::mensajeDuplicado(),
                ];
            }
        }

        $oMatricula = $this->matriculaDlRepository->findById($Qid_activ, $Qid_asignatura, $Qid_nom);
        if ($oMatricula === null) {
            $oMatricula = new Matricula();
            $oMatricula->setId_activ($Qid_activ);
            $oMatricula->setId_asignatura($Qid_asignatura);
            $oMatricula->setId_nom($Qid_nom);
        }
        $oMatricula->setId_nivel($Qid_nivel);
        $oMatricula->setId_situacion($Qid_situacion);
        $oMatricula->setPreceptor($Qpreceptor !== '' && $Qpreceptor !== 'f' ? true : null);
        $oMatricula->setId_preceptor($Qid_preceptor);
        if ($this->matriculaDlRepository->Guardar($oMatricula) === false) {
            return self::resultado(_("hay un error, no se ha guardado"));
        }

        $oDossier = $this->dossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1303,
        ]));
        if ($oDossier === null) {
            $oDossier = $this->dossierRepository->crearDossier(DossierPk::fromArray([
                'tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1303,
            ]));
        }
        $oDossier->abrir();
        $this->dossierRepository->Guardar($oDossier);

        $oDossierActiv = $this->dossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3103,
        ]));
        if ($oDossierActiv !== null) {
            $oDossierActiv->cerrar();
            $this->dossierRepository->Guardar($oDossierActiv);
        }

        $cActividadAsignaturas = $this->actividadAsignaturaDlRepository->getActividadAsignaturas(
            ['id_activ' => $Qid_activ, 'id_asignatura' => $Qid_asignatura]
        );
        if ($matricularEnCa) {
            return self::resultado('');
        }
        if (count($cActividadAsignaturas) === 0 || $confirmar) {
            $oActividadAsignatura = new ActividadAsignatura();
            $oActividadAsignatura->setId_activ($Qid_activ);
            $oActividadAsignatura->setId_asignatura($Qid_asignatura);
            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpreceptor)) {
                $oActividadAsignatura->setId_profesor($Qid_preceptor);
                $tipo = 'p';
            } else {
                $tipo = '';
            }
            $oActividadAsignatura->setTipo($tipo);
            $this->actividadAsignaturaDlRepository->Guardar($oActividadAsignatura);
        }

        return self::resultado('');
    }

    /**
     * @return array{error: string, requiere_confirmacion: bool, mensaje: string}
     */
    private static function resultado(string $error): array
    {
        return [
            'error' => $error,
            'requiere_confirmacion' => false,
            'mensaje' => '',
        ];
    }
}
