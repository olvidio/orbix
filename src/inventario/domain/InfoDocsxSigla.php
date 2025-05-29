<?php

namespace src\inventario\domain;

use core\ConfigGlobal;
use src\inventario\application\repositories\DocumentoRepository;
use src\inventario\application\repositories\TipoDocRepository;
use src\shared\DatosInfoRepo;
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

    public function addCampos($aCampos)
    {
        /*
        $a_campos = [
            'script' => $oDatosTabla->getScript(),
            'url' => $url,
            'oHashBuscar' => $oHashBuscar,
            'txt_buscar' => $oInfoClase->getTxtBuscar(),
            'k_buscar' => $Qk_buscar,
        ];
        */
        $a_campos = $aCampos;

        $Repository = new TipoDocRepository();
        $aOpciones = $Repository->getArrayTipoDoc();
        $oDesplTiposDoc = new Desplegable('k_buscar',$aOpciones,'',true);
        $a_campos['oDesplTiposDoc'] = $oDesplTiposDoc;

        $url_bloque = ConfigGlobal::getWeb().'/src/inventario/controller/documentos_form.php';
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
        if (empty($_POST['k_buscar'])) {
            // para evitar que salgan todos
            $aWhere = ['id_tipo_doc' => 0];
            $aOperador = [];
        } else {
            $id_tipo_doc = $_POST['k_buscar'];
            $aWhere = ['id_tipo_doc' => $id_tipo_doc, '_ordre' => 'id_ubi,id_lugar'];
            $aOperador = [];
        }

        $ColeccionRepository = new DocumentoRepository();
        $Coleccion = $ColeccionRepository->getDocumentos($aWhere, $aOperador);

        return $Coleccion;
    }
}
