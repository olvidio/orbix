<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\security\HashB;

/**
 * Documentos de un lugar usado como maleta de un equipaje (`lista_docs_de_lugar`), todos marcados, con las
 * cápsulas para guardar el grupo, añadir documentos y eliminar el grupo (cada una solo si llegan sus ids).
 *
 * Sucesor de la lógica del controlador `lista_docs_de_lugar.php` (fase 5, paso 5.2). La respuesta es la
 * misma (filas indexadas desde 1, en el orden del repositorio).
 */
final class DocumentosDeLugarData
{
    public function __construct(
        private DocumentoRepositoryInterface $documentoRepository,
        private LugarRepositoryInterface $lugarRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
    ) {
    }

    /** @return array{a_valores: array<int, array<int|string, mixed>>, nombre_valija: string, ctx_update_grupo: string, ctx_add: string, ctx_eliminar_grupo: string} */
    public function execute(int $idLugar, int $idGrupo, int $idEquipaje, int $idItemEgm): array
    {
        $out = [
            'a_valores' => [],
            'nombre_valija' => '',
            'ctx_update_grupo' => ($idGrupo > 0 && $idEquipaje > 0 && $idLugar > 0)
                ? HashB::sign('equipajes_update_grupo', ['id_grupo' => $idGrupo, 'id_equipaje' => $idEquipaje, 'id_lugar' => $idLugar])
                : '',
            'ctx_add' => $idItemEgm > 0 ? HashB::sign('equipajes_add_doc', ['id_item_egm' => $idItemEgm]) : '',
            'ctx_eliminar_grupo' => ($idGrupo > 0 && $idEquipaje > 0)
                ? HashB::sign('equipajes_eliminar_grupo', ['id_grupo' => $idGrupo, 'id_equipaje' => $idEquipaje])
                : '',
        ];
        $cDocumentos = $this->documentoRepository->matching(Criteria::create()->where(Filter::eq(DocF::IdLugar, $idLugar)));
        $oLugar = $this->lugarRepository->findById($idLugar);
        if ($oLugar === null) {
            return $out;
        }
        $out['nombre_valija'] = $oLugar->getNom_lugar();
        $d = 0;
        foreach ($cDocumentos as $oDocumento) {
            $d++;
            $oTipoDoc = $this->tipoDocRepository->findById((int) $oDocumento->getId_tipo_doc());
            if ($oTipoDoc === null) {
                continue;
            }
            $out['a_valores'][$d] = [
                'sel' => ['id' => $oDocumento->getId_doc(), 'select' => 'checked'],
                1 => $oTipoDoc->getSigla() . ' ' . $oTipoDoc->getNom_doc(),
                2 => $oDocumento->getIdentificador(),
                3 => $oDocumento->getNum_reg(),
            ];
        }

        return $out;
    }
}
