<?php

namespace src\inventario\model;

use src\inventario\domain\repositories\DocumentoRepository;
use src\inventario\domain\repositories\UbiInventarioRepository;
use src\shared\DatosInfoRepo;

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

    public function getBuscar_view(){
        return 'buscarDocsxCtr.phtml';
    }

    public function getBuscar_namespace()
    {
        return 'inventario\controller';
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

        $oHash = $aCampos['oHashBuscar'];
        $camposForm = $oHash->getCamposForm();
        $camposForm .= '!exacto';
        $oHash->setCamposForm($camposForm);

        $a_campos['oHashBuscar'] = $oHash;

        return $a_campos;
    }

    public function getColeccion()
    {
        // Si se quiere listar una selección, $_POST['k_buscar']
        if (empty($_POST['k_buscar'])) {
            // para evitar que salgan todos
            $aWhere = array('id_ubi' => 1, '_ordre' => 'id_ubi,id_lugar');
            $aOperador = [];
        } else {
            $nom_ubi = str_replace("+", "\+", $_POST['k_buscar']); // para los centros de la sss+
            $aWhereUbi = array('nom_ubi' => $nom_ubi);
            if (!empty($_POST['exacto'])) {
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
                $id_ubi = 1;
                $aWhere = ['id_ubi' => $id_ubi];
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
}
