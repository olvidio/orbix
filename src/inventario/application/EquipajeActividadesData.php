<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\criteria\ActividadAllField;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\entity\Ubi;

/**
 * Actividades para los equipajes: las casas con actividades en un periodo
 * (`lista_casas_posibles_periodo`); las de una casa en un periodo, para elegir las del equipaje
 * (`equipajes_lista_activ_periodo`); el resumen de las elegidas para crear el equipaje
 * (`equipajes_lista_activ_sel`); y los nombres de las actividades de un equipaje ya creado
 * (`equipajes_lista_activ_equipaje`).
 *
 * Sucesor de la lógica de esos tres controladores (fase 5, paso 5.2). Corrige: una actividad del
 * equipaje que ya no existe se saltaba sin avisar (el aviso «No se encuentra la actividad» solo
 * saltaba si la actividad existía con el nombre vacío).
 */
final class EquipajeActividadesData
{
    public function __construct(
        private ActividadRepositoryInterface $actividadRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private EquipajeRepositoryInterface $equipajeRepository,
    ) {
    }

    /**
     * Actividades de mi dl y sección en la casa que se solapan con el rango (fechas ISO), por fecha.
     *
     * @return array{a_valores: array<int, array<int|string, mixed>>, nombre_ubi: string}
     */
    public function porPeriodo(int $idCdc, string $inicioIso, string $finIso): array
    {
        $criteria = Criteria::create()
            ->where(Filter::eq(ActividadAllField::IdUbi, $idCdc))
            ->andWhere(Filter::eq(ActividadAllField::DlOrg, ConfigGlobal::mi_dele()))
            ->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, '^' . ConfigGlobal::mi_sfsv()))
            ->andWhere(Filter::lte(ActividadAllField::FIni, $finIso))
            ->andWhere(Filter::gte(ActividadAllField::FFin, $inicioIso))
            ->andWhere(Filter::lt(ActividadAllField::Status, 4))
            ->orderBy(ActividadAllField::FIni);
        $valores = [];
        $a = 0;
        foreach ($this->actividadRepository->matching($criteria) as $oActividad) {
            $a++;
            $valores[$a] = [
                'sel' => ['id' => $oActividad->getId_activ(), 'select' => ''],
                1 => $oActividad->getF_ini()?->getFromLocal(),
                2 => $oActividad->getF_fin()?->getFromLocal(),
                3 => $oActividad->getNom_activ(),
                4 => $oActividad->getObservVo()?->value(),
            ];
        }

        return ['a_valores' => $valores, 'nombre_ubi' => self::nombreUbi($idCdc)];
    }

    /**
     * Casas con actividades de mi dl y sección que se solapan con el rango (fechas ISO), por nombre
     * (`lista_casas_posibles_periodo`).
     *
     * @return array{a_opciones: array<int, string>}
     */
    public function casasConActividades(string $inicioIso, string $finIso): array
    {
        $criteria = Criteria::create()
            ->where(Filter::eq(ActividadAllField::DlOrg, ConfigGlobal::mi_dele()))
            ->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, '^' . ConfigGlobal::mi_sfsv()))
            ->andWhere(Filter::lte(ActividadAllField::FIni, $finIso))
            ->andWhere(Filter::gte(ActividadAllField::FFin, $inicioIso))
            ->andWhere(Filter::lt(ActividadAllField::Status, 4));
        $casas = [];
        foreach ($this->actividadRepository->getUbis($criteria) as $idUbi) {
            if ($idUbi === null) {
                continue;
            }
            $casas[$idUbi] = Ubi::NewUbi($idUbi)?->getNombreUbiVo()?->value() ?? 'sin determinar';
        }
        asort($casas);

        return ['a_opciones' => $casas];
    }

    /**
     * Casa, primera fecha de inicio y última de fin de las actividades elegidas, sus ids y la cápsula
     * para crear el equipaje.
     *
     * @param list<mixed> $idsActiv
     * @return array{nombre_ubi: string, ini: string|false, fin: string|false, ids_activ: string, ctx_nuevo: string}
     */
    public function seleccion(array $idsActiv, int $idCdc): array
    {
        $ids = [];
        $inicios = [];
        $fines = [];
        foreach ($idsActiv as $raw) {
            if (!is_numeric($raw)) {
                continue;
            }
            $ids[] = (int) $raw;
            $oActividad = $this->actividadAllRepository->findById((int) $raw);
            $fIni = $oActividad?->getF_ini();
            $fFin = $oActividad?->getF_fin();
            if (!$fIni instanceof DateTimeLocal || !$fFin instanceof DateTimeLocal) {
                continue;
            }
            $inicios[$fIni->getIso()] = $fIni->getFromLocal();
            $fines[$fFin->getIso()] = $fFin->getFromLocal();
        }
        ksort($inicios);
        ksort($fines);

        return [
            'nombre_ubi' => self::nombreUbi($idCdc),
            'ini' => reset($inicios),
            'fin' => end($fines),
            'ids_activ' => implode(',', $ids),
            'ctx_nuevo' => HashB::sign('equipajes_nuevo_guardar', ['id_ubi_activ' => $idCdc]),
        ];
    }

    /**
     * @return array{a_actividades: array<int, string>}|array{}
     */
    public function deEquipaje(int $idEquipaje): array
    {
        $oEquipaje = $this->equipajeRepository->findById($idEquipaje);
        if ($oEquipaje === null) {
            return [];
        }
        $nombres = [];
        $a = 0;
        foreach (explode(',', $oEquipaje->getIds_activ() ?? '') as $raw) {
            if (!is_numeric($raw)) {
                continue;
            }
            $a++;
            $nombre = $this->actividadAllRepository->findById((int) $raw)?->getNom_activ();
            $nombres[$a] = empty($nombre)
                ? sprintf(_("OJO! No se encuentra la actividad con id: %s"), (int) $raw)
                : $nombre;
        }

        return ['a_actividades' => $nombres];
    }

    private static function nombreUbi(int $idCdc): string
    {
        $nombre = Ubi::NewUbi($idCdc)?->getNombreUbiVo()?->value();

        return empty($nombre) ? 'sin determinar' : $nombre;
    }
}
