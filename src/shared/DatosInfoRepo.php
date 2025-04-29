<?php

namespace src\shared;

/* No vale el underscore en el nombre */

abstract class DatosInfoRepo
{

    protected $accion;
    protected $obj;

    protected $txt_explicacion;
    protected $txt_titulo;
    protected $txt_eliminar;
    protected $txt_buscar;
    protected $metodo_gestor;

    protected $id_pau;
    protected $mod;
    protected $a_pkey;

    protected $k_buscar;

    protected $pau;

    /**
     * Para sobreescribir por la clase que si tenga
     * @return string empty
     */
    public function getBuscar_view()
    {
        return '';
    }

    public function getBuscar_namespace()
    {
        return '';
    }

    public function getMetodoGestor()
    {
        return $this->metodo_gestor;
    }

    public function setMetodoGestor($metodo_gestor)
    {
        $this->metodo_gestor = $metodo_gestor;
    }

    public function setId_pau($id_pau)
    {
        $this->id_pau = $id_pau;
    }

    public function setMod($mod)
    {
        $this->mod = $mod;
    }

    public function setA_pkey($a_pkey)
    {
        $this->a_pkey = $a_pkey;
    }

    public function setK_buscar($k_buscar)
    {
        $this->k_buscar = $k_buscar;
    }

    public function setTxtExplicacion($txt_explicacion = '')
    {
        $this->txt_explicacion = $txt_explicacion;
    }

    public function setTxtTitulo($txt_titulo = '')
    {
        $this->txt_titulo = $txt_titulo;
    }

    public function setTxtEliminar($txt_eliminar = '')
    {
        $this->txt_eliminar = $txt_eliminar;
    }

    public function setTxtBuscar($txt_buscar = '')
    {
        $this->txt_buscar = $txt_buscar;
    }

    public function getTxtExplicacion()
    {
        if (!isset($this->txt_explicacion)) {
            return '';
        } else {
            return $this->txt_explicacion;
        }
    }

    public function getTxtTitulo()
    {
        if (!isset($this->txt_titulo)) {
            return '';
        } else {
            return $this->txt_titulo;
        }
    }

    public function getTxtEliminar()
    {
        if (!isset($this->txt_eliminar)) {
            return '';
        } else {
            return $this->txt_eliminar;
        }
    }

    public function getTxtBuscar()
    {
        if (!isset($this->txt_buscar)) {
            return '';
        } else {
            return $this->txt_buscar;
        }
    }

    public function setClase($obj)
    {
        $this->obj = $obj;
    }

    public function getClase()
    {
        return $this->obj;
    }

    public function setPau($pau)
    {
        $this->pau = $pau;
    }

    public function getPau()
    {
        return $this->pau;
    }

    public function getKeyCollection()
    {
        $key_collection = '';
        if (isset($this->pau)) {
            switch ($this->pau) {
                case 'p':
                    $key_collection = array('id_nom' => $this->id_pau);
                    break;
                case 'a':
                    $key_collection = array('id_activ' => $this->id_pau);
                    break;
                case 'u':
                    $key_collection = array('id_ubi' => $this->id_pau);
                    break;
            }
        }
        return $key_collection;
    }

    public function setObj_pau($obj_pau)
    {
        switch ($obj_pau) {
            case 'Centro':
                $this->obj = 'ubis\\model\\entity\\TelecoCtr';
                break;
            case 'CentroDl':
                $this->obj = 'ubis\\model\\entity\\TelecoCtrDl';
                break;
            case 'CentroEx':
                $this->obj = 'ubis\\model\\entity\\TelecoCtrEx';
                break;
            case 'Casa':
                $this->obj = 'ubis\\model\\entity\\TelecoCdc';
                break;
            case 'CasaDl':
                $this->obj = 'ubis\\model\\entity\\TelecoCdcDl';
                break;
            case 'CasaEx':
                $this->obj = 'ubis\\model\\entity\\TelecoCdcEx';
                break;
        }
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        $gestor = preg_replace('/\\\(\w*)$/', '\Gestor\1', $this->obj);
        $oLista = new $gestor();
        $metodo = $this->getMetodoGestor();
        $key = $this->getKeyCollection();
        $Coleccion = $oLista->$metodo($key);

        return $Coleccion;
    }

    public function getFicha()
    {
        $obj = $this->obj;
        $repo = str_replace('domain\entity', 'application\repositories', $obj);
        $repository = $repo . 'Repository';

        $oFicha = null;
        $oRepository = new $repository();
        switch ($this->mod) {
            case 'nuevo':
                $oFicha = new $this->obj($this->getKeyCollection());
                break;
            case 'eliminar':
            case 'editar':
                if (!empty($this->a_pkey)) {
                    $oFicha = $oRepository->findById($this->a_pkey);
                }
                break;
        }
        return $oFicha;
    }

    public function getDespl_depende()
    {
        $despl_depende = "<option></option>";
        return $despl_depende;
        /* Debe sobreescribirse el método, esto es un ejemlpo
        $oFicha =  $this->getFicha();
        // para el desplegable depende
        $v1=$oFicha->tipo_teleco;
        $v2=$oFicha->desc_teleco;
        if (!empty($v2)) {
            $oDepende = new GestorDescTeleco();
            $aOpciones=$oDepende->getListaDescTelecoUbis($v1);
            $oDesplegable=new Desplegable('',$aOpciones,$v2,true);
            $despl_depende = $oDesplegable->options();
        } else {
            $despl_depende = "<option></option>";
        }
         *
         */
    }

    public function getAccion($valor_depende)
    {
        //caso de actualizar el campo depende
        /* Debe sobreescribirse el método, esto es un ejemlpo
        if (isset($this->accion)) {
            if ($this->accion == 'desc_teleco') {
                $oDepende = new GestorDescTeleco();
                $aOpciones = $oDepende->getListaDescTelecoUbis($valor_depende);
                $oDesplegable = new Desplegable('',$aOpciones,'',true);
                $despl_depende = $oDesplegable->options();
            }
        }

        return $despl_depende;
         *
         */
    }

    public function setAccion($accion)
    {
        $this->accion = $accion;
    }
}