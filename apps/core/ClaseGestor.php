<?php
namespace core;

abstract class ClaseGestor
{
    /**
     * oDbl de Grupo
     *
     * @var object
     */
    protected $oDbl;
    protected $oDbl_Select;
    /**
     * NomTabla de Grupo
     *
     * @var string
     */
    protected $sNomTabla;
    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    public function getoDbl()
    {
        return $this->oDbl;
    }

    public function getoDbl_Select()
    {
        return $this->oDbl_Select;
    }


    /**
     * Establece el valor del atributo oDbl de Grupo
     *
     * @param object oDbl
     */
    public function setoDbl($oDbl)
    {
        $this->oDbl = $oDbl;
    }

    public function setoDbl_Select($oDbl_Select)
    {
        $this->oDbl_Select = $oDbl_Select;
    }

    /**
     * Recupera el atributo sNomTabla de Grupo
     *
     * @return string sNomTabla
     */
    public function getNomTabla()
    {
        return $this->sNomTabla;
    }

    /**
     * Establece el valor del atributo sNomTabla de Grupo
     *
     * @param string sNomTabla
     */
    protected function setNomTabla($sNomTabla)
    {
        $this->sNomTabla = $sNomTabla;
    }

    /* OTROS MÉTODOS --------------------------------------------------------*/

    /**
     * Serveix per juntar en un conjunt una serie de col·leccions separades
     *
     * @param array nom de les classes
     * @string namespace nom del namespace
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     */
    protected function getConjunt($a_Clases, $namespace, $aWhere, $aOperators)
    {
        $cClassesTot = [];
        $ord_Tot = [];

        $paraOrdenar = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') {
            $paraOrdenar = $aWhere['_ordre'];
            unset($aWhere['_ordre']);
        }
        foreach ($a_Clases as $aClasse) {
            $Classe = $aClasse['clase'];
            $get = $aClasse['get'];

            $a_ord[$Classe] = [];
            $a_ord_cond[$Classe] = [];
            $Gestor = $namespace . '\Gestor' . $Classe;
            $oGesClasse = new $Gestor;
            $cClasses = $oGesClasse->$get($aWhere, $aOperators);
            if (is_array($cClasses)) $cClassesTot = array_merge($cClassesTot, $cClasses);
        }

        //ordenar
        if (!empty($paraOrdenar)) {
            $a_ordre = explode(',', $paraOrdenar);
            foreach ($cClassesTot as $key_c => $oClass) {
                $get = '';
                foreach ($a_ordre as $key_o => $ordre) {
                    //comprobar que en $ordre está sólo el campo. Puede tener parametros: ASC, DESC
                    $aa_ordre = explode(' ', $ordre);
                    $ordreCamp = $aa_ordre[0];
                    $get = 'get' . ucfirst($ordreCamp);
                    $a_ord[$key_o][$key_c] = strtolower($oClass->$get() ?? '');
                    $a_ord_cond[$key_o] = SORT_ASC;
                    if (count($aa_ordre) > 1) {
                        if ($aa_ordre[1] === 'DESC') {
                            $a_ord_cond[$key_o] = SORT_DESC;
                        }
                    }
                }
            }
            $multisort_args = [];
            $ord = 0;
            foreach ($a_ordre as $key_o => $ordre) {
                $ord++;
                if (!empty($a_ord[$key_o])) {
                    $multisort_args[] = $a_ord[$key_o];
                    $multisort_args[] = $a_ord_cond[$key_o];
                    $multisort_args[] = SORT_STRING;
                }
            }
            $multisort_args[] = &$cClassesTot;   // finally add the source array, by reference
            call_user_func_array("array_multisort", $multisort_args);
        }
        return $cClassesTot;
    }

}
