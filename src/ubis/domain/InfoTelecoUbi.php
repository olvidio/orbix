<?php

namespace src\ubis\domain;

/* No vale el underscore en el nombre */

use src\shared\infrastructure\ProvidesRepositories;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use web\Desplegable;

class InfoTelecoUbi extends DatosInfoRepo
{
    use ProvidesRepositories;

    public function __construct()
    {
        $this->setTxtTitulo(_("Telecos del Ubi"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta teleco?"));
        $this->setTxtBuscar(_("buscar una teleco (número)"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\TelecoUbi');
        $this->setMetodoGestor('getTelecos');
        $this->setPau('u');

        // $this->setRepositoryInterface --> se hace en setObj_pau()
    }

    public function getId_dossier()
    {
        return 2001;
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_ubi'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'id_tipo_teleco';
            $aOperador = [];
        } else {
            $aWhere = ['num_teleco' => $this->k_buscar];
            $aOperador = ['num_teleco' => 'sin_acentos'];
        }

        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getTelecos($aWhere, $aOperador);

        return $Coleccion;
    }

    public function setObj_pau($obj_pau): void
    {
        $repoInterface = $this->getTelecoRepositoryClass((string)$obj_pau);
        $this->setRepositoryInterface($repoInterface);
    }

    public function getOpcionesParaCondicion($pKeyRepository, $valor_depende, $opcion_sel = null)
    {
        $valor_depende = empty($valor_depende) ? 0 : $valor_depende;
        //caso de actualizar el campo depende
        $DescTelecoRepository = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
        $aOpciones = $DescTelecoRepository->getArrayDescTelecoUbis($valor_depende);
        $oDesplegable = new Desplegable('', $aOpciones, $opcion_sel, true);
        $opciones_txt = $oDesplegable->options();

        return $opciones_txt;
    }

    public function getArrayCamposDepende()
    {
        // key -> campo pKeyRepository (campo llave del repository)
        // value -> campo que se debe llenar con valores del repository
        return ['id_tipo_teleco' => 'id_desc_teleco'];
    }
}
