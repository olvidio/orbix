<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorDocenciaStgr extends DatosInfoRepo
{

    public function __construct(
        private ProfesorDocenciaStgrRepositoryInterface $profesorDocenciaStgrRepository,
    ) {
        $this->setTxtTitulo(_("dossier de actividad docente"));
        $this->setTxtEliminar();
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorDocenciaStgr');
        $this->setMetodoGestor('getProfesorDocenciasStgr');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorDocenciaStgrRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1025;
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
            $aWhere['_ordre'] = 'curso_inicio DESC';
            $aOperador = [];
        } else {
            //$aWhere['congreso'] = $this->k_buscar;
            //$aOperador['congreso'] ='sin_acentos';
        }
        return $this->profesorDocenciaStgrRepository->getProfesorDocenciasStgr($aWhere, $aOperador);
    }
}