<?php

namespace src\personas\domain;

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
        $aWhere = [];
        $aOperador = [];
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'f_traslado';
        }

        return $this->trasladoRepository->getTraslados($aWhere, $aOperador);
    }
}
