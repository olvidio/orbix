<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorCongreso extends DatosInfoRepo
{

    public function __construct(
        private ProfesorCongresoRepositoryInterface $profesorCongresoRepository,
    ) {
        $this->setTxtTitulo(_("congresos a los que ha asistido una persona"));
        $this->setTxtEliminar();
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorCongreso');
        $this->setMetodoGestor('getProfesorCongresos');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorCongresoRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1024;
    }

    /**
     * @return list<object>
     */
    public function getColeccion(): array
    {
        $aWhere = [];
        $aOperador = [];
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'f_ini DESC';
            $aOperador = [];
        } else {
            $aWhere['congreso'] = $this->k_buscar;
            $aOperador['congreso'] = 'sin_acentos';
        }
        return $this->profesorCongresoRepository->getProfesorCongresos($aWhere, $aOperador);
    }
}