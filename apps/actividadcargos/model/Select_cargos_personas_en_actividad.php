<?php

namespace actividadcargos\model;

/**
 * Dossier codigo cargos_personas_en_actividad (id_tipo_dossier 1302); legacy: {@see Select1302}
 */
class Select_cargos_personas_en_actividad extends Select1302
{
    protected function selectTemplateFile(): string
    {
        return 'select_cargos_personas_en_actividad.phtml';
    }
}
