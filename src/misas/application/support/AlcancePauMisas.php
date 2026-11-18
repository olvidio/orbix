<?php

declare(strict_types=1);

namespace src\misas\application\support;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoSacdField;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\FilterGroup;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\criteria\ZonaField;

/**
 * Qué sacds, centros y zonas del plan de misas puede pedir el usuario actual si su rol está limitado a una
 * persona o a un centro (`p-sacd`, `Centro sv` / `Centro sf`); `null` si no tiene límite (oficina, jefe de
 * calendario). Lo mismo que le ofrecen las pantallas de búsqueda (`BuscarPlanSacdData`,
 * `BuscarPlanCtrData`), para comprobar el id que llega del formulario. Revisión de seguridad del
 * 2026-11-18, punto 2.
 *
 * - p-sacd: él y, si es jefe de zona, los sacds de sus zonas; los centros de sus encargos (de tipo 1–3,
 *   vigentes) y los de sus zonas; sus zonas.
 * - centro: su centro; ningún sacd ni zona.
 */
class AlcancePauMisas
{
    /** @var array{rol: string, id: int}|false|null */
    private array|false|null $usuario = null;

    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly ZonaRepositoryInterface $zonaRepository,
        private readonly ZonaSacdRepositoryInterface $zonaSacdRepository,
        private readonly EncargoSacdRepositoryInterface $encargoSacdRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
        private readonly CentrosDeZona $centrosDeZona,
    ) {
    }

    /** @return list<int>|null */
    public function sacds(): ?array
    {
        $u = $this->usuarioPau();
        if ($u === null) {
            return null;
        }
        if ($u['rol'] !== 'sacd' || $u['id'] === 0) {
            return [];
        }
        $ids = [$u['id']];
        foreach ($this->zonasDeJefe($u['id']) as $idZona) {
            foreach ($this->zonaSacdRepository->getIdSacdsDeZona($idZona) as $idNom) {
                $ids[] = (int) $idNom;
            }
        }

        return array_values(array_unique($ids));
    }

    /** @return list<int>|null */
    public function centros(): ?array
    {
        $u = $this->usuarioPau();
        if ($u === null) {
            return null;
        }
        if ($u['id'] === 0) {
            return [];
        }
        if ($u['rol'] === 'ctr') {
            return [$u['id']];
        }
        $ids = [];
        foreach ($this->zonas() ?? [] as $idZona) {
            foreach ($this->centrosDeZona->idUbisDeZona($idZona) as $idUbi) {
                $ids[] = (int) $idUbi;
            }
        }
        array_push($ids, ...$this->centrosDeEncargos($u['id']));
        $ids = array_values(array_unique($ids));
        sort($ids);

        return $ids;
    }

    /** @return list<int>|null */
    public function zonas(): ?array
    {
        $u = $this->usuarioPau();
        if ($u === null) {
            return null;
        }
        if ($u['rol'] !== 'sacd' || $u['id'] === 0) {
            return [];
        }

        return array_map('intval', array_keys($this->zonaRepository->getArrayZonas($u['id'])));
    }

    /** @return list<int> zonas de las que es jefe */
    private function zonasDeJefe(int $idNom): array
    {
        $ids = [];
        foreach ($this->zonaRepository->matching(Criteria::create()->where(Filter::eq(ZonaField::IdNom, $idNom))) as $oZona) {
            $ids[] = $oZona->getId_zona();
        }

        return $ids;
    }

    /** @return list<int> centros de sus encargos vigentes de tipo 1–3 (como `BuscarPlanCtrData`) */
    private function centrosDeEncargos(int $idNom): array
    {
        $hoy = (new DateTimeLocal(date('Y-m-d')))->getIso();
        $cEncargosSacd = $this->encargoSacdRepository->matching(
            Criteria::create()
                ->where(Filter::eq(EncargoSacdField::IdNom, $idNom))
                ->andWhere(FilterGroup::anyOf(Filter::isNull(EncargoSacdField::FFin), Filter::gt(EncargoSacdField::FFin, $hoy)))
        );
        $ids = [];
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $oEncargo = $this->encargoRepository->findById($oEncargoSacd->getId_enc());
            if ($oEncargo === null || substr((string) $oEncargo->getId_tipo_enc(), 0, 1) > '3') {
                continue;
            }
            $idUbi = $oEncargo->getId_ubi();
            if ($idUbi !== null) {
                $ids[] = $idUbi;
            }
        }

        return $ids;
    }

    /** @return array{rol: string, id: int}|null rol `sacd` o `ctr` e id de su persona o centro; null si no está limitado */
    private function usuarioPau(): ?array
    {
        if ($this->usuario === null) {
            $this->usuario = $this->resolverUsuario() ?? false;
        }

        return $this->usuario === false ? null : $this->usuario;
    }

    /** @return array{rol: string, id: int}|null */
    private function resolverUsuario(): ?array
    {
        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return ['rol' => 'sacd', 'id' => 0];
        }
        $nombreRol = $this->roleRepository->getArrayRoles()[$oMiUsuario->getId_role()] ?? '';
        $rol = match ($nombreRol) {
            'p-sacd' => 'sacd',
            'Centro sv', 'Centro sf' => 'ctr',
            default => null,
        };
        if ($rol === null) {
            return null;
        }
        $oConfig = $_SESSION['oConfig'] ?? null;
        if ($rol === 'sacd' && is_object($oConfig) && method_exists($oConfig, 'is_jefeCalendario') && $oConfig->is_jefeCalendario()) {
            return null;
        }
        $csv = $oMiUsuario->getCsvIdPauAsString();

        return ['rol' => $rol, 'id' => is_numeric($csv) ? (int) $csv : 0];
    }
}
