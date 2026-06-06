<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;

/**
 * Description of actividadlugar
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class ActividadLugar
{
    public function __construct(
        private CasaRepositoryInterface $casaRepository,
        private CentroRepositoryInterface $centroRepository,
    ) {
    }

    private int $isfsv = 0;
    private string $ssfsv = '';
    private int|string $opcion_sel = '';

    public function getFiltroLugar(int $id_ubi): string
    {
        $oCasa = $this->casaRepository->findById($id_ubi);
        if ($oCasa === null) {
            return '';
        }
        $dl = $oCasa->getDl() ?? '';
        $reg = $oCasa->getRegion() ?? '';

        if ($dl === '') {
            return 'r|' . $reg;
        }

        return 'dl|' . $dl;
    }

    /**
     * Devuelve el array de opciones (value => label) de casas y centros que
     * encajan con la entrada (dl|xxx, r|xxx, crXxx). El frontend es responsable
     * de construir el `<select>` a partir de este array.
     *
     * @return array<string|int, string>
     */
    public function getLugaresPosibles(string $Qentrada = ''): array
    {
        $donde_sfsv = '';
        if ($Qentrada === '') {
            return [];
        }

        $dl_r = strtok($Qentrada, '|');
        $reg = strtok('|');
        if ($reg === false) {
            return [];
        }
        $cr = substr($reg, 0, 2);
        if ($cr === 'cr') {
            $dl_r = 'r';
            $reg = substr($reg, 2);
        }
        $reg_no_f = (string) preg_replace('/(\.*)f$/', '\1', $reg);

        if ($this->ssfsv === 'sv') {
            $this->isfsv = 1;
        }
        if ($this->ssfsv === 'sf') {
            $this->isfsv = 2;
        }
        $isfsv = $this->isfsv === 0 ? ConfigGlobal::mi_sfsv() : $this->isfsv;
        switch ($isfsv) {
            case 1:
                $donde_sfsv = "AND sv='t' ";
                break;
            case 2:
                $donde_sfsv = "AND sf='t' ";
                break;
        }
        $donde = '';
        switch ($dl_r) {
            case 'dl':
                $donde = "WHERE dl='$reg_no_f' ";
                break;
            case 'r':
                $donde = "WHERE region='$reg_no_f' ";
                break;
        }
        $donde .= $donde_sfsv;

        if ($dl_r !== 'dl' && $dl_r !== 'r') {
            $donde = '';
        }
        if ($donde !== '') {
            $donde .= " AND active='t'";
        } else {
            $donde = "WHERE active='t'";
        }
        $oOpcionesCasas = $this->casaRepository->getArrayCasas($donde);

        $donde_ctr = '';
        switch ($dl_r) {
            case 'dl':
                $donde_ctr = "dl='$reg' ";
                break;
            case 'r':
                $donde_ctr = "region='$reg_no_f' ";
                break;
        }
        $donde_ctr .= $donde_sfsv;

        $oOpcionesCentros = $this->centroRepository->getArrayCentrosCdc($donde_ctr);

        return $oOpcionesCasas + $oOpcionesCentros;
    }

    public function setIsfsv(int $isfsv): void
    {
        $this->isfsv = $isfsv;
    }

    public function setSsfsv(string $ssfsv): void
    {
        $this->ssfsv = $ssfsv;
    }

    public function setOpcion_sel(int|string $opcion_sel): void
    {
        $this->opcion_sel = $opcion_sel;
    }

    public function getOpcion_sel(): int|string
    {
        return $this->opcion_sel;
    }
}
