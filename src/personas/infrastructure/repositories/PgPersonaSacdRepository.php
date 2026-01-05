<?php

namespace src\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaPub;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla p_numerarios a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PgPersonaSacdRepository extends PgPersonaPubRepository implements PersonaSacdRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('cp_sacd');
    }


    public function getSacdsBySelect(int $Qseleccion_sacd): array
    {
        // selecciono según la variable selecion_sacd ('2'=> n y agd, '4'=> de paso, '8'=> sssc, '16'=>cp)
        $a_tipos_tablas = [];
        if (empty($Qseleccion_sacd) || ($Qseleccion_sacd & 2)) {
            $a_tipos_tablas[] = 'n';
            $a_tipos_tablas[] = 'a';
        }
        if ($Qseleccion_sacd & 4) {
            $a_tipos_tablas[] = 'pn';
            $a_tipos_tablas[] = 'pa';
        }
        if ($Qseleccion_sacd & 8) {
            $a_tipos_tablas[] = 'sssc';
        }

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tabla'] = "'". implode("','", $a_tipos_tablas) ."'";
        $aOperador['id_tabla'] = 'IN';
        $aWhere['sacd'] = 't';
        $aWhere['situacion'] = 'A';
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';
        $cPersonas = $this->getPersonas($aWhere, $aOperador);

        return $cPersonas   ;
    }

    public function getArraySacdyCheckBox(int $Qseleccion_sacd): array
    {
        /* lista sacd posibles */
        // selecciono según la variable selecion_sacd ('2'=> n y agd, '4'=> de paso, '8'=> sssc, '16'=>cp)
        $chk_prelatura = '';
        $chk_de_paso = '';
        $chk_sssc = '';
        $a_tipos_tablas = [];
        if (empty($Qseleccion_sacd) || ($Qseleccion_sacd & 2)) {
            $a_tipos_tablas[] = 'n';
            $a_tipos_tablas[] = 'a';
            $chk_prelatura = 'checked';
        }
        if ($Qseleccion_sacd & 4) {
            $a_tipos_tablas[] = 'pn';
            $a_tipos_tablas[] = 'pa';
            $chk_de_paso = 'checked';
        }
        if ($Qseleccion_sacd & 8) {
            $a_tipos_tablas[] = 'sssc';
            $chk_sssc = 'checked';
        }

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tabla'] = "'". implode("','", $a_tipos_tablas) ."'";
        $aOperador['id_tabla'] = 'IN';
        $aWhere['sacd'] = 't';
        $aWhere['situacion'] = 'A';
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';
        $cPersonas = $this->getPersonas($aWhere, $aOperador);
        $aOpciones = [];
        foreach ($cPersonas as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $apellidos_nombre = $oPersona->getApellidosNombre();
            $aOpciones[$id_nom] = $apellidos_nombre;
        }

        return array($chk_prelatura, $chk_de_paso, $chk_sssc, $aOpciones);
    }


    /**
     * Busca la clase con id_nom en la base de datos .
     */
    public function findById(int $id_nom): ?PersonaEx
    {
        $aDatos = $this->datosById($id_nom);
        if (empty($aDatos)) {
            return null;
        }
        return PersonaEx::fromArray($aDatos);
    }

}