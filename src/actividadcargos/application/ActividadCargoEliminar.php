<?php

namespace src\actividadcargos\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;

/**
 * Elimina un `ActividadCargo` y, si `elim_asis === 2` y el tipo de actividad
 * es `s`/`sg`, elimina tambien el `Asistente` y cierra los dossiers 1301/3101.
 *
 * Sustituye al case `eliminar` del antiguo `update_3102.php` dispatcher.
 */
final class ActividadCargoEliminar
{
    public function __construct(
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private ActividadRepositoryInterface $actividadRepository,
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
        $id_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_item');
        $elim_asis = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'elim_asis');

        $a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($input, 'sel');
        if ($id_item <= 0 && $a_sel !== []) {
            // formato "id_nom#id_item#elim_asis#id_schema"
            $tok = $a_sel[0];
            strtok($tok, '#');
            $id_item = (int) strtok('#');
            $elim_asis = (int) strtok('#');
        }

        if ($id_item <= 0) {
            return _("falta id_item");
        }

        $oActividadCargo = $this->actividadCargoRepository->findById($id_item);
        if ($oActividadCargo === null) {
            return _("no encuentro el cargo");
        }
        $id_activ = $oActividadCargo->getId_activ();
        $id_nom = $oActividadCargo->getId_nom();

        if ($this->actividadCargoRepository->Eliminar($oActividadCargo) === false) {
            return _("hay un error, no se ha eliminado");
        }

        $this->cerrarDossier('p', $id_nom, 1302);

        if ($elim_asis === 2 && $id_nom !== null && $id_activ > 0) {
            $error = $this->eliminarAsistenteSiProcede($id_activ, $id_nom);
            if ($error !== '') {
                return $error;
            }
        }

        return '';
    }

    private function eliminarAsistenteSiProcede(int $id_activ, int $id_nom): string
    {
        $oActividad = $this->actividadRepository->findById($id_activ);
        if ($oActividad === null) {
            return '';
        }
        $oTipoActiv = new TiposActividades($oActividad->getId_tipo_activ());
        $sasistentes = $oTipoActiv->getAsistentesText();
        if ($sasistentes !== 's' && $sasistentes !== 'sg') {
            return '';
        }

        $oAsistente = $this->findAsistente($id_activ, $id_nom);
        if ($oAsistente === null) {
            return '';
        }
        if (!\src\shared\domain\helpers\FuncTablasSupport::isTrue($oAsistente->perm_modificar())) {
            return '';
        }
        if ($this->asistenteApplicationService->eliminar($oAsistente) === false) {
            return _("hay un error, no se ha eliminado el asistente");
        }
        $this->cerrarDossier('p', $id_nom, 1301);
        return '';
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

    private function cerrarDossier(string $tabla, ?int $id_pau, int $id_tipo_dossier): void
    {
        if ($id_pau === null || $id_pau <= 0) {
            return;
        }
        $oDossier = $this->dossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => $tabla,
            'id_pau' => $id_pau,
            'id_tipo_dossier' => $id_tipo_dossier,
        ]));
        if ($oDossier === null) {
            return;
        }
        $oDossier->cerrar();
        $this->dossierRepository->Guardar($oDossier);
    }
}
