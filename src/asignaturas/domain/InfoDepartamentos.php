<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Departamento;
use src\shared\domain\DatosInfoRepo;

class InfoDepartamentos extends DatosInfoRepo
{
    public function __construct(
        private DepartamentoRepositoryInterface $departamentoRepository,
    ) {
        $this->setTxtTitulo(_("departamentos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este departamento?"));
        $this->setTxtBuscar(_("buscar un departamento"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Departamento');
        $this->setMetodoGestor('getDepartamentos');

        $this->setRepositoryInterface(DepartamentoRepositoryInterface::class);
    }

    /**
     * @return list<Departamento>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'departamento'];
            $aOperador = [];
        } else {
            $aWhere = ['departamento' => $this->k_buscar];
            $aOperador = ['departamento' => 'sin_acentos'];
        }

        return $this->departamentoRepository->getDepartamentos($aWhere, $aOperador);
    }
}
