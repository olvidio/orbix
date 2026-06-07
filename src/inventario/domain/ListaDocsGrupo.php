<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;

class ListaDocsGrupo
{
    public function __construct(
        private ColeccionRepositoryInterface $coleccionRepository,
        private LugarRepositoryInterface $lugarRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private WhereisRepositoryInterface $whereisRepository,
        private EgmRepositoryInterface $egmRepository,
    ) {
    }

    /**
     * @return array{a_valores: array<int, array<string, mixed>>, id_item_egm: int|null}
     */
    public function listaDocsGrupo(int $id_equipaje, int $id_lugar, int $id_grupo): array
    {
        $aColeccion = [];
        $cColecciones = $this->coleccionRepository->getColecciones();
        foreach ($cColecciones as $oColeccion) {
            $aColeccion[$oColeccion->getId_coleccion()] = $oColeccion->isAgrupar();
        }

        $cEgm = $this->egmRepository->getEgmes(['id_equipaje' => $id_equipaje, 'id_grupo' => $id_grupo]);
        $id_item_egm = null;
        $a_valores = [];
        if ($cEgm !== []) {
            $id_item_egm = $cEgm[0]->getId_item();
            $cWhereis = $this->whereisRepository->getWhereare(['id_item_egm' => $id_item_egm]);

            $d = 0;
            $orden = [];
            $orden_coleccion = [];
            $num = [];
            foreach ($cWhereis as $oWhereis) {
                $d++;
                $id_doc = $oWhereis->getId_doc();
                if ($id_doc === null) {
                    continue;
                }
                $oDocumento = $this->documentoRepository->findById($id_doc);
                if ($oDocumento === null) {
                    throw new \Exception('Documento no encontrado con ID: ' . $id_doc);
                }
                $identificador = $oDocumento->getIdentificador();
                $num_ejemplares = $oDocumento->getNum_ejemplares();
                $observ = $oDocumento->getObservVo()?->value();
                $id_tipo_doc = $oDocumento->getId_tipo_doc();
                $oTipoDoc = $this->tipoDocRepository->findById($id_tipo_doc);
                if ($oTipoDoc === null) {
                    throw new \Exception('Documento no encontrado con ID: ' . $id_tipo_doc);
                }
                $id_lugar_doc = $oDocumento->getId_lugar();
                if ($id_lugar_doc !== null && $id_lugar !== $id_lugar_doc) {
                    $oLugar = $this->lugarRepository->findById($id_lugar_doc);
                    if ($oLugar === null) {
                        throw new \Exception('Lugar no encontrado con ID: ' . $id_lugar_doc);
                    }
                    $lugar = $oLugar->getNom_lugar();
                    $identificador = _('de') . " $lugar: $identificador";
                }

                if (!empty($num_ejemplares) && $num_ejemplares > 1) {
                    $a_valores[$d]['ejemplares'] = $num_ejemplares;
                    $a_valores[$d]['nombre'] = $num_ejemplares . ' ' . _('ejemplares de') . ' ';
                } else {
                    $a_valores[$d]['ejemplares'] = '';
                    $a_valores[$d]['nombre'] = '';
                }
                $a_valores[$d]['nombre'] .= $oTipoDoc->getSigla() . ' ' . $oTipoDoc->getNom_doc() . ' ' . $observ;

                $a_valores[$d]['identificador'] = $identificador;
                $num[$d] = $a_valores[$d]['identificador'];

                $bAgrupar = false;
                $orden_coleccion[$d] = 0;
                $id_coleccion = $oTipoDoc->getId_coleccion();
                if (!empty($id_coleccion)) {
                    $orden_coleccion[$d] = $id_coleccion;
                    $bAgrupar = $aColeccion[$id_coleccion] ?? false;
                }
                if (!empty($identificador)) {
                    if (!$bAgrupar) {
                        $orden[$d] = 1;
                        $orden_coleccion[$d] = 0;
                    } else {
                        $orden[$d] = 4;
                    }
                } else {
                    if (!empty($id_coleccion)) {
                        $orden[$d] = $bAgrupar ? 4 : 2;
                    } else {
                        $orden[$d] = 3;
                    }
                }

                $a_valores[$d]['lugar'] = false;
                $a_valores[$d]['coleccion'] = empty($id_coleccion) ? false : $id_coleccion;
                $a_valores[$d]['carta'] = $bAgrupar;
            }
            if ($a_valores !== []) {
                array_multisort(
                    $orden,
                    SORT_ASC,
                    $orden_coleccion,
                    SORT_ASC,
                    SORT_NUMERIC,
                    $num,
                    SORT_ASC,
                    SORT_NATURAL,
                    $a_valores
                );
            }
        }

        return [
            'a_valores' => $a_valores,
            'id_item_egm' => $id_item_egm,
        ];
    }
}
