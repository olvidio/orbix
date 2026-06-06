<?php

namespace src\actividadestudios\application;

use Psr\Container\ContainerInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Marca el flag `est_ok` (plan de estudios confirmado) de un Asistente.
 * Sustituye al case `plan` de `update_3103.php`.
 */
final class AsistentePlanEstOk
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
        $Qid_activ = input_int($input, 'id_activ');
        $Qid_nom = input_int($input, 'id_pau');
        if ($Qid_nom <= 0) {
            $Qid_nom = input_int($input, 'id_nom');
        }
        $Qest_ok = input_string($input, 'est_ok');

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
        $oAsistente->setEst_ok(is_true($Qest_ok));
        if ($AsistenteRepository->Guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
