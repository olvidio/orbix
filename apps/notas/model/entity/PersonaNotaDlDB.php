<?php

namespace notas\model\entity;

class PersonaNotaDlDB extends PersonaNotaDB
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    public function __construct(?array $a_id = NULL)
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_nom') && $val_id !== '') {
                    $this->iid_nom = (int)$val_id;
                }
                if (($nom_id === 'id_asignatura') && $val_id !== '') {
                    $this->iid_asignatura = (int)$val_id;
                }
                if (($nom_id === 'id_nivel') && $val_id !== '') {
                    $this->iid_nivel = (int)$val_id;
                }
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_dl');
    }

}
