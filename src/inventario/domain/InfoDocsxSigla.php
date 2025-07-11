<?php

namespace src\inventario\domain;

use core\ConfigGlobal;
use src\inventario\application\repositories\DocumentoRepository;
use src\inventario\application\repositories\LugarRepository;
use src\inventario\application\repositories\TipoDocRepository;
use src\shared\domain\DatosInfoRepo;
use web\Desplegable;
use web\Hash;

/* No vale el underscore en el nombre */

class InfoDocsxSigla extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("documentos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este documento?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Documento');
        $this->setMetodoGestor('getId_doc');
        $this->setPau('p');
    }

    public function getBuscar_view()
    {
        return '../view/buscarDocsxSigla.phtml';
    }

    public function getBuscar_namespace()
    {
        return __NAMESPACE__;
    }

    public function addCamposFormBuscar()
    {
        return '';
    }

    public function addCampos($a_campos = [])
    {
        $Repository = new TipoDocRepository();
        $aOpciones = $Repository->getArrayTipoDoc();
        $a_campos['aOpcionesTiposDoc'] = $aOpciones;

        $url_bloque = ConfigGlobal::getWeb().'/src/inventario/infrastructure/controllers/documentos_form.php';
        $oHash = new Hash();
        $sCamposForm = 'id_tipo_doc!documentos';
        $oHash->setUrl($url_bloque);
        $oHash->setCamposForm($sCamposForm);
        $h1 = $oHash->linkSinVal();

        $a_campos['url_bloque'] = $url_bloque;
        $a_campos['h1'] = $h1;

        // para los campos de comprobar fecha
        $locale_us = ConfigGlobal::is_locale_us();
        $a_campos['locale_us'] = $locale_us;

        return $a_campos;
    }

    public function getColeccion()
    {
        // Si se quiere listar una selección, $_POST['k_buscar']
        if (empty($this->k_buscar)) {
            // para evitar que salgan todos
            $aWhere = ['id_tipo_doc' => 0];
            $aOperador = [];
        } else {
            $id_tipo_doc = $this->k_buscar;
            $aWhere = ['id_tipo_doc' => $id_tipo_doc, '_ordre' => 'id_ubi,id_lugar'];
            $aOperador = [];
        }

        $ColeccionRepository = new DocumentoRepository();
        $Coleccion = $ColeccionRepository->getDocumentos($aWhere, $aOperador);

        return $Coleccion;
    }

    public function getOpcionesParaCondicion($pKeyRepository,$valor_depende, $opcion_sel=null)
    {
        $valor_depende = empty($valor_depende) ? 0 : $valor_depende;
        //caso de actualizar el campo depende
        $LugarRepository = new LugarRepository();
        $aOpciones = $LugarRepository->getArrayLugares($valor_depende);
        $oDesplegable = new Desplegable('', $aOpciones, $opcion_sel, true);
        $opciones_txt = $oDesplegable->options();

        return $opciones_txt;
    }

    public function getArrayCamposDepende()
    {
        // key -> campo pKeyRepository (campo llave del repository)
        // value -> campo que se debe llenar con valores del repository
        return ['id_ubi' =>'id_lugar'];
    }

}
