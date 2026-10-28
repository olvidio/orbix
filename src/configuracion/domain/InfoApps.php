<?php

namespace src\configuracion\domain;

/* No vale el underscore en el nombre */

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\criteria\AppField;
use src\configuracion\domain\entity\App;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
            $criteria = Criteria::create()->orderBy(AppField::Nom);
        } else {
            $criteria = Criteria::create()->where(Filter::matchesUnaccented(AppField::Nom, $this->k_buscar));
        }

        return $this->appRepository->matching($criteria);
    }
}
