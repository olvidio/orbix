<?php

namespace src\misas\application;

use core\ConfigGlobal;
use function core\strtoupper_dlb;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\PeriodoQue;

/**
 * Formulario "Cambiar estado del plan de misas" (zona, estado, orden, periodo).
 */
class CambiarStatusPantallaData
{
    /**
     * @return array{
     *   zonas_opciones: array<int|string, string>,
     *   orden_opciones: array<string, string>,
     *   estados_opciones: array<int, string>,
     *   periodo_td_html: string
     * }
     */
    public static function getData(): array
    {
        $container = $GLOBALS['container'];

        $UsuarioRepository = $container->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();

        $RoleRepository = $container->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $id_nom_jefe = null;
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
            if (!$_SESSION['oConfig']->is_jefeCalendario()) {
                $id_nom_jefe = (int)$oMiUsuario->getCsvIdPauAsString();
                if ($id_nom_jefe === 0) {
                    exit(_('No tiene permiso para ver esta página'));
                }
            }
        }

        $ZonaRepository = $container->get(ZonaRepositoryInterface::class);
        $zonas = $ZonaRepository->getArrayZonas($id_nom_jefe);

        $orden = [
            'orden' => 'orden',
            'prioridad' => 'prioridad',
            'desc_enc' => 'alfabético',
        ];

        $estados = [
            EncargoDiaStatus::STATUS_PROPUESTA => 'propuesta',
            EncargoDiaStatus::STATUS_COMUNICADO_SACD => 'comunicado sacerdotes',
            EncargoDiaStatus::STATUS_COMUNICADO_CTR => 'comunicado centros',
        ];

        $aOpcionesPeriodo = [
            'proxima_semana' => _('próxima semana de lunes a domingo'),
            'proximo_mes' => _('próximo mes natural'),
            'otro' => _('otro'),
        ];

        $oFormP = new PeriodoQue();
        $oFormP->setFormName('frm_nuevo_periodo');
        $oFormP->setTitulo(strtoupper_dlb(_('seleccionar un periodo')));
        $oFormP->setPosiblesPeriodos($aOpcionesPeriodo);
        $oFormP->setDesplPeriodosOpcion_sel('proxima_semana');
        $oFormP->setisDesplAnysVisible(false);

        $ohoy = new DateTimeLocal(date('Y-m-d'));
        $shoy = $ohoy->format('d/m/Y');
        $oFormP->setEmpiezaMin($shoy);
        $oFormP->setEmpiezaMax($shoy);

        return [
            'zonas_opciones' => $zonas,
            'orden_opciones' => $orden,
            'estados_opciones' => $estados,
            'periodo_td_html' => $oFormP->getTd(),
        ];
    }
}
