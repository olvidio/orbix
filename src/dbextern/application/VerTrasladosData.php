<?php

namespace src\dbextern\application;

use src\dbextern\application\support\SincroDBFactory;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;

class VerTrasladosData
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private PersonaDlRepositoryFactoryInterface $personaDlRepositoryFactory,
        private SincroDBFactory $sincroDBFactory,
    ) {
    }

    /**
     * @param list<int> $a_ids_traslados
     * @return array<string, mixed>
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
        if ($obj_pau === '') {
            return ['error' => _("No existe la clase de la persona")];
        }

        try {
            $this->personaRepositoryResolver->repositorio($obj_pau);
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }

        $oSincroDB = $this->sincroDBFactory->create();
        $oSincroDB->setTipo_persona($tipo_persona);

        $a_persona_orbix = [];
        $i = 0;
        foreach ($a_ids_traslados as $id_nom_orbix) {
            $i++;
            $idInt = (int)$id_nom_orbix;
            $dl_orbix = $oSincroDB->buscarEnOrbix($idInt);
            $a_reg_dl = explode('-', $dl_orbix);
            $dl_actual = substr($a_reg_dl[1] ?? '', 0, -1);

            $oDB = $oSincroDB->conexion($dl_orbix);
            $repoPersonaDl = $this->personaDlRepositoryFactory->createWithConnection($oDB);
            $oPersonaOrbix = $repoPersonaDl->findById($idInt);

            $a_persona_orbix[$i] = [
                'id_nom_orbix' => $id_nom_orbix,
                'ape_nom' => $oPersonaOrbix?->getPrefApellidosNombre() ?? '',
                'dl' => $oPersonaOrbix?->getDl() ?? '',
                'dl_actual' => $dl_actual,
            ];

            $oSincroDB->restaurarConexion($oDB);
        }

        return ['personas' => $a_persona_orbix];
    }
}
