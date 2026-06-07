<?php

namespace src\profesores\domain;

use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */
class InfoProfesorLatin extends DatosInfoRepo
{

    public function __construct(
        private ProfesorLatinRepositoryInterface $profesorLatinRepository,
    ) {
        $this->setTxtTitulo(_("dossier profesores de latín"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esto?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorLatin');
        $this->setMetodoGestor('getProfesoresLatin');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorLatinRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1022;
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
            //$aWhere['_ordre'] = 'f_ini DESC';
            $aOperador = [];
        } else {
            //$aWhere['congreso'] = $this->k_buscar;
            //$aOperador['congreso'] ='sin_acentos';
        }
        return $this->profesorLatinRepository->getProfesoresLatin($aWhere, $aOperador);
    }
}