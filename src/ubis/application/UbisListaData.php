<?php

namespace src\ubis\application;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\criteria\CasaField as CasaF;
use src\ubis\domain\criteria\CentroField as CentroF;
use src\permisos\domain\XPermisos;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;

class UbisListaData
{
    public function __construct(
        private CasaRepositoryInterface $casaRepository,
        private CentroRepositoryInterface $centroRepository,
    ) {
    }

    /**
     * @return array{a_cabeceras: list<string>, a_valores: list<array<string|int>>}
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(string $nombre_ubi): array
    {
        $miSfsv = ConfigGlobal::mi_sfsv();

        $criteriaCasa = Criteria::create();
        $criteriaCtr = Criteria::create();

        if ($nombre_ubi !== '') {
            $nom_ubi = str_replace('+', "\+", $nombre_ubi);
            $criteriaCasa = $criteriaCasa->andWhere(Filter::matchesUnaccented(CasaF::NombreUbi, $nom_ubi));
            $criteriaCtr = $criteriaCtr->andWhere(Filter::matchesUnaccented(CentroF::NombreUbi, $nom_ubi));
        }

        switch ($miSfsv) {
            case 1:
                $oPerm = $_SESSION['oPerm'] ?? null;
                $puedeVerSf = $oPerm instanceof XPermisos
                    && ($oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des'));
                if (!$puedeVerSf) {
                    $criteriaCasa = $criteriaCasa->andWhere(Filter::eq(CasaF::Sv, true));
                }
                break;
            case 2:
                $criteriaCasa = $criteriaCasa->andWhere(Filter::eq(CasaF::Sf, true));
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                return [
                    'error' => $err_switch,
                    'a_cabeceras' => [],
                    'a_valores' => [],
                ];
        }

        $criteriaCasa = $criteriaCasa->andWhere(Filter::eq(CasaF::Active, true));
        $criteriaCtr = $criteriaCtr
            ->andWhere(Filter::eq(CentroF::Active, true))
            ->andWhere(Filter::eq(CentroF::Cdc, true));

        $a_ubis = [];
        $CasaRepository = $this->casaRepository;
        $cCasas = $CasaRepository->matching($criteriaCasa);
        foreach ($cCasas as $oCasa) {
            $nom = $oCasa->getNombre_ubi();
            $a_ubis[$nom] = $oCasa;
        }

        $CentroRepository = $this->centroRepository;
        $cCtr = $CentroRepository->matching($criteriaCtr);
        foreach ($cCtr as $oCentro) {
            $nom = $oCentro->getNombre_ubi();
            $a_ubis[$nom] = $oCentro;
        }

        uksort($a_ubis, 'strnatcasecmp');

        $a_cabeceras = [
            ucfirst(_("nombre del centro")),
            _("tipo"),
            _("dl"),
            ucfirst(_("región")),
            ucfirst(_("dirección")),
            _("cp"),
            ucfirst(_("ciudad")),
        ];

        $a_valores = [];
        $i = 0;
        foreach ($a_ubis as $oUbi) {
            $i++;
            $id_ubi = $oUbi->getId_ubi();
            $nom_ubi = $oUbi->getNombre_ubi();
            $tipo_ubi = substr($oUbi->getTipo_ubi() ?? '', 0, 3);

            $row = [
                'sel' => $id_ubi,
                1 => $nom_ubi,
                2 => $tipo_ubi,
                3 => $oUbi->getDl(),
                4 => $oUbi->getRegion(),
            ];

            $cDirecciones = $oUbi->getDirecciones();
            if (!empty($cDirecciones)) {
                $oDireccion = $cDirecciones[0];
                $row[5] = $oDireccion->getDireccionVo()?->value() ?? '';
                $row[6] = $oDireccion->getC_p();
                $row[7] = $oDireccion->getPoblacion();
            } else {
                $row[5] = '';
                $row[6] = '';
                $row[7] = '';
            }
            $a_valores[] = $row;
        }

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }
}
