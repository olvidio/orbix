<?php

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use function DI\factory;

return [
    PermisosActividades::class => factory(function (
        UsuarioGrupoRepositoryInterface $usuarioGrupoRepository,
        ActividadAllRepositoryInterface $actividadAllRepository,
        ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
        TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        TareaProcesoRepositoryInterface $tareaProcesoRepository,
        int $idUsuario,
    ): PermisosActividades {
        return new PermisosActividades(
            $usuarioGrupoRepository,
            $actividadAllRepository,
            $actividadProcesoTareaRepository,
            $tipoDeActividadRepository,
            $tareaProcesoRepository,
            $idUsuario,
        );
    }),
];
