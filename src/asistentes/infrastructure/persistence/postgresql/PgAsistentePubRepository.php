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
        $where = '';
        if (!empty($aId_activ)) {
            $where = 'WHERE id_activ=';
            $where .= implode(' OR id_activ=', $aId_activ);
        }
        $sQuery = "SELECT DISTINCT id_nom from publicv.d_asistentes_de_paso $where";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsistentePub.lista.id_nom';
            /** @var \src\shared\infrastructure\logging\GestorErrores $oGestorErrores */
            $oGestorErrores = $_SESSION['oGestorErrores'];
            $oGestorErrores->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
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