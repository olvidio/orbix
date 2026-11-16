<?php

namespace src\actividadescentro\domain;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\criteria\CentroEncargadoField;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
        // Solo consulta: los centros se asignan, quitan y ordenan en su pantalla (`CentrosEncargadosData`).
        // La edición genérica de tablas no vale aquí: la clave son dos ids ajenos (actividad y centro).
        $this->setSoloLectura(true);
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
        // Dentro del dossier de una actividad (`id_pau`), solo sus centros, por orden.
        $criteria = Criteria::create();
        if (!empty($this->id_pau)) {
            $criteria = $criteria->where(Filter::eq(CentroEncargadoField::IdActiv, (int) $this->id_pau));
        }

        return $this->centroEncargadoRepository->matching($criteria->orderBy(CentroEncargadoField::NumOrden));
    }
}
