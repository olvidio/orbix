<?php

namespace src\personas\domain;

/* No vale el underscore en el nombre */

use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\Situacion;
use src\shared\domain\DatosInfoRepo;

class InfoSituacion extends DatosInfoRepo
{
    public function __construct(
        private SituacionRepositoryInterface $situacionRepository,
    ) {
        $this->setTxtTitulo(_("tipos de situación"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de situación?"));
        $this->setTxtBuscar(_("buscar un tipo de situación"));
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\Situacion');
        $this->setMetodoGestor('getSituaciones');

        $this->setRepositoryInterface(SituacionRepositoryInterface::class);
    }

    /**
     * @return list<Situacion>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'situacion'];
            $aOperador = [];
        } else {
            $aWhere = ['situacion' => $this->k_buscar];
            $aOperador = ['situacion' => 'sin_acentos'];
        }

        return $this->situacionRepository->getSituaciones($aWhere, $aOperador);
    }
}
