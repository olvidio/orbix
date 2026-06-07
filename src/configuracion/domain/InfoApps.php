<?php

namespace src\configuracion\domain;

/* No vale el underscore en el nombre */

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\entity\App;
use src\shared\domain\DatosInfoRepo;

class InfoApps extends DatosInfoRepo
{
    public function __construct(
        private AppRepositoryInterface $appRepository,
    ) {
        $this->setTxtTitulo(_("aplicaciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta aplicación?"));
        $this->setTxtBuscar(_("buscar una aplicación"));
        $this->setTxtExplicacion();

        $this->setClase('src\\configuracion\\domain\\entity\\App');
        $this->setMetodoGestor('getApps');

        $this->setRepositoryInterface(AppRepositoryInterface::class);
    }

    /**
     * @return list<App>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nom'];
            $aOperador = [];
        } else {
            $aWhere = ['nom' => $this->k_buscar];
            $aOperador = ['nom' => 'sin_acentos'];
        }

        return $this->appRepository->getApps($aWhere, $aOperador);
    }
}
