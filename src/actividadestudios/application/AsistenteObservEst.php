<?php

namespace src\actividadestudios\application;

use Psr\Container\ContainerInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;

/**
 * Guarda el texto `observ_est` de un Asistente (persona en una actividad
 * de estudios). Sustituye al case `observ_est` de `update_3103.php`.
 */
final class AsistenteObservEst
{
    public function __construct(
        private ContainerInterface $container,
        private AsistenteActividadService $asistenteActividadService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
        if ($Qid_nom <= 0) {
            $Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        }
        $Qobserv_est = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ_est');

        if ($Qid_activ <= 0 || $Qid_nom <= 0) {
            return _("falta id_activ o id_nom");
        }

        $AsistenteRepositoryInterface = $this->asistenteActividadService->getRepoAsistente($Qid_nom, $Qid_activ);
        /** @var AsistenteRepositoryInterface $AsistenteRepository */
        $AsistenteRepository = $this->container->get($AsistenteRepositoryInterface);
        $oAsistente = $AsistenteRepository->findById($Qid_activ, $Qid_nom);
        if ($oAsistente === null) {
            return _("no encuentro al asistente");
        }
        $oAsistente->setObserv_est($Qobserv_est);
        if ($AsistenteRepository->Guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
