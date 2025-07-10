<?php

namespace src\certificados\domain;

use core\DatosInfo;
use src\certificados\application\repositories\CertificadoEmitidoRepository;

/* No vale el underscore en el nombre */

class InfoCertificados extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("certificados del stgr"));;
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este certificado?"));
        $this->setTxtBuscar(_("buscar en sigla"));
        $this->setTxtExplicacion();

        $this->setClase('src\certificados\domain\entity\CertificadoRecibido');
        $this->setMetodoGestor('getCertificados');
    }

    public function getColeccion()
    {
        $aOperador = [];
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $aWhere['certificado'] = $this->k_buscar;
            //$aOperador['certificado'] = 'sin_acentos';
        }
        /*
        $aWhere['id_asignatura'] = 3000;
        $aOperador['id_asignatura'] = '<';
        */
        $aWhere['_ordre'] = 'certificado';

        $certificadoEmitidoRepository = new CertificadoEmitidoRepository();
        $Coleccion = $certificadoEmitidoRepository->getCertificados($aWhere, $aOperador);

        return $Coleccion;
    }
}