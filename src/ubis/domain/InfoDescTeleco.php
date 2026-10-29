<?php

namespace src\ubis\domain;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\criteria\DescTelecoField;
use src\ubis\domain\entity\DescTeleco;

/* No vale el underscore en el nombre */

class InfoDescTeleco extends DatosInfoRepo
{
    public function __construct(
        private DescTelecoRepositoryInterface $descTelecoRepository,
    ) {
        $this->setTxtTitulo(_("descripciones de teleco"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta descripción?"));
        $this->setTxtBuscar(_("buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\DescTeleco');
        $this->setMetodoGestor('getDescTeleco');

        $this->setRepositoryInterface(DescTelecoRepositoryInterface::class);
    }

    /**
     * @return list<DescTeleco>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $criterio = Criteria::create()
                ->orderBy(DescTelecoField::Ubi)
                ->addOrderBy(DescTelecoField::Persona)
                ->addOrderBy(DescTelecoField::Orden);
        } else {
            // Antes buscaba en la columna inexistente `id_desc_teleco` (error SQL); se busca en la descripción.
            $criterio = Criteria::create()
                ->where(Filter::matchesUnaccented(DescTelecoField::DescTeleco, (string) $this->k_buscar));
        }

        return $this->descTelecoRepository->matching($criterio);
    }
}
