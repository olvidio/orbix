<?php

namespace src\inventario\domain;

use src\inventario\domain\repositories\ColeccionRepository;
use src\inventario\domain\repositories\DocumentoRepository;
use src\inventario\domain\repositories\EgmRepository;
use src\inventario\domain\repositories\LugarRepository;
use src\inventario\domain\repositories\TipoDocRepository;
use src\inventario\domain\repositories\WhereisRepository;

class ListaDocsGrupo
{
    static public function lista_docs_grupo(int $id_equipaje, int $id_lugar, int $id_grupo)
    {
        $Repository = new ColeccionRepository();
        $cColecciones = $Repository->getColecciones();
        foreach ($cColecciones as $oColeccion) {
            $aColeccion[$oColeccion->getId_coleccion()] = $oColeccion->isAgrupar();
        }

        $LugarRepository = new LugarRepository();
        $TipoDocRepository = new TipoDocRepository();
        $DocumentoRepository = new DocumentoRepository();
        $WhereisRepository = new WhereisRepository();
        $EgmRepository = new EgmRepository();
        $cEgm = $EgmRepository->getEgmes(['id_equipaje' => $id_equipaje, 'id_grupo' => $id_grupo]);
        if (is_array($cEgm) && !empty($cEgm)) {
            $id_item_egm = $cEgm[0]->getId_item();
            $cWhereis = $WhereisRepository->getWhereare(['id_item_egm' => $id_item_egm]);

            $d = 0;
            $a_valores = [];
            foreach ($cWhereis as $oWhereis) {
                $d++;
                $id_doc = $oWhereis->getId_doc();
                $oDocumento = $DocumentoRepository->findById($id_doc);
                $identificador = $oDocumento->getIdentificador();
                $num_ejemplares = $oDocumento->getNum_ejemplares();
                $observ = $oDocumento->getObserv();
                $id_tipo_doc = $oDocumento->getId_tipo_doc();
                $oTipoDoc = $TipoDocRepository->findById($id_tipo_doc);
                $id_lugar_doc = $oDocumento->getId_lugar();
                $lugar = '';
                if ($id_lugar != $id_lugar_doc) { // Debo cogerlo de otro sitio.
                    $oLugar = $LugarRepository->findById($id_lugar_doc);
                    $lugar = $oLugar->getNom_lugar();
                    $identificador = _("de") . " $lugar: $identificador";
                }

                if (!empty($num_ejemplares) && $num_ejemplares > 1) {
                    $a_valores[$d][1] = $num_ejemplares . ' ' . _("ejemplares de") . ' ';
                } else {
                    $a_valores[$d][1] = '';
                }
                $a_valores[$d][1] .= $oTipoDoc->getSigla() . " " . $oTipoDoc->getNom_doc() . " " . $observ;

                $a_valores[$d][2] = $identificador;
                //para poder ordenar
                //$tipo[$d] = $a_valores[$d][1];
                $num[$d] = $a_valores[$d][2];

                $id_col = $oTipoDoc->getId_coleccion();
                if (!empty($id_col)) {
                    $orden_col[$d] = $id_col;
                    $bAgrupar = $aColeccion[$id_col];
                    if (!empty($identificador) && !$bAgrupar) {
                        $orden[$d] = 1;
                    } elseif ($bAgrupar) {
                        $orden[$d] = 2;
                    } else {
                        $orden[$d] = 3;
                    }
                } else { // documentos sin colección
                    $orden[$d] = 4;
                    $orden_col[$d] = 0;
                    $bAgrupar = false;
                }
                $a_valores[$d][4] = empty($id_col) ? false : $id_col;
                //$a_valores[$d][5] = false; // para ser compatible con los docs de la casa.
                $a_valores[$d][3] = false; // ?¿?¿? para ser compatible con los docs de la casa.
                $a_valores[$d][5] = $bAgrupar;
            }
            // ordenar por sigla
            if (!empty($a_valores)) {
                array_multisort($orden, SORT_ASC, $num, SORT_ASC, $a_valores);
            }
        } else {
            $a_valores = [];
        }


        $data = [
            'a_valores' => $a_valores,
            'id_item_egm' => $id_item_egm,
        ];

        return $data;
    }

}