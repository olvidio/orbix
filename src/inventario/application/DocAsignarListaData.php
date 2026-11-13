<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\inventario\domain\criteria\UbiInventarioField as UbiF;
use src\inventario\domain\entity\TipoDoc;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use src\shared\security\HashB;

/**
 * Formulario para asignar un tipo de documento a los centros elegidos (`lista_docs_asignar_ctr`) o a
 * los lugares de la dl elegidos (`lista_docs_asignar_dlb`): una fila por destino con el número que se
 * propone (el de registro siguiente si el tipo es numerado; si no, el número de ejemplares que ya tiene
 * el centro, o 1).
 *
 * Sucesor de la lógica de esos dos controladores (fase 5, paso 5.2). Cambios:
 * - devuelve filas neutras (`id`, `nombre`, `num`); el `<input>` y la lista de campos del formulario,
 *   que antes se montaban aquí en `src/`, los monta ahora el frontend;
 * - **corrige el número siguiente** de los tipos numerados: se tomaba el del primer documento ordenando
 *   por número descendente, y en PostgreSQL los nulos van primero; con un solo documento sin número, el
 *   «último» era nulo y se proponía 1, 2… en vez de último + 1.
 */
final class DocAsignarListaData
{
    public function __construct(
        private TipoDocRepositoryInterface $tipoDocRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /**
     * @param list<mixed> $idsUbi centros elegidos
     * @return array{filas: list<array{id: int, nombre: string, num: int|null}>, nombreDoc: string, isNumerado: bool, ctx_guardar: string}|array{}
     */
    public function paraCentros(int $idTipoDoc, array $idsUbi): array
    {
        $oTipoDoc = $this->tipoDocRepository->findById($idTipoDoc);
        if ($oTipoDoc === null) {
            return [];
        }
        $destinos = [];
        foreach ($this->ubiInventarioRepository->matching(Criteria::create()->orderBy(UbiF::NomUbi)) as $oUbi) {
            if (in_array($oUbi->getId_ubi(), $idsUbi)) {
                $destinos[] = ['id' => $oUbi->getId_ubi(), 'nombre' => $oUbi->getNom_ubi(), 'id_ubi' => $oUbi->getId_ubi()];
            }
        }

        return $this->respuesta($oTipoDoc, $destinos, 'doc_asignar_ctr_guardar');
    }

    /**
     * @param list<mixed> $idsLugar lugares elegidos
     * @return array{filas: list<array{id: int, nombre: string, num: int|null}>, nombreDoc: string, isNumerado: bool, ctx_guardar: string}|array{}
     */
    public function paraLugares(int $idTipoDoc, array $idsLugar): array
    {
        $oTipoDoc = $this->tipoDocRepository->findById($idTipoDoc);
        if ($oTipoDoc === null) {
            return [];
        }
        $destinos = [];
        foreach ($idsLugar as $raw) {
            $oLugar = is_numeric($raw) ? $this->lugarRepository->findById((int) $raw) : null;
            $oUbi = $oLugar === null ? null : $this->ubiInventarioRepository->findById((int) $oLugar->getId_ubi());
            if ($oLugar === null || $oUbi === null) {
                continue;
            }
            $destinos[] = ['id' => $oLugar->getId_lugar(), 'nombre' => $oUbi->getNom_ubi() . ' - ' . $oLugar->getNom_lugar(), 'id_ubi' => $oUbi->getId_ubi()];
        }

        return $this->respuesta($oTipoDoc, $destinos, 'doc_asignar_dlb_guardar');
    }

    /**
     * @param list<array{id: int, nombre: string, id_ubi: int}> $destinos
     * @return array{filas: list<array{id: int, nombre: string, num: int|null}>, nombreDoc: string, isNumerado: bool, ctx_guardar: string}
     */
    private function respuesta(TipoDoc $oTipoDoc, array $destinos, string $accionGuardar): array
    {
        $idTipoDoc = $oTipoDoc->getId_tipo_doc();
        $numerado = (bool) $oTipoDoc->isNumerado();
        $ultimo = $numerado ? $this->ultimoNumero($idTipoDoc) : 0;
        $filas = [];
        foreach ($destinos as $i => $destino) {
            if ($numerado) {
                $num = $ultimo + $i + 1;
            } else {
                $cDocumentos = $this->documentoRepository->matching(Criteria::create()
                    ->where(Filter::eq(DocF::IdTipoDoc, $idTipoDoc))
                    ->andWhere(Filter::eq(DocF::IdUbi, $destino['id_ubi'])));
                $num = $cDocumentos === [] ? 1 : $cDocumentos[0]->getNum_ejemplares();
            }
            $filas[] = ['id' => $destino['id'], 'nombre' => $destino['nombre'], 'num' => $num];
        }
        $nomDoc = $oTipoDoc->getNom_doc();

        return [
            'filas' => $filas,
            'nombreDoc' => empty($nomDoc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nomDoc . ')',
            'isNumerado' => $numerado,
            'ctx_guardar' => HashB::sign($accionGuardar, ['id_tipo_doc' => $idTipoDoc]),
        ];
    }

    /** Mayor número de registro del tipo (0 si no hay ninguno con número). */
    private function ultimoNumero(int $idTipoDoc): int
    {
        $cDocumentos = $this->documentoRepository->matching(Criteria::create()
            ->where(Filter::eq(DocF::IdTipoDoc, $idTipoDoc))
            ->andWhere(Filter::isNotNull(DocF::NumReg))
            ->orderBy(DocF::NumReg, OrderDirection::Desc)
            ->limit(1));

        return $cDocumentos === [] ? 0 : (int) $cDocumentos[0]->getNum_reg();
    }
}
