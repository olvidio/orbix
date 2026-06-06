<?php

namespace src\actividadescentro\domain;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class Info3010 extends DatosInfoRepo
{
    public function __construct(
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
    ) {
        $this->setTxtTitulo(_("centros encargados de la actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este centro?"));
        $this->setTxtBuscar(_("buscar un centro"));
        $this->setTxtExplicacion();

        $this->setClase('src\\actividadescentro\\domain\\entity\\CentroEncargado');
        $this->setMetodoGestor('getCentrosEncargados');
        $this->setRepositoryInterface(CentroEncargadoRepositoryInterface::class);
        $this->setPau('a');
    }

    public function getId_dossier(): int
    {
        return 3010;
    }

    /**
     * @return list<CentroEncargado>
     */
    public function getColeccion(): array
    {
        return $this->centroEncargadoRepository->getCentrosEncargados([], []);
    }
}
