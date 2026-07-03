<?php

namespace src\configuracion\application;

use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModsReq;
use src\configuracion\domain\value_objects\ModuloDescription;
use src\configuracion\domain\value_objects\ModuloId;
use src\configuracion\domain\value_objects\ModuloName;

/**
 * Alta / baja / modificación de módulos (respuesta texto plano para AJAX legacy).
 */
final class ModulosUpdateAction
{
    public function __construct(
        private ModuloRepositoryInterface $moduloRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     */
    public function execute(array $post): string
    {
        $a_sel = isset($post['sel']) && is_array($post['sel']) ? $post['sel'] : [];

        $Qmod = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'mod');
        $Qid_mod = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_mod');

        if ($a_sel !== []) {
            $firstSel = $a_sel[0] ?? '';
            $selString = is_scalar($firstSel) ? (string)$firstSel : '';
            $decoded = urldecode(strtok($selString, '#') ?: '');
            $Qid_mod = (int)$decoded;
        }

        $Qnom = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'nom');
        $Qdescripcion = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'descripcion');
        $Qsel_mods = $this->parseIntList($post, 'sel_mods');
        $Qsel_apps = $this->parseIntList($post, 'sel_apps');

        switch ($Qmod) {
            case 'nuevo':
                if ($Qnom !== '') {
                    $newId = $this->moduloRepository->getNewId();
                    $oModulo = new Modulo();
                    $oModulo->setIdModVo(new ModuloId((int)$newId));
                    $oModulo->setNomVo(ModuloName::fromString($Qnom));
                    $oModulo->setDescripcionVo(ModuloDescription::fromNullableString($Qdescripcion));
                    $oModulo->setModsReqVo(ModsReq::fromNullableArray($Qsel_mods));
                    $oModulo->setAppsReqVo(AppsReq::fromNullableArray($Qsel_apps));
                    $this->moduloRepository->Guardar($oModulo);
                }
                return '';

            case 'eliminar':
                $oModulo = $this->moduloRepository->findById($Qid_mod);
                if ($oModulo === null) {
                    return '';
                }
                if ($this->moduloRepository->Eliminar($oModulo) === false) {
                    return _("hay un error, no se ha eliminado") . "\n" . $this->moduloRepository->getErrorTxt();
                }
                return '';

            default:
                $oModulo = $this->moduloRepository->findById($Qid_mod);
                if ($oModulo !== null) {
                    $oModulo->setNomVo(ModuloName::fromString($Qnom));
                    $oModulo->setDescripcionVo(ModuloDescription::fromNullableString($Qdescripcion));
                    $oModulo->setModsReqVo(ModsReq::fromNullableArray($Qsel_mods));
                    $oModulo->setAppsReqVo(AppsReq::fromNullableArray($Qsel_apps));
                    $this->moduloRepository->Guardar($oModulo);
                }
                return '';
        }
    }

    /**
     * @param array<string, mixed> $post
     * @return list<int>|null
     */
    private function parseIntList(array $post, string $key): ?array
    {
        if (!isset($post[$key]) || !is_array($post[$key])) {
            return null;
        }
        $result = [];
        foreach ($post[$key] as $value) {
            if (is_int($value)) {
                $result[] = $value;
            } elseif (is_string($value) && is_numeric($value)) {
                $result[] = (int)$value;
            }
        }

        return $result === [] ? null : $result;
    }
}
