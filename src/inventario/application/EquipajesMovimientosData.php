<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\inventario\domain\criteria\EgmField as EgmF;
use src\inventario\domain\criteria\WhereisField as WhF;
use src\inventario\domain\entity\TipoDoc;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Movimientos de documentos para preparar los equipajes (`equipajes_movimientos`): por cada maleta
 * (egm) de cada equipaje, qué documentos hay que **quitar** (son de ese lugar pero no van en la
 * maleta) y cuáles hay que **poner** (van en la maleta pero son de otro lugar, que se indica).
 *
 * Sucesor de la lógica del controlador `equipajes_movimientos.php` (fase 5, paso 5.2). Corrige el
 * recuento: el contador de ejemplares y la lista de identificadores eran variables comunes a todos los
 * tipos de documento (y se arrastraban de «quitar» a «poner»), así que con tipos intercalados salían
 * cuentas e identificadores mezclados. Ahora se cuenta por tipo (y por lugar de origen al poner).
 * También faltaba el espacio antes del identificador o del lugar cuando había un solo ejemplar.
 */
final class EquipajesMovimientosData
{
    /** @var array<int, TipoDoc|null> */
    private array $tipos = [];

    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
        private EgmRepositoryInterface $egmRepository,
        private WhereisRepositoryInterface $whereisRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /**
     * @param list<mixed> $idsEquipaje
     * @return array{aCambios: array<int, array<int, array{out?: array<int, string>, in?: array<string, string>}>>, aLugaresPorEgm: array<int, string>, aNomEquipajes: array<int, string>}
     */
    public function execute(array $idsEquipaje): array
    {
        $aCambios = [];
        $aLugaresPorEgm = [];
        $aNomEquipajes = [];
        foreach ($idsEquipaje as $raw) {
            if (!is_numeric($raw)) {
                continue;
            }
            $idEquipaje = (int) $raw;
            $oEquipaje = $this->equipajeRepository->findById($idEquipaje);
            if ($oEquipaje === null) {
                continue;
            }
            $aNomEquipajes[$idEquipaje] = (string) $oEquipaje->getNom_equipaje();
            $cEgm = $this->egmRepository->matching(Criteria::create()->where(Filter::eq(EgmF::IdEquipaje, $idEquipaje)));
            foreach ($cEgm as $oEgm) {
                $idItemEgm = $oEgm->getId_item();
                $idLugar = $oEgm->getId_lugar();
                $oLugar = $this->lugarRepository->findById((int) $idLugar);
                if ($oLugar === null) {
                    continue;
                }
                $aLugaresPorEgm[$idItemEgm] = $oLugar->getNom_lugar();

                $quitar = $this->quitar((int) $idLugar, $idItemEgm);
                if ($quitar !== []) {
                    $aCambios[$idEquipaje][$idItemEgm]['out'] = $quitar;
                }
                $poner = $this->poner((int) $idLugar, $idItemEgm);
                if ($poner !== []) {
                    $aCambios[$idEquipaje][$idItemEgm]['in'] = $poner;
                }
            }
        }

        return ['aCambios' => $aCambios, 'aLugaresPorEgm' => $aLugaresPorEgm, 'aNomEquipajes' => $aNomEquipajes];
    }

    /**
     * Documentos del lugar de la maleta que no están en ella, agrupados por tipo.
     *
     * @return array<int, string> id_tipo_doc => texto
     */
    private function quitar(int $idLugar, int $idItemEgm): array
    {
        $grupos = [];
        $cDocumentos = $this->documentoRepository->matching(
            Criteria::create()->where(Filter::eq(DocF::IdLugar, $idLugar))->orderBy(DocF::IdDoc)
        );
        foreach ($cDocumentos as $oDocumento) {
            $cWhereis = $this->whereisRepository->matching(Criteria::create()
                ->where(Filter::eq(WhF::IdDoc, $oDocumento->getId_doc()))
                ->andWhere(Filter::eq(WhF::IdItemEgm, $idItemEgm)));
            if ($cWhereis !== []) {
                continue; // está en su sitio
            }
            $idTipo = (int) $oDocumento->getId_tipo_doc();
            $oTipo = $this->tipo($idTipo);
            if ($oTipo === null) {
                continue;
            }
            $grupos[$idTipo]['tipo'] = $oTipo;
            $grupos[$idTipo]['n'] = ($grupos[$idTipo]['n'] ?? 0) + 1;
            $grupos[$idTipo]['ids'][] = (string) $oDocumento->getIdentificador();
        }

        $out = [];
        foreach ($grupos as $idTipo => $g) {
            $oTipo = $g['tipo'];
            $nombre = trim($oTipo->getSigla() . ' ' . $oTipo->getNom_doc());
            $ids = implode(',', array_filter($g['ids'], static fn (string $i): bool => $i !== ''));
            $out[$idTipo] = $g['n'] === 1
                ? $nombre . ($ids === '' ? '' : " ($ids)")
                : $g['n'] . ' ' . _("ejemplares de") . ' ' . $nombre . ($ids === '' ? '' : " ($ids)");
        }

        return $out;
    }

    /**
     * Documentos de la maleta que son de otro lugar, agrupados por tipo y lugar de origen.
     *
     * @return array<string, string> «id_tipo_doc#id_lugar_de_la_maleta#id_lugar_de_origen» => texto
     */
    private function poner(int $idLugar, int $idItemEgm): array
    {
        $grupos = [];
        $cWhereis = $this->whereisRepository->matching(Criteria::create()->where(Filter::eq(WhF::IdItemEgm, $idItemEgm)));
        foreach ($cWhereis as $oWhereis) {
            $oDocumento = $this->documentoRepository->findById((int) $oWhereis->getId_doc());
            if ($oDocumento === null) {
                continue;
            }
            $idLugarDoc = $oDocumento->getId_lugar();
            if ($idLugarDoc === null || $idLugarDoc === $idLugar) {
                continue;
            }
            $oLugar = $this->lugarRepository->findById($idLugarDoc);
            $oTipo = $this->tipo((int) $oDocumento->getId_tipo_doc());
            if ($oLugar === null || $oTipo === null) {
                continue;
            }
            $idTipo = (int) $oDocumento->getId_tipo_doc();
            // Antes la clave era «tipo#lugar de la maleta» y juntaba documentos de distintos lugares
            // de origen bajo el nombre del primero; ahora se separan por lugar de origen.
            $clave = $idTipo . '#' . $idLugar . '#' . $idLugarDoc;
            $grupos[$clave]['tipo'] = $oTipo;
            $grupos[$clave]['lugar'] = $oLugar->getNom_lugar();
            $grupos[$clave]['n'] = ($grupos[$clave]['n'] ?? 0) + 1;
            $grupos[$clave]['ids'][] = (string) $oDocumento->getIdentificador();
        }

        $out = [];
        foreach ($grupos as $clave => $g) {
            $oTipo = $g['tipo'];
            $nombre = trim($oTipo->getSigla() . ' ' . $oTipo->getNom_doc());
            $ids = implode(',', array_filter($g['ids'], static fn (string $i): bool => $i !== ''));
            $de = _("de") . ' ' . $g['lugar'];
            $out[$clave] = $g['n'] === 1
                ? $nombre . ' ' . ($ids === '' ? $de : "$de: ($ids)")
                : $g['n'] . ' ' . _("ejemplares de") . ' ' . $nombre . ($ids === '' ? " [$de]" : " [$de: ($ids)]");
        }

        return $out;
    }

    private function tipo(int $idTipo): ?TipoDoc
    {
        if (!array_key_exists($idTipo, $this->tipos)) {
            $this->tipos[$idTipo] = $this->tipoDocRepository->findById($idTipo);
        }

        return $this->tipos[$idTipo];
    }
}
