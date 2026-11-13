<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\criteria\WhereisField as WhF;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\security\HashB;

/**
 * Documentos de una maleta (egm) de un equipaje (`lista_docs_de_egm`), ordenados por tipo, con las
 * cápsulas para quitar documentos, añadir y eliminar el grupo.
 *
 * Sucesor de la lógica del controlador `lista_docs_de_egm.php` (fase 5, paso 5.2). Se quita una
 * consulta del lugar de cada documento que no se usaba y cuyo único efecto era esconder de la lista los
 * documentos de la maleta cuyo lugar ya no existe.
 */
final class DocumentosDeEgmData
{
    public function __construct(
        private EgmRepositoryInterface $egmRepository,
        private LugarRepositoryInterface $lugarRepository,
        private WhereisRepositoryInterface $whereisRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
    ) {
    }

    /** @return array<string, mixed> */
    public function execute(int $idItemEgm): array
    {
        $oEgm = $this->egmRepository->findById($idItemEgm);
        $oLugar = $oEgm === null ? null : $this->lugarRepository->findById((int) $oEgm->getId_lugar());
        if ($oEgm === null || $oLugar === null) {
            return [];
        }
        $valores = [];
        $tipos = [];
        foreach ($this->whereisRepository->matching(Criteria::create()->where(Filter::eq(WhF::IdItemEgm, $idItemEgm))) as $oWhereis) {
            $oDocumento = $this->documentoRepository->findById((int) $oWhereis->getId_doc());
            $oTipoDoc = $oDocumento === null ? null : $this->tipoDocRepository->findById((int) $oDocumento->getId_tipo_doc());
            if ($oDocumento === null || $oTipoDoc === null) {
                continue;
            }
            $nombre = $oTipoDoc->getSigla() . ' ' . $oTipoDoc->getNom_doc();
            $valores[] = ['sel' => $oWhereis->getId_item_whereis(), 1 => $nombre, 2 => $oDocumento->getIdentificador()];
            $tipos[] = $nombre;
        }
        if ($valores !== []) {
            array_multisort($tipos, SORT_ASC, $valores);
        }
        $idGrupo = (int) $oEgm->getId_grupo();
        $idEquipaje = (int) $oEgm->getId_equipaje();

        return [
            'a_valores' => $valores,
            'nombre_valija' => $oLugar->getNom_lugar(),
            'ctx_del' => HashB::sign('equipajes_del_doc', ['id_item_egm' => $idItemEgm, 'id_grupo' => $idGrupo, 'id_equipaje' => $idEquipaje]),
            'ctx_add' => HashB::sign('equipajes_add_doc', ['id_item_egm' => $idItemEgm]),
            'ctx_eliminar_grupo' => HashB::sign('equipajes_eliminar_grupo', ['id_grupo' => $idGrupo, 'id_equipaje' => $idEquipaje]),
        ];
    }
}
