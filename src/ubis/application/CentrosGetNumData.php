<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class CentrosGetNumData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * @return array{a_cabeceras: list<mixed>, a_valores: array<int, array<int, mixed>>}
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $aWhere = ['active' => 't', '_ordre' => 'nombre_ubi'];
        $cCentrosDl = $this->centroDlRepository->getCentros($aWhere);

        $c = 0;
        $a_valores = [];
        foreach ($cCentrosDl as $oCentro) {
            $c++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $n_buzon = $oCentro->getN_buzon();
            $num_pi = $oCentro->getNum_pi();
            $num_cartas = $oCentro->getNum_cartas();

            $num_pi = empty($num_pi) ? '0' : $num_pi;
            $num_cartas = empty($num_cartas) ? '0' : $num_cartas;

            $script = "fnjs_modificar($id_ubi,\"num\")";
            $a_valores[$c][1] = ['script' => $script, 'valor' => $nombre_ubi];
            $a_valores[$c][2] = $n_buzon;
            $a_valores[$c][3] = $num_pi;
            $a_valores[$c][4] = $num_cartas;
        }

        $a_cabeceras = [];
        $a_cabeceras[] = ['name' => ucfirst(_("centro")), 'width' => 100, 'formatter' => 'clickFormatter'];
        $a_cabeceras[] = ucfirst(_("número de buzón"));
        $a_cabeceras[] = ucfirst(_("número de pi"));
        $a_cabeceras[] = ucfirst(_("número de cartas"));

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }
}
