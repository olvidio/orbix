<?php

namespace src\ubis\application;

use src\ubis\domain\criteria\DireccionField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\security\HashB;

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
        $criteria = Criteria::create();
        if ($c_p !== '') {
            // antes `c_p LIKE '%…%'`: subcadena (el texto se escapa para que no valga como patrón)
            $criteria = $criteria->andWhere(Filter::matches(F::CP, preg_quote($c_p)));
        }
        if ($ciudad !== '') {
            $criteria = $criteria->andWhere(Filter::matchesUnaccented(F::Poblacion, $ciudad));
        }
        if ($pais !== '') {
            $criteria = $criteria->andWhere(Filter::matchesUnaccented(F::Pais, $pais));
        }

        $a_valores = [];
        $i = 0;
        $cDirecciones = $DireccionRepository->matching($criteria);
        foreach ($cDirecciones as $oDireccion) {
            $i++;
            $id_direccion = $oDireccion->getId_direccion();
            $ctx_asignar = HashB::sign('direcciones_asignar', [
                'id_ubi' => $id_ubi,
                'obj_dir' => $obj_dir,
                'id_direccion' => $id_direccion,
            ]);
            $a_valores[$i][1] = $id_direccion;
            $a_valores[$i][2] = ['script' => 'fnjs_asignar_dir(' . json_encode($ctx_asignar) . ')', 'valor' => 'ok'];
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
