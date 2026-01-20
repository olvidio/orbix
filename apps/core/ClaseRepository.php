<?php

namespace core;

use PDO;
use src\shared\domain\traits\Hydratable;

abstract class ClaseRepository
{
    use Hydratable;
    /**
     * oDbl de ClasePropiedades
     *
     * @var PDO
     */
    protected $oDbl;
    protected $oDbl_Select;
    /**
     * NomTabla de ClasePropiedades
     *
     * @var string
     */
    protected $sNomTabla = '';
    /**
     * ErrorTxt de ClasePropiedades
     *
     * @var string
     */
    protected $sErrorTxt;

    protected $iid_schema;

    /**
     *
     *
     * @param integer $iid_schema
     */
    public function setId_schema(int $iid_schema): void
    {
        $this->iid_schema = $iid_schema;
    }

    /**
     * Recupera el atributo oDbl de ClaseRepository
     *
     * @return PDO $oDbl
     */
    public function getoDbl(): PDO
    {
        return $this->oDbl;
    }

    public function getoDbl_Select(): PDO
    {
        return $this->oDbl_Select;
    }

    /**
     * Lo hago público para cuando se necesita copiar datos entre esquemas.
     *
     * @param PDO $oDbl
     */
    public function setoDbl(PDO $oDbl): void
    {
        $this->oDbl = $oDbl;
    }

    public function setoDbl_Select(PDO $oDbl_Select): void
    {
        $this->oDbl_Select = $oDbl_Select;
    }

    /**
     *
     * @return string $sNomTabla
     */
    public function getNomTabla(): string
    {
        return $this->sNomTabla;
    }

    /**
     * @param string $sNomTabla
     */
    public function setNomTabla(string $sNomTabla): void
    {
        $this->sNomTabla = $sNomTabla;
    }

    /**
     * sErrorTxt
     * @return string
     */
    public function getErrorTxt(): string
    {
        return $this->sErrorTxt;
    }

    /**
     * Recupera el atributo iid_schema
     *
     * @return integer $iid_schema
     */
    protected function getId_schema(): int
    {
        return $this->iid_schema;
    }

    /**
     * sErrorTxt
     * @param string $sErrorTxt
     * @return ClaseRepository
     */
    protected function setErrorTxt(string $sErrorTxt): ClaseRepository
    {
        $this->sErrorTxt = $sErrorTxt;
        return $this;
    }

    /**
     * Serveix per juntar en un conjunt una serie de col·leccions separades
     *
     * @param array $a_Clases nom de les classes
     * @param string $namespace nom del namespace
     * @param array $aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array $aOperators aOperators associate amb els valors dels operadors que cal aplicar a cada variable
     */
    public function getConjunt(array $a_Clases, string $namespace, array $aWhere, array $aOperators)
    {
        $cClassesTot = [];

        $paraOrdenar = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $paraOrdenar = $aWhere['_ordre'];
            unset($aWhere['_ordre']);
        }
        foreach ($a_Clases as $aClasse) {
            $repoInterface = $aClasse['repo'];
            $repoName = $repoInterface;
            $get = $aClasse['get'];

            $a_ord[$repoName] = [];
            $a_ord_cond[$repoName] = [];
            $Repository = $GLOBALS['container']->get($repoInterface);
            $cClasses = $Repository->$get($aWhere, $aOperators);
            if (is_array($cClasses)) {
                $cClassesTot = array_merge($cClassesTot, $cClasses);
            }
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
                    if (method_exists($oClass, $get)) {
                        $a_ord[$key_o][$key_c] = strtolower($oClass->$get() ?? '');
                        $a_ord_cond[$key_o] = SORT_ASC;
                        if (count($aa_ordre) > 1 && $aa_ordre[1] === 'DESC') {
                            $a_ord_cond[$key_o] = SORT_DESC;
                        }
                    }
                }
            }
            $multisort_args = [];
            foreach ($a_ordre as $key_o => $ordre) {
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