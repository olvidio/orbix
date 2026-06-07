<?php

namespace src\ubiscamas\application;

use Psr\Container\ContainerInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\ubiscamas\domain\value_objects\CamaId;

final class UpdateCamaAsistente
{
    public function __construct(
        private AsistenteActividadService $asistenteActividadService,
        private ContainerInterface $container,
    ) {
    }

    /**
     * @return array{success: bool, mensaje: string}
     */
    public function execute(int $id_nom, int $id_activ, string $id_cama): array
    {
        $oAsistente = $this->asistenteActividadService->buscarAsistencia($id_nom, $id_activ);
        if ($oAsistente === false) {
            return [
                'success' => false,
                'mensaje' => "Asistencia no encontrada para id_nom $id_nom e id_activ $id_activ.",
            ];
        }

        $AsistenteRepositoryInterface = $this->asistenteActividadService->getRepoAsistente($id_nom, $id_activ);
        /** @var AsistenteRepositoryInterface $AsistenteRepository */
        $AsistenteRepository = $this->container->get($AsistenteRepositoryInterface);

        $uuid_cama = CamaId::fromNullableString($id_cama);
        $oAsistente->setCamaVo($uuid_cama);

        if ($AsistenteRepository->Guardar($oAsistente) === false) {
            return [
                'success' => false,
                'mensaje' => 'Error al guardar la asignación de la cama.',
            ];
        }

        return ['success' => true, 'mensaje' => 'ok'];
    }
}
