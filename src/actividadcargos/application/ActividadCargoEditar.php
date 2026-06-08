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
use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Edicion de un `ActividadCargo` existente (o alta si `id_item` viene vacio,
 * preservando la semantica del legacy `update_3102` caso `editar`).
 *
 * Si `asis` cambia respecto al estado actual del `Asistente`, lo crea o lo
 * elimina, y actualiza los dossiers 1301/3101 correspondientes.
 *
 * El parametro `asis_presente` debe valer `'1'` cuando el form incluye el
 * input `asis` (visible solo en `nuevo` sin id_nom_real). Sustituye el
 * antiguo `isset($_POST['asis'])` del dispatcher.
 */
final class ActividadCargoEditar
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
        $id_item = input_int($input, 'id_item');
        $id_activ = input_int($input, 'id_activ');
        $id_nom = input_int($input, 'id_nom');
        $id_cargo = input_int($input, 'id_cargo');
        $observ = input_string($input, 'observ');
        $puede_agd = input_string($input, 'puede_agd');
        $asis = input_string($input, 'asis');
        $asis_presente = input_string($input, 'asis_presente') !== '';

        if ($id_activ <= 0 || $id_nom <= 0 || $id_cargo <= 0) {
            return _("faltan parametros id_activ / id_nom / id_cargo");
        }

        if ($id_item <= 0) {
            $oActividadCargo = new ActividadCargo();
            $oActividadCargo->setId_item($this->actividadCargoRepository->getNewId());
        } else {
            $oActividadCargo = $this->actividadCargoRepository->findById($id_item);
            if ($oActividadCargo === null) {
                return _("no encuentro el cargo");
            }
        }

        $oActividadCargo->setId_activ($id_activ);
        $oActividadCargo->setId_cargo($id_cargo);
        $oActividadCargo->setId_nom($id_nom);
        $oActividadCargo->setObserv($observ !== '' ? $observ : null);
        $oActividadCargo->setPuede_agd(is_true($puede_agd) ?? false);

        if ($this->actividadCargoRepository->Guardar($oActividadCargo) === false) {
            $error = $this->lastSessionError();
            if ($error !== '' && strpos($error, 'duplicate key') !== false) {
                return _("ya existe este cargo para esta actividad");
            }
            return _("hay un error, no se ha guardado");
        }

        return $this->sincronizarAsistente($id_activ, $id_nom, $asis, $asis_presente);
    }

    private function sincronizarAsistente(int $id_activ, int $id_nom, string $asis, bool $asis_presente): string
    {
        $oAsistente = $this->findAsistente($id_activ, $id_nom);

        if ($oAsistente === null) {
            if ($asis === '') {
                return '';
            }
            $oAsistente = new Asistente();
            $oAsistente->setId_activ($id_activ);
            $oAsistente->setId_nom($id_nom);
            $oAsistente->setPropio(true);
            $oAsistente->setFalta(false);
            $oAsistente->setDl_responsable(ConfigGlobal::mi_delef());
            if ($this->asistenteApplicationService->guardar($oAsistente) === false) {
                return _("hay un error, no se ha guardado el asistente");
            }
            $this->abrirDossier('p', $id_nom, 1301);
            $this->abrirDossier('a', $id_activ, 3101);
            return '';
        }

        if ($asis_presente && $asis === '') {
            if ($this->asistenteApplicationService->eliminar($oAsistente) === false) {
                return _("hay un error, no se ha eliminado el asistente");
            }
            $this->abrirDossier('p', $id_nom, 1301);
            $this->abrirDossier('a', $id_activ, 3101);
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
