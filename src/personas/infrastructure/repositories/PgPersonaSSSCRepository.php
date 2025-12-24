<?php

namespace src\personas\infrastructure\repositories;

use core\ConfigGlobal;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaSSSC;
use src\shared\traits\HandlesPdoErrors;
use src\utils_database\domain\GenerateIdGlobal;


/**
 * Clase que adapta la tabla p_supernumerarios a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PgPersonaSSSCRepository extends PgPersonaDlRepository implements PersonaSSSCRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_supernumerarios');
    }

    /**
     * Busca la clase con id_nom en la base de datos .
     */
    public function findById(int $id_nom): ?PersonaSSSC
    {
        $aDatos = $this->datosById($id_nom);
        if (empty($aDatos)) {
            return null;
        }
        return (new PersonaSSSC())->setAllAttributes($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('p_supernumerarios_id_auto_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

    public function getNewIdNom($id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }
}