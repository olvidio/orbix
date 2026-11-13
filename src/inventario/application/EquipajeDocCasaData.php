<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\ColeccionField as ColF;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\inventario\domain\criteria\UbiInventarioField as UbiF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Documentos que tiene la casa donde va un equipaje (`equipajes_doc_casa`): los no eliminados del
 * inventario de esa casa, ordenados por lugar, con identificador primero, después las colecciones que
 * se agrupan («cartas») y los demás, por colección, identificador y nombre.
 *
 * Sucesor de la lógica del controlador `equipajes_doc_casa.php` (fase 5, paso 5.2). Corrige: un
 * equipaje sin casa (`id_ubi_activ` vacío) daba una excepción al filtrar con null, y un tipo de
 * documento cuya colección no existe, un aviso de PHP.
 */
final class EquipajeDocCasaData
{
    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
        private ColeccionRepositoryInterface $coleccionRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /**
     * @return array{a_valores: array<int, array<string, mixed>>, nombre_ubi: string, id_ubi: int|null}|array{}
     */
    public function execute(int $idEquipaje): array
    {
        $oEquipaje = $this->equipajeRepository->findById($idEquipaje);
        if ($oEquipaje === null) {
            return [];
        }
        $idUbiActiv = $oEquipaje->getId_ubi_activ();
        $nombreLugar = $oEquipaje->getLugarVo()?->value() ?? '';
        $cUbis = $idUbiActiv === null
            ? []
            : $this->ubiInventarioRepository->matching(Criteria::create()->where(Filter::eq(UbiF::IdUbiActiv, $idUbiActiv)));
        if ($cUbis === []) {
            return ['a_valores' => [], 'nombre_ubi' => $nombreLugar, 'id_ubi' => null];
        }

        $agrupar = [];
        foreach ($this->coleccionRepository->matching(Criteria::create()->orderBy(ColF::NomColeccion)) as $oColeccion) {
            $agrupar[$oColeccion->getId_coleccion()] = $oColeccion->isAgrupar();
        }
        $idUbi = $cUbis[0]->getId_ubi();
        $cDocumentos = $this->documentoRepository->matching(Criteria::create()
            ->where(Filter::eq(DocF::IdUbi, $idUbi))
            ->andWhere(Filter::eq(DocF::Eliminado, false)));

        $valores = [];
        $orden = [];
        $tipo = [];
        $num = [];
        $lugar = [];
        $coleccion = [];
        foreach ($cDocumentos as $oDocumento) {
            $oTipoDoc = $this->tipoDocRepository->findById((int) $oDocumento->getId_tipo_doc());
            if ($oTipoDoc === null) {
                continue;
            }
            $nomLugar = '';
            $idLugar = $oDocumento->getId_lugar();
            if (!empty($idLugar)) {
                $oLugar = $this->lugarRepository->findById($idLugar);
                if ($oLugar === null) {
                    continue;
                }
                $nomLugar = $oLugar->getNom_lugar();
            }
            $identificador = $oDocumento->getIdentificador();
            $numEjemplares = $oDocumento->getNum_ejemplares();
            $varios = !empty($numEjemplares) && $numEjemplares > 1;
            $nombre = ($varios ? $numEjemplares . ' ' . _("ejemplares de") . ' ' : '')
                . $oTipoDoc->getSigla() . ' ' . $oTipoDoc->getNom_doc() . ' ' . $oDocumento->getObservVo()?->value();

            // Primero los que tienen identificador y no son cartas, luego cartas, luego sin identificador.
            $idColeccion = $oTipoDoc->getId_coleccion();
            if ($idColeccion !== null) {
                $carta = $agrupar[$idColeccion] ?? null;
                $orden[] = (!empty($identificador) && !$carta) ? 1 : ($carta ? 2 : 3);
            } else {
                $carta = '';
                $orden[] = 4;
            }
            $valores[] = [
                'ejemplares' => $varios ? $numEjemplares : '',
                'nombre' => $nombre,
                'identificador' => $identificador,
                'lugar' => $nomLugar,
                'coleccion' => empty($idColeccion) ? false : $idColeccion,
                'carta' => empty($carta) ? false : $carta,
            ];
            $tipo[] = $nombre;
            $num[] = $identificador;
            $lugar[] = $nomLugar;
            $coleccion[] = empty($idColeccion) ? false : $idColeccion;
        }
        if ($valores !== []) {
            array_multisort($lugar, SORT_ASC, $orden, SORT_NUMERIC, SORT_DESC, $coleccion, SORT_NUMERIC, SORT_ASC,
                $num, SORT_NUMERIC, SORT_ASC, $tipo, SORT_ASC, SORT_NATURAL, $valores);
        }

        return ['a_valores' => $valores, 'nombre_ubi' => $cUbis[0]->getNom_ubi(), 'id_ubi' => $idUbi];
    }
}
