<?php

namespace src\ubiscamas\application;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\infrastructure\ui\http\controllers\ListaHabitacionesAjax;
use web\Hash;

class HabitacioinesCamaLista
{

    public function __invoke($id_activ): array
    {
        $aWhere = [];
        $aOperador = [];

        // Instantiate repositories out of the container
        $actividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $asistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
        $habitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);
        $camaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);

        $controller = new ListaHabitacionesAjax(
            $actividadAllRepository,
            $asistenteActividadService,
            $habitacionRepository,
            $camaRepository
        );

        $data = $controller->fetch((int)$Qid_activ);

        $a_cabeceras = [_("grupo"),
            ['name' => 'accion', 'formatter' => 'clickFormatter']
        ];

        $a_botones[] = ['txt' => _("borrar"), 'click' => "fnjs_eliminar(this.form)"];

        $a_valores = [];
        $i = 0;
        foreach ($cGrupos as $oGrupo) {
            $i++;
            $id_usuario = $oGrupo->getId_usuario();
            $usuario = $oGrupo->getUsuarioAsString();

            $pagina = Hash::link(ConfigGlobal::getWeb() . '/frontend/usuarios/controller/grupo_form.php?'
                . http_build_query(array('quien' => 'grupo', 'id_usuario' => $id_usuario))
            );

            $a_valores[$i]['sel'] = "$id_usuario#";
            $a_valores[$i][1] = $usuario;
            $a_valores[$i][2] = array('ira' => $pagina, 'valor' => 'editar');
        }

        $data = ['a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];

        return $data;
    }
}