<?php

namespace src\dbextern\application;

use src\dbextern\domain\SincroDB;
use src\personas\application\support\PersonaRepositoryResolver;

class VerTrasladosData
{
    /**
     * Obtiene datos de personas a trasladar desde otras DL.
     *
     * @param array $a_ids_traslados Array de IDs de personas Orbix a trasladar
     * @return array Datos serializables
     */
    public function __invoke(string $tipo_persona, array $a_ids_traslados): array
    {
        $obj_pau = match ($tipo_persona) {
            'n' => 'PersonaN',
            'a' => 'PersonaAgd',
            's' => 'PersonaS',
            'sssc' => 'PersonaSSSC',
            default => '',
        };

        $resolver = new PersonaRepositoryResolver();
        try {
            $repoPersona = $resolver->repositorio($obj_pau);
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }

        $oSincroDB = new SincroDB();
        $oSincroDB->setTipo_persona($tipo_persona);

        $a_persona_orbix = [];
        $i = 0;
        foreach ($a_ids_traslados as $id_nom_orbix) {
            $i++;
            $dl_orbix = $oSincroDB->buscarEnOrbix($id_nom_orbix);
            $a_reg_dl = explode('-', $dl_orbix);
            $dl_actual = substr($a_reg_dl[1], 0, -1);

            $oDB = $oSincroDB->conexion($dl_orbix);
            $repoPersona->setoDbl($oDB);
            $oPersonaOrbix = $repoPersona->findById($id_nom_orbix);

            $a_persona_orbix[$i] = [
                'id_nom_orbix' => $id_nom_orbix,
                'ape_nom' => $oPersonaOrbix?->getPrefApellidosNombre() ?? '',
                'dl' => $oPersonaOrbix?->getDl() ?? '',
                'dl_actual' => $dl_actual,
            ];

            $oSincroDB->restaurarConexion($oDB);
            $repoPersona->setoDbl($GLOBALS['oDB']);
        }

        return ['personas' => $a_persona_orbix];
    }
}
