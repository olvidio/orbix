<?php

namespace src\personas\domain;

// necesario para los desplegables de 'depende'

/* No vale el underscore en el nombre */

use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaExRepositoryInterface;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use web\Desplegable;

class InfoTelecoPersona extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("telecomunicaciones de una persona"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta teleco?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\TelecoPersonaDl');
        $this->setMetodoGestor('getTelecosPersona');
        $this->setPau('p');

        $this->setRepositoryInterface(TelecoPersonaDlRepositoryInterface::class);
    }

    public function getId_dossier()
    {
        return 1001;
    }


    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'id_tipo_teleco';
            $aOperador = [];
        } else {
            $aWhere['congreso'] = $this->k_buscar;
            $aOperador['congreso'] = 'sin_acentos';
        }


        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getTelecosPersona($aWhere, $aOperador);

        return $Coleccion;
    }

    public function setObj_pau($Qobj_pau)
    {
        switch ($Qobj_pau) {
            case 'PersonaN':
                $this->repoInterface = TelecoPersonaDlRepositoryInterface::class;
                break;
            case 'PersonaEx':
                $this->repoInterface = TelecoPersonaExRepositoryInterface::class;
                break;
        }
    }

    public function getOpcionesParaCondicion($pKeyRepository, $valor_depende, $opcion_sel = null)
    {
        $valor_depende = empty($valor_depende) ? 0 : $valor_depende;
        //caso de actualizar el campo depende
        $DescTelecoRepository = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
        $aOpciones = $DescTelecoRepository->getArrayDescTelecoPersonas($valor_depende);
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
