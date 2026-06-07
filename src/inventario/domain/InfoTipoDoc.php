<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\TipoDoc;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoTipoDoc extends DatosInfoRepo
{
    public function __construct(
        private TipoDocRepositoryInterface $tipoDocRepository,
    ) {
        $this->setTxtTitulo(_('tipo de documentos'));
        $this->setTxtEliminar(_('¿Está seguro que desea eliminar esta tipo de documento?'));
        $this->setTxtBuscar(_("buscar en 'detalle'"));
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\TipoDoc');
        $this->setMetodoGestor('getTipoDocs');
        $this->setPau('p');

        $this->setRepositoryInterface(TipoDocRepositoryInterface::class);
    }

    /**
     * @return list<TipoDoc>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nom_doc'];
            $aOperador = [];
        } else {
            $aWhere = ['nom_doc' => $this->k_buscar];
            $aOperador = ['nom_doc' => 'sin_acentos'];
        }

        return $this->tipoDocRepository->getTipoDocs($aWhere, $aOperador);
    }
}
