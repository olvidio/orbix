<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\criteria\EgmField as EgmF;
use src\inventario\domain\criteria\WhereisField as WhF;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Whereis;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Cambios en las maletas (grupos, «egm») de un equipaje: guardar un grupo con sus documentos
 * (`equipajes_update_grupo`), añadir documentos a una maleta (`equipajes_add_doc`), quitarlos
 * (`equipajes_del_doc`) y eliminar el grupo (`equipajes_eliminar_grupo`).
 *
 * Sucesor de la lógica de esos cuatro controladores (fase 5, paso 5.2). Corrige:
 * - al quitar documentos no se comprobaba que la fila fuera de la maleta de la cápsula: con la cápsula
 *   de una maleta se podían quitar documentos de otra;
 * - al añadir no se miraba si el documento ya estaba en la maleta, y se duplicaba.
 */
final class EquipajeMaletas
{
    public function __construct(
        private EgmRepositoryInterface $egmRepository,
        private WhereisRepositoryInterface $whereisRepository,
    ) {
    }

    /**
     * Crea la maleta (equipaje + grupo + lugar) si no existe y le añade los documentos.
     *
     * @param list<mixed> $idsDoc
     * @return array{error: string, id_item_egm: int}
     */
    public function guardarGrupo(int $idEquipaje, int $idGrupo, int $idLugar, array $idsDoc): array
    {
        $error = '';
        $cEgm = $this->egmRepository->matching(Criteria::create()
            ->where(Filter::eq(EgmF::IdEquipaje, $idEquipaje))
            ->andWhere(Filter::eq(EgmF::IdGrupo, $idGrupo))
            ->andWhere(Filter::eq(EgmF::IdLugar, $idLugar)));
        if ($cEgm === []) {
            $idItemEgm = $this->egmRepository->getNewId();
            $oEgm = new Egm();
            $oEgm->setId_item($idItemEgm);
            $oEgm->setId_equipaje($idEquipaje);
            $oEgm->setId_grupo($idGrupo);
            $oEgm->setId_lugar($idLugar);
            if ($this->egmRepository->Guardar($oEgm) === false) {
                $error .= _("hay un error, no se ha guardado") . "\n" . $this->egmRepository->getErrorTxt();
            }
        } else {
            $idItemEgm = $cEgm[0]->getId_item();
        }

        return ['error' => $error . $this->anyadirDocumentos($idItemEgm, $idsDoc), 'id_item_egm' => $idItemEgm];
    }

    /**
     * @param list<mixed> $idsDoc
     * @return string texto de error ('' si todo fue bien)
     */
    public function anyadirDocumentos(int $idItemEgm, array $idsDoc): string
    {
        $error = '';
        foreach ($idsDoc as $raw) {
            if (!is_numeric($raw)) {
                continue;
            }
            $yaEsta = $this->whereisRepository->matching(Criteria::create()
                ->where(Filter::eq(WhF::IdDoc, (int) $raw))
                ->andWhere(Filter::eq(WhF::IdItemEgm, $idItemEgm)));
            if ($yaEsta !== []) {
                continue;
            }
            $oWhereis = new Whereis();
            $oWhereis->setId_item_whereis($this->whereisRepository->getNewId());
            $oWhereis->setId_item_egm($idItemEgm);
            $oWhereis->setId_doc((int) $raw);
            if ($this->whereisRepository->Guardar($oWhereis) === false) {
                $error .= _("hay un error, no se ha guardado") . "\n" . $this->whereisRepository->getErrorTxt();
            }
        }

        return $error;
    }

    /**
     * Quita de la maleta las filas indicadas; las que no son de esa maleta se ignoran.
     *
     * @param list<mixed> $idsItemWhereis
     * @return string texto de error ('' si todo fue bien)
     */
    public function quitarDocumentos(int $idItemEgm, array $idsItemWhereis): string
    {
        $error = '';
        foreach ($idsItemWhereis as $raw) {
            $oWhereis = is_numeric($raw) ? $this->whereisRepository->findById((int) $raw) : null;
            if ($oWhereis === null || $oWhereis->getId_item_egm() !== $idItemEgm) {
                continue;
            }
            if ($this->whereisRepository->Eliminar($oWhereis) === false) {
                $error .= _("hay un error, no se ha eliminado") . "\n" . $this->whereisRepository->getErrorTxt();
            }
        }

        return $error;
    }

    /**
     * Elimina la maleta del grupo (sus documentos los quita la BD por la clave foránea).
     *
     * @return string texto de error ('' si todo fue bien)
     */
    public function eliminarGrupo(int $idEquipaje, int $idGrupo): string
    {
        $cEgm = $this->egmRepository->matching(Criteria::create()
            ->where(Filter::eq(EgmF::IdEquipaje, $idEquipaje))
            ->andWhere(Filter::eq(EgmF::IdGrupo, $idGrupo)));
        if ($cEgm !== [] && $this->egmRepository->Eliminar($cEgm[0]) === false) {
            return _("hay un error, no se ha eliminado") . "\n" . $this->egmRepository->getErrorTxt();
        }

        return '';
    }
}
