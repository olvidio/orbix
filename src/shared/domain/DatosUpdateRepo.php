<?php

namespace src\shared\domain;

use src\profesores\domain\entity\ProfesorLatin;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

class DatosUpdateRepo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private $oFicha;
    private $Campos;
    private mixed $RepositoryInterface;

    public function eliminar()
    {
        $oFicha = $this->getFicha();
        $oRepository = $GLOBALS['container']->get($this->RepositoryInterface);
        if ($oRepository->Eliminar($oFicha) === false) {
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
            if ($tipo === 'fecha') {
                if (empty($aCampos[$nom_camp])) {
                    //$aCampos[$nom_camp] = new NullDateTimeLocal();
                    $aCampos[$nom_camp] = null;
                } else {
                    $aCampos[$nom_camp] = DateTimeLocal::createFromLocal($aCampos[$nom_camp]);
                }
            }
            // si es con decimales, cambio coma por punto
            if ($tipo === 'decimal' && !empty($aCampos[$nom_camp])) $aCampos[$nom_camp] = str_replace(',', '.', $aCampos[$nom_camp]);

            if (empty($aCampos[$nom_camp])) { // En general mejor null, por el tipado. puede venir '' para un integer
                $aCampos[$nom_camp] = null;
            }

            if ($nom_camp === 'id_nom' || $nom_camp === 'id_ubi' || $nom_camp === 'id_activ') {
                $aCampos[$nom_camp] = $this->Campos['id_pau'];
            }

            $metodo = $oDatosCampo->getMetodoSet();
            // uso el método legacy
            if (substr($metodo, -2) === 'Vo') {
                $metodo = substr($metodo, 0, -2);
            }
            try {
                $oFicha->$metodo($aCampos[$nom_camp]);
            } catch (\Throwable $e) {
                return 'Error al ejecutar ' . $metodo . ' para el campo ' . $nom_camp . ': ' . $e->getMessage();
            }
        }

        $oRepository = $GLOBALS['container']->get($this->RepositoryInterface);
        // Casos especiales que no tienen getNewId
        $NoNewId = false;
        if ($oFicha instanceof ProfesorLatin) {
            $new_id = $this->Campos['id_pau'];
            $NoNewId = true;
        }

        if ($NoNewId === false) {
            $new_id = $oRepository->getNewId();
        }

        $pks1 = 'set' . ucfirst($oFicha->getPrimary_key());
        $oFicha->$pks1($new_id);

        try {
            $oRepository->Guardar($oFicha);
        } catch (\Throwable $e) {
            return 'Error al guardar: ' . $e->getMessage();
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
                    //$aCampos[$nom_camp] = new NullDateTimeLocal();
                    $aCampos[$nom_camp] = null;
                } else {
                    $aCampos[$nom_camp] = DateTimeLocal::createFromLocal($aCampos[$nom_camp]);
                }
            }
            // si es con decimales, cambio coma por punto
            if ($tipo === 'decimal' && !empty($aCampos[$nom_camp])) {
                $aCampos[$nom_camp] = str_replace(',', '.', $aCampos[$nom_camp]);
            }

            $metodo = $oDatosCampo->getMetodoSet();
            // uso el método legacy
            if (substr($metodo, -2) === 'Vo') {
                $metodo = substr($metodo, 0, -2);
            }

            // cambiar las cadenas vacías por null (va bien cuando el dato que se espera en un integer)
            // pero en el caso de los check, espera false (no null)
            if ($tipo !== 'check' && empty($aCampos[$nom_camp])) {
                $aCampos[$nom_camp] = null;
            }
            try {
                $oFicha->$metodo($aCampos[$nom_camp]);
            } catch (\Throwable $e) {
                return 'Error al ejecutar ' . $metodo . ' para el campo ' . $nom_camp . ': ' . $e->getMessage();
            }
        }

        $oRepository = $GLOBALS['container']->get($this->RepositoryInterface);
        if ($oRepository->Guardar($oFicha) === false) {
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

    public function setRepositoryInterface($repository)
    {
        $this->RepositoryInterface = $repository;
    }
}