<?php

namespace src\usuarios\domain;

use src\shared\domain\DatosInfoRepo;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\entity\Local;

/* No vale el underscore en el nombre */

class InfoLocales extends DatosInfoRepo
{
    public function __construct(
        private LocalRepositoryInterface $localRepository,
    ) {
        $this->setTxtTitulo(_('idiomas posibles para la aplicación'));
        $this->setTxtEliminar();
        $this->setTxtBuscar(_('idioma a buscar'));
        $this->setTxtExplicacion();

        $this->setClase('src\\usuarios\\domain\\entity\\Local');
        $this->setMetodoGestor('getLocales');

        $this->setRepositoryInterface(LocalRepositoryInterface::class);
    }

    /**
     * @return list<Local>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = [];
            $aOperador = [];
        } else {
            $aWhere = ['nom_idioma' => $this->k_buscar];
            $aOperador = ['nom_idioma' => 'sin_acentos'];
        }
        $aWhere['_ordre'] = 'active DESC,nom_idioma ASC';

        return $this->localRepository->getLocales($aWhere, $aOperador);
    }
}
