<?php

namespace src\zonassacd\domain;

use src\shared\domain\DatosInfoRepo;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\entity\ZonaGrupo;

/* No vale el underscore en el nombre */

class InfoZonaGrupo extends DatosInfoRepo
{
    public function __construct(
        private ZonaGrupoRepositoryInterface $zonaGrupoRepository,
    )
    {
        $this->setTxtTitulo(_("grupo de zonas de misas"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este grupo?"));
        $this->setTxtBuscar(_("grupo a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\zonassacd\\domain\\entity\\ZonaGrupo');
        $this->setMetodoGestor('getZonasGrupo');

        $this->setRepositoryInterface(ZonaGrupoRepositoryInterface::class);
    }

    /**
     * @return list<ZonaGrupo>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'orden'];
            $aOperador = [];
        } else {
            $aWhere = ['nombre_grupo' => $this->k_buscar];
            $aOperador = ['nombre_grupo' => 'sin_acentos'];
        }

        return $this->zonaGrupoRepository->getZonasGrupo($aWhere, $aOperador);
    }
}
