<?php

namespace src\actividadcargos\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Alta de un `ActividadCargo`. Si llega `asis=true` ademas da de alta
 * al `Asistente` asociado (flujo tipico `nuevo` desde el form 3102/1302).
 *
 * Replica la logica del case `nuevo` del antiguo `update_3102.php`:
 * abrir dossiers `1302`/`3102` (cargos) y, cuando proceda, `1301`/`3101`
 * (asistentes).
 */
final class ActividadCargoNuevo
{
    public function __construct(
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private AsistenteApplicationService $asistenteApplicationService,
        private AsistenteDlRepositoryInterface $asistenteDlRepository,
        private AsistenteOutRepositoryInterface $asistenteOutRepository,
        private AsistenteExRepositoryInterface $asistenteExRepository,
        private AsistentePubRepositoryInterface $asistentePubRepository,
        private DossierRepositoryInterface $dossierRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_activ = input_int($input, 'id_activ');
        $id_nom = input_int($input, 'id_nom');
        $id_cargo = input_int($input, 'id_cargo');
        $observ = input_string($input, 'observ');
        $puede_agd = input_string($input, 'puede_agd');
        $asis = input_string($input, 'asis');

        if ($id_activ <= 0 || $id_nom <= 0 || $id_cargo <= 0) {
            return _("faltan parametros id_activ / id_nom / id_cargo");
        }

        $oActividadCargo = new ActividadCargo();
        $oActividadCargo->setId_item($this->actividadCargoRepository->getNewId());
        $oActividadCargo->setId_activ($id_activ);
        $oActividadCargo->setId_cargo($id_cargo);
        $oActividadCargo->setId_nom($id_nom);
        $oActividadCargo->setObserv($observ !== '' ? $observ : null);
        $oActividadCargo->setPuede_agd(is_true($puede_agd));

        if ($this->actividadCargoRepository->Guardar($oActividadCargo) === false) {
            $error = $this->lastSessionError();
            if ($error !== '' && strpos($error, 'duplicate key') !== false) {
                return _("ya existe este cargo para esta actividad");
            }
            return $error !== '' ? $error : _("hay un error, no se ha guardado");
        }

        $this->abrirDossier('p', $id_nom, 1302);
        $this->abrirDossier('a', $id_activ, 3102);

        if ($asis !== '') {
            $error = $this->crearAsistente($id_activ, $id_nom);
            if ($error !== '') {
                return $error;
            }
            $this->abrirDossier('p', $id_nom, 1301);
            $this->abrirDossier('a', $id_activ, 3101);
        }

        return '';
    }

    private function crearAsistente(int $id_activ, int $id_nom): string
    {
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if (!is_object($oPersona)) {
            return sprintf(_("no encuentro a nadie con id_nom: %d"), $id_nom);
        }

        $oAsistente = $this->findAsistente($id_activ, $id_nom);
        if ($oAsistente === null) {
            $oAsistente = new Asistente();
            $oAsistente->setId_activ($id_activ);
            $oAsistente->setId_nom($id_nom);
            $oAsistente->setEst_ok(false);
            $oAsistente->setCfi(false);
        }
        $oAsistente->setPropio(true);
        $oAsistente->setFalta(false);
        $oAsistente->setDl_responsable(ConfigGlobal::mi_delef());

        if ($this->asistenteApplicationService->guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado el asistente");
        }

        return '';
    }

    private function abrirDossier(string $tabla, int $id_pau, int $id_tipo_dossier): void
    {
        $pk = DossierPk::fromArray([
            'tabla' => $tabla,
            'id_pau' => $id_pau,
            'id_tipo_dossier' => $id_tipo_dossier,
        ]);
        $oDossier = $this->dossierRepository->findByPk($pk);
        if ($oDossier === null) {
            $oDossier = $this->dossierRepository->crearDossier($pk);
        }
        $oDossier->abrir();
        $this->dossierRepository->Guardar($oDossier);
    }

    private function findAsistente(int $id_activ, int $id_nom): ?Asistente
    {
        foreach (
            [
                $this->asistenteDlRepository,
                $this->asistenteOutRepository,
                $this->asistenteExRepository,
                $this->asistentePubRepository,
            ] as $repo
        ) {
            $found = $repo->findById($id_activ, $id_nom);
            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    private function lastSessionError(): string
    {
        $errores = $_SESSION['errores'] ?? null;
        if (!is_array($errores)) {
            return '';
        }
        $error = end($errores);

        return is_string($error) ? $error : '';
    }
}
