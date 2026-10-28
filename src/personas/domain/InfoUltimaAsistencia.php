<?php

namespace src\personas\domain;

use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\UltimaAsistenciaField;
use src\personas\domain\contracts\UltimaAsistenciaRepositoryInterface;
use src\personas\domain\entity\UltimaAsistencia;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoUltimaAsistencia extends DatosInfoRepo
{
    public function __construct(
        private UltimaAsistenciaRepositoryInterface $ultimaAsistenciaRepository,
    ) {
        $this->setTxtTitulo(_("dossier de última aistencia a tipo de actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta actividad?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\UltimaAsistencia');
        $this->setMetodoGestor('getUltimasAsistencias');
        $this->setPau('p');

        $this->setRepositoryInterface(UltimaAsistenciaRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1006;
    }

    /**
     * @return list<UltimaAsistencia>
     */
    public function getColeccion(): array
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        $criteria = Criteria::create();
        if (!empty($this->id_pau)) {
            $criteria = $criteria->andWhere(Filter::eq(UltimaAsistenciaField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(UltimaAsistenciaField::FIni);
        }

        return $this->ultimaAsistenciaRepository->matching($criteria);
    }
}
