<?php

namespace src\ubis\application;

final class DireccionesTablaData
{
    public function __construct(
        private DireccionesResolver $direccionesResolver,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_ubi, string $obj_dir, string $c_p, string $ciudad, string $pais): array
    {
        $DireccionRepository = $this->direccionesResolver->direccionRepo($obj_dir);
        $aWhere = [];
        $aOperador = [];
        if ($c_p !== '') {
            $aWhere['c_p'] = '%' . $c_p . '%';
            $aOperador['c_p'] = 'LIKE';
        }
        if ($ciudad !== '') {
            $aWhere['poblacion'] = $ciudad;
            $aOperador['poblacion'] = 'sin_acentos';
        }
        if ($pais !== '') {
            $aWhere['pais'] = $pais;
            $aOperador['pais'] = 'sin_acentos';
        }

        $a_valores = [];
        $i = 0;
        $cDirecciones = $DireccionRepository->getDirecciones($aWhere, $aOperador);
        foreach ($cDirecciones as $oDireccion) {
            $i++;
            $id_direccion = $oDireccion->getId_direccion();
            $a_valores[$i][1] = $id_direccion;
            $a_valores[$i][2] = ['script' => "fnjs_asignar_dir($id_ubi,\"$obj_dir\", $id_direccion)", 'valor' => 'ok'];
            $a_valores[$i][3] = $oDireccion->getDireccionVo()?->value() ?? '';
            $a_valores[$i][4] = $oDireccion->getC_p();
            $a_valores[$i][5] = $oDireccion->getPoblacion();
            $a_valores[$i][6] = $oDireccion->getProvincia();
            $a_valores[$i][7] = $oDireccion->getA_p();
            $a_valores[$i][8] = $oDireccion->getPais();
            $a_valores[$i][9] = $oDireccion->getF_direccion()?->getFromLocal();
            $a_valores[$i][10] = $oDireccion->getObserv();
        }

        return [
            'a_cabeceras' => [
                _("id"),
                ['name' => _("ok"), 'formatter' => 'clickFormatter'],
                _("dirección"),
                _("cp"),
                _("ciudad"),
                _("provincia"),
                _("ap. correos"),
                _("país"),
                ['name' => ucfirst(_("última modif.")), 'class' => 'fecha'],
                _("observaciones"),
            ],
            'a_valores' => $a_valores,
            'url_nueva' => 'frontend/ubis/controller/direcciones_editar.php',
            'id_ubi' => $id_ubi,
            'obj_dir' => $obj_dir,
        ];
    }
}
