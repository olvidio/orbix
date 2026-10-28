<?php

namespace src\personas\domain;

use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\TrasladoField;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\entity\Traslado;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoTraslado extends DatosInfoRepo
{
    public function __construct(
        private TrasladoRepositoryInterface $trasladoRepository,
    ) {
        $this->setTxtTitulo(_("dossier de traslados"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este traslado?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\Traslado');
        $this->setMetodoGestor('getTraslados');
        $this->setPau('p');

        $this->setRepositoryInterface(TrasladoRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1004;
    }

    /**
     * @return list<Traslado>
     */
    public function getColeccion(): array
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        $criteria = Criteria::create();
        if (!empty($this->id_pau)) {
            $criteria = $criteria->andWhere(Filter::eq(TrasladoField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(TrasladoField::FTraslado);
        }

        return $this->trasladoRepository->matching($criteria);
    }
}
