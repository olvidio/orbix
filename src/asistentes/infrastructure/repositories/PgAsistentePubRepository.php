<?php

namespace src\asistentes\infrastructure\repositories;

use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\shared\domain\contracts\EventBusInterface;
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

    public function __construct(EventBusInterface $eventBus)
    {
        parent::__construct($eventBus);
        $oDbl = $GLOBALS['oDBEP'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBEP_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_asistentes_de_paso');
    }

    function getListaAsistentesDistintos($aId_activ = array())
    {
        $oDbl = $this->getoDbl_Select();
        $where = '';
        if (!empty($aId_activ)) {
            $where = 'WHERE id_activ=';
            $where .= implode(' OR id_activ=', $aId_activ);
        }
        $sQuery = "SELECT DISTINCT id_nom from publicv.d_asistentes_de_paso $where";
        //echo "qq: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsistentePub.lista.id_nom';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aId_nom = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $aId_nom[] = $aDades['id_nom'];
        }
        return $aId_nom;
    }
}