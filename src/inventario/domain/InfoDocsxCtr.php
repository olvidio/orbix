<?php

namespace src\inventario\domain;

use src\inventario\application\repositories\DocumentoRepository;
use src\inventario\application\repositories\LugarRepository;
use src\inventario\application\repositories\UbiInventarioRepository;
use src\shared\domain\DatosInfoRepo;
use web\Desplegable;

/* No vale el underscore en el nombre */

class InfoDocsxCtr extends DatosInfoRepo
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
        return '../view/buscarDocsxCtr.phtml';
    }

    public function getBuscar_namespace()
    {
        return __NAMESPACE__;
    }

    /**
     * campos del formulario a añadir al hashBuscar
     *
     * @return string
     */
    public function addCamposFormBuscar()
    {
        return '!exacto';
    }

    public function addCampos($a_campos=[])
    {
        return $a_campos;
    }

    public function getColeccion()
    {
        // Si se quiere listar una selección, $k_buscar
        if (empty($this->k_buscar)) {
            // para evitar que salgan todos
            $aWhere = array('_limit' => 6, '_ordre' => 'id_ubi,id_lugar');
            $aOperador = [];
        } else {
            $nom_ubi = str_replace("+", "\+", $this->k_buscar); // para los centros de la sss+
            $aWhereUbi = array('nom_ubi' => $nom_ubi);
            if ($this->exacto) {
                $aOperadorUbi = [];
            } else {
                $aOperadorUbi = array('nom_ubi' => 'sin_acentos');
            }
            //selecciono los ctrs
            $RepoUbiInventario = new UbiInventarioRepository();
            $cUbisInventario = $RepoUbiInventario->getUbisInventario($aWhereUbi, $aOperadorUbi);
            $lst_id_ubi = '';
            foreach ($cUbisInventario as $oUbiDoc) {
                $lst_id_ubi .= empty($lst_id_ubi) ? '' : ',';
                $lst_id_ubi .= $oUbiDoc->getId_ubi();
            }
            // para evitar que salgan todos
            if (empty($lst_id_ubi)) {
                $aWhere = ['_limit' => 6];
                $aOperador = [];
            } else {
                $aWhere = ['id_ubi' => $lst_id_ubi];
                $aOperador = ['id_ubi' => 'IN'];
            }
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
