<?php

namespace src\actividadestudios\application;

use Psr\Container\ContainerInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Guarda el texto `observ` de un Asistente. Sustituye al case `observ`
 * de `update_3103.php`.
 */
final class AsistenteObserv
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
        $Qid_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_nom = FuncTablasSupport::inputInt($input, 'id_pau');
        if ($Qid_nom <= 0) {
            $Qid_nom = FuncTablasSupport::inputInt($input, 'id_nom');
        }
        $Qobserv = FuncTablasSupport::inputString($input, 'observ');

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
        $oAsistente->setObserv($Qobserv);
        if ($AsistenteRepository->Guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
