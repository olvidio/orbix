<?php

namespace src\profesores\domain;

/* No vale el underscore en el nombre */

use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorPublicacion extends DatosInfoRepo
{

    public function __construct(
        private ProfesorPublicacionRepositoryInterface $profesorPublicacionRepository,
    ) {
        $this->setTxtTitulo(_("dossier de publicaciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta publicación?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorPublicacion');
        $this->setMetodoGestor('getProfesorPublicaciones');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorPublicacionRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1012;
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
            $aWhere['_ordre'] = 'f_publicacion DESC';
            $aOperador = [];
        } else {
            $aWhere['titulo'] = $this->k_buscar;
            $aOperador['titulo'] = 'sin_acentos';
        }
        return $this->profesorPublicacionRepository->getProfesorPublicaciones($aWhere, $aOperador);
    }
}