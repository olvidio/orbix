<?php

namespace actividadcargos\model;

/**
 * Dossier codigo cargos_de_actividad (id_tipo_dossier 3102); legacy: {@see Select3102}
 */
class Select_cargos_de_actividad extends Select3102
{
    protected function selectTemplateFile(): string
    {
        return 'select_cargos_de_actividad.phtml';
    }
}
