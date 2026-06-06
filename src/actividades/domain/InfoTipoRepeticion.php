<?php

namespace  src\actividades\domain;

/* No vale el underscore en el nombre */

use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoTipoRepeticion extends DatosInfoRepo
{
    public function __construct(
        private RepeticionRepositoryInterface $repeticionRepository,
    ) {
        $this->setTxtTitulo(_("tipo de repetición de actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de repetición?"));
        $this->setTxtBuscar(_("buscar un tipo de repetición"));
        $this->setTxtExplicacion();

        $this->setClase('src\\actividades\\domain\\entity\\Repeticion');
        $this->setMetodoGestor('getRepeticiones');

        $this->setRepositoryInterface(RepeticionRepositoryInterface::class);
    }

    /**
     * @return list<\src\actividades\domain\entity\Repeticion>
     */
    public function getColeccion(): array
    {
        $aWhere = [];
        $aOperador = [];
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $aWhere['repeticion'] = $this->k_buscar;
            $aOperador['repeticion'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'temporada, repeticion';
        $Coleccion = $this->repeticionRepository->getRepeticiones($aWhere, $aOperador);

        return $Coleccion;
    }
}
