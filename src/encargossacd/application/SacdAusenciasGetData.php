<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoField;
use src\encargossacd\domain\criteria\EncargoSacdField;
use src\encargossacd\domain\criteria\EncargoSacdHorarioField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\security\HashB;

/**
 * Datos para la ficha de ausencias de un SACD
 * (`frontend/encargossacd/controller/sacd_ausencias_get.php`).
 *
 * Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo
 * 7/4) y las filas asociadas al SACD. Con `historial=1` incluye todas las
 * ausencias; sin historial solo muestra las que aun tienen vigencia.
 */
final class SacdAusenciasGetData
{

    public function __construct(
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository
    ) {
    }

    /**
     * @return array{
     *     array_tipo_ausencias: array<int, string>,
     *     filas: list<array{
     *         id_enc: int,
     *         id_tipo_enc: int,
     *         desc_enc: string,
     *         id_item: int,
     *         inicio: ?string,
     *         fin: ?string,
     *         dedic_m: string,
     *         dedic_t: string,
     *         dedic_v: string
     *     }>
     * }
     */
    public function execute(int $id_nom, int $historial): array
    {
        $hoy = date('Y-m-d');

        $cEncargos = $this->encargoRepository->matching(
            Criteria::create()
                ->where(Filter::matches(EncargoField::IdTipoEnc, '(7|4)...'))
                ->orderBy(EncargoField::IdTipoEnc)
        );
        $array_tipo_ausencias = [];
        if ($cEncargos !== []) {
            foreach ($cEncargos as $oEncargo) {
                $array_tipo_ausencias[(int)$oEncargo->getId_enc()] = (string)$oEncargo->getDesc_enc();
            }
        }

        $criterioP = Criteria::create()
            ->where(Filter::eq(EncargoSacdField::IdNom, $id_nom))
            ->orderBy(EncargoSacdField::FIni);
        if ($historial !== 1) {
            $criterioP = $criterioP->andWhere(Filter::gte(EncargoSacdField::FIni, $hoy));
        }
        $cEncargosSacd = $this->encargoSacdRepository->matching($criterioP);

        $filas = [];
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $id_enc = (int)$oEncargoSacd->getId_enc();
            $oEncargo = $this->encargoRepository->findById($id_enc);
            if ($oEncargo === null) {
                continue;
            }
            $id_tipo_enc = (string)$oEncargo->getId_tipo_enc();
            if (!preg_match('/[74]/', $id_tipo_enc)) {
                continue;
            }

            $cHorarios = $this->cargarHorarios(
                $this->encargoSacdHorarioRepository,
                $id_enc,
                $id_nom,
                $historial,
                $hoy,
            );

            $dedic_m = '';
            $dedic_t = '';
            $dedic_v = '';
            foreach ($cHorarios as $oHorario) {
                switch ((string)$oHorario->getDia_ref()) {
                    case 'm':
                        $dedic_m = (string)$oHorario->getDia_inc();
                        break;
                    case 't':
                        $dedic_t = (string)$oHorario->getDia_inc();
                        break;
                    case 'v':
                        $dedic_v = (string)$oHorario->getDia_inc();
                        break;
                }
            }

            $f_ini = $oEncargoSacd->getF_ini();
            $f_fin = $oEncargoSacd->getF_fin();
            $filas[] = [
                'id_enc' => $id_enc,
                'id_tipo_enc' => (int)$id_tipo_enc,
                'desc_enc' => (string)$oEncargo->getDesc_enc(),
                'id_item' => (int)$oEncargoSacd->getId_item(),
                'inicio' => empty($f_ini) ? null : (string)$f_ini->getFromLocal(),
                'fin' => empty($f_fin) ? null : (string)$f_fin->getFromLocal(),
                'dedic_m' => $dedic_m,
                'dedic_t' => $dedic_t,
                'dedic_v' => $dedic_v,
            ];
        }

        return [
            'array_tipo_ausencias' => $array_tipo_ausencias,
            'filas' => $filas,
            'ctx_guardar' => HashB::sign('sacd_ausencias_update', ['id_nom' => $id_nom]),
        ];
    }

    /**
     * @return list<EncargoSacdHorario>
     */
    private function cargarHorarios(
        EncargoSacdHorarioRepositoryInterface $repo,
        int $id_enc,
        int $id_nom,
        int $historial,
        string $hoy,
    ): array {
        $criterio = Criteria::create()
            ->where(Filter::eq(EncargoSacdHorarioField::IdEnc, $id_enc))
            ->andWhere(Filter::eq(EncargoSacdHorarioField::IdNom, $id_nom));
        if ($historial === 1) {
            return $repo->matching($criterio);
        }

        $c1 = $repo->matching($criterio->andWhere(Filter::gt(EncargoSacdHorarioField::FFin, $hoy)));
        $c2 = $repo->matching($criterio->andWhere(Filter::isNull(EncargoSacdHorarioField::FFin)));

        return array_merge($c1, $c2);
    }
}
