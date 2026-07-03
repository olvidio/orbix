<?php

namespace src\actividadplazas\application;

use src\actividades\domain\entity\TiposActividades;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Opciones del desplegable de delegaciones + `id_tipo_activ` resuelto para
 * {@see frontend/actividadplazas/controller/plazas_balance_que.php}.
 */
final class PlazasBalanceQueData
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input POST (id_tipo_activ, sasistentes, sactividad, …)
     * @return array{id_tipo_activ: string, delegaciones_opciones: array<string, string>}
     */
    public function execute(array $input): array
    {
        $idTipo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tipo_activ');
        if ($idTipo === '') {
            $ssfsv = '';
            $mi = (int)ConfigGlobal::mi_sfsv();
            if ($mi === 1) {
                $ssfsv = 'sv';
            }
            if ($mi === 2) {
                $ssfsv = 'sf';
            }
            $sa = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sasistentes');
            $sact = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sactividad');
            $oTipoActiv = new TiposActividades();
            $oTipoActiv->setSfsvText($ssfsv);
            $oTipoActiv->setAsistentesText($sa);
            $oTipoActiv->setActividadText($sact);
            $idTipo = (string)$oTipoActiv->getId_tipo_activ();
        }

        return [
            'id_tipo_activ' => $idTipo,
            'delegaciones_opciones' => $this->delegacionesActivasOrdenNombre(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function delegacionesActivasOrdenNombre(): array
    {
        $delegaciones = $this->delegacionRepository->getDelegaciones([
            'active' => true,
            '_ordre' => 'nombre_dl',
        ]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $opciones[$dl->getDlVo()->value()] = $dl->getNombreDlVo()?->value() ?? '';
        }

        return $opciones;
    }
}
