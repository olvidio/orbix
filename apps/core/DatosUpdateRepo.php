<?php

namespace core;

use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Classe que implementa
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/04/2018
 */
class DatosUpdateRepo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private $oFicha;
    private $Campos;
    private mixed $repository;

    public function eliminar()
    {
        $oFicha = $this->getFicha();
        $oRepository = new $this->repository();
        if ($oRepository->Eliminar($oFicha) === FALSE) {
            $error_txt = $oRepository->getErrorTxt();
            return $error_txt;
        }
        return true;
    }

    public function nuevo()
    {
        $aCampos = $this->getCampos();
        $oFicha = $this->getFicha();
        foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
            $nom_camp = $oDatosCampo->getNom_camp();
            // si es un checkbox y está vacío, no pasa nada
            $tipo = $oDatosCampo->getTipo();
            if ($tipo === 'check') {
                if (empty($aCampos[$nom_camp])) {
                    $aCampos[$nom_camp] = false;
                } else {
                    $aCampos[$nom_camp] = is_true($aCampos[$nom_camp]);
                }
            }
            // si es con decimales, cambio coma por punto
            if ($tipo === 'decimal' && !empty($aCampos[$nom_camp])) $aCampos[$nom_camp] = str_replace(',', '.', $aCampos[$nom_camp]);
            //$oFicha->$nom_camp = $aCampos[$nom_camp];

            $metodo = $oDatosCampo->getMetodoSet();
            $oFicha->$metodo($aCampos[$nom_camp]);
        }

        $oRepository = new $this->repository();
        $new_id = $oRepository->getNewID();
        $pks1 = 'set'.ucfirst($oFicha->getPrimary_key());
        $oFicha->$pks1($new_id);

        if ($oRepository->Guardar($oFicha) === FALSE) {
            $error_txt = $oRepository->getErrorTxt();
            return $error_txt;
        }
        return true;
    }

    public function editar()
    {
        $aCampos = $this->getCampos();
        $oFicha = $this->getFicha();
        foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
            $nom_camp = $oDatosCampo->getNom_camp();
            // si es un checkbox y está vacío, no pasa nada
            $tipo = $oDatosCampo->getTipo();
            if ($tipo === 'check') {
                if (empty($aCampos[$nom_camp])) {
                    $aCampos[$nom_camp] = false;
                } else {
                    $aCampos[$nom_camp] = is_true($aCampos[$nom_camp]);
                }
            }
            if ($tipo === 'fecha') {
                if (empty($aCampos[$nom_camp])) {
                    $aCampos[$nom_camp] = new NullDateTimeLocal();
                } else {
                    $aCampos[$nom_camp] = DateTimeLocal::createFromLocal($aCampos[$nom_camp]);
                }
            }
            // si es con decimales, cambio coma por punto
            if ($tipo === 'decimal' && !empty($aCampos[$nom_camp])) {
                $aCampos[$nom_camp] = str_replace(',', '.', $aCampos[$nom_camp]);
            }

            $metodo = $oDatosCampo->getMetodoSet();
            // cambiar las cadenas vacías por null (va bien cuando el dato que se espera en un integer)
            $aCampos[$nom_camp] = empty($aCampos[$nom_camp])? null: $aCampos[$nom_camp];
            $oFicha->$metodo($aCampos[$nom_camp]);
        }

        $oRepository = new $this->repository();
        if ($oRepository->Guardar($oFicha) === FALSE) {
            $error_txt = $oRepository->getErrorTxt();
            return $error_txt;
        }
        return true;
    }

    public function getFicha()
    {
        return $this->oFicha;
    }

    public function getCampos()
    {
        return $this->Campos;
    }

    public function setFicha($oFicha)
    {
        $this->oFicha = $oFicha;
    }

    public function setCampos($Campos)
    {
        $this->Campos = $Campos;
    }

    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
}