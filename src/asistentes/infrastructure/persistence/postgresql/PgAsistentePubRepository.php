<?php

namespace src\asistentes\infrastructure\persistence\postgresql;

use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_asistentes_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgAsistentePubRepository extends PgAsistenteRepository implements AsistentePubRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(UnitOfWorkInterface $unitOfWork)
    {
        parent::__construct($unitOfWork);
        $oDbl = GlobalPdo::get('oDBEP');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBEP_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_asistentes_de_paso');
    }

    /**
     * @param list<int> $aId_activ
     * @return list<int>|false
     */
    public function getListaAsistentesDistintos(array $aId_activ = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $params = [];
        $where = '';
        if (!empty($aId_activ)) {
            $placeholders = [];
            foreach (array_values($aId_activ) as $i => $idActiv) {
                $placeholders[] = ":id_activ_$i";
                $params["id_activ_$i"] = (int) $idActiv;
            }
            $where = 'WHERE id_activ IN (' . implode(', ', $placeholders) . ')';
        }
        $sQuery = "SELECT DISTINCT id_nom FROM publicv.d_asistentes_de_paso $where";
        $oDblSt = $this->prepareAndExecute($oDbl, $sQuery, $params, 'GestorAsistentePub.lista.id_nom', __FILE__, __LINE__);
        if ($oDblSt === false) {
            return false;
        }
        $aId_nom = [];
        foreach ($oDblSt as $aDades) {
            if (!is_array($aDades) || !array_key_exists('id_nom', $aDades)) {
                continue;
            }
            $idNomRaw = $aDades['id_nom'];
            if (!is_numeric($idNomRaw)) {
                continue;
            }
            $aId_nom[] = (int) $idNomRaw;
        }
        return $aId_nom;
    }
}