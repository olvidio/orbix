<?php

namespace src\encargossacd\application;

use src\shared\config\ConfigGlobal;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoField;
use src\encargossacd\domain\criteria\EncargoSacdField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\criteria\CentroDlField;
use src\ubis\domain\criteria\CentroEllasField;

/**
 * Datos para la comunicacion a los centros.
 * Sustituye la logica de `frontend/encargossacd/controller/listas_com_ctr.php`.
 *
 * El modelo de salida replica el consumido por la vista
 * `listas_com_ctr.phtml`:
 *  - `array_atn_sacd[nombre_ubi]` con titular, suplente, colaboradores y
 *    el texto de comunicacion traducido al idioma del idioma actual.
 *  - `origen_txt` cabecera de emisor y `lugar_fecha` pie.
 */
final class ListasComCtrData
{

    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private PersonaDlRepositoryInterface $personaDlRepository
    ) {
    }

    /**
     * @return array{
     *     array_atn_sacd: array<string, array<string, mixed>>,
     *     origen_txt: string,
     *     lugar_fecha: string
     * }
     */
    public function execute(string $sfsv): array
    {
        $oService = $this->aplicacionService;

        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $poblacion = $oService->getLugar_dl();
        $lugar_fecha = "$poblacion, $hoy_local";

        $origen_txt = '';
        $cCentros = [];
        switch ($sfsv) {
            case 'sv':
                $cCentros = $this->centroDlRepository->matching(
                    Criteria::create()
                        ->where(Filter::eq(CentroDlField::Active, true))
                        ->orderBy(CentroDlField::TipoCtr)
                        ->addOrderBy(CentroDlField::NombreUbi)
                );
                $origen_txt = (string)ConfigGlobal::mi_dele();
                break;
            case 'sf':
                $cCentros = $this->centroEllasRepository->matching(
                    Criteria::create()
                        ->where(Filter::eq(CentroEllasField::Active, true))
                        ->orderBy(CentroEllasField::TipoCtr)
                        ->addOrderBy(CentroEllasField::NombreUbi)
                );
                $origen_txt = ConfigGlobal::mi_dele() . 'f';
                break;
        }

        $array_atn_sacd = [];
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = (string)$oCentro->getNombre_ubi();

            $cEncargos = $this->encargoRepository->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoField::IdUbi, $id_ubi))
                    ->andWhere(Filter::matches(EncargoField::IdTipoEnc, '(1|2|3).00'))
            );
            if (count($cEncargos) === 0) {
                continue;
            }
            if (count($cEncargos) !== 1) {
                continue;
            }
            $id_enc = $cEncargos[0]->getId_enc();
            if (empty($id_enc)) {
                continue;
            }
            $cEncargosSacd = $this->encargoSacdRepository->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoSacdField::IdEnc, $id_enc))
                    ->andWhere(Filter::isNull(EncargoSacdField::FFin))
                    ->orderBy(EncargoSacdField::Modo)
            );

            $sacd_colaborador = [];
            $array_atn_sacd[$nombre_ubi] = [
                'titular' => '',
                'titular_dedicacion' => '',
                'suplente' => '',
                'colaborador' => [],
                'txt' => [],
            ];
            foreach ($cEncargosSacd as $oEncargoSacd) {
                $id_nom = $oEncargoSacd->getId_nom();
                $oPersona = $this->personaDlRepository->findById($id_nom);
                if ($oPersona === null) {
                    continue;
                }
                $modo = (int)$oEncargoSacd->getModo();
                switch ($modo) {
                    case 2:
                    case 3:
                        $array_atn_sacd[$nombre_ubi]['titular'] = $oPersona->getNombreApellidos();
                        $array_atn_sacd[$nombre_ubi]['titular_dedicacion'] = $oService->dedicacion($id_nom, $id_enc);
                        break;
                    case 4:
                        $array_atn_sacd[$nombre_ubi]['suplente'] = $oPersona->getNombreApellidos();
                        break;
                    case 5:
                        $sacd_colaborador[] = [
                            'nom' => $oPersona->getNombreApellidos(),
                            'dedicacion' => $oService->dedicacion($id_nom, $id_enc),
                        ];
                        break;
                }
            }
            $array_atn_sacd[$nombre_ubi]['colaborador'] = $sacd_colaborador;
            $array_atn_sacd[$nombre_ubi]['txt']['com_ctr'] = $oService->getTraduccion('com_ctr', '');
        }

        return [
            'array_atn_sacd' => $array_atn_sacd,
            'origen_txt' => $origen_txt,
            'lugar_fecha' => $lugar_fecha,
        ];
    }
}
