<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroDl;
use function core\is_true;

final class UbisGuardar
{
    use ProvidesRepositories;

    public function execute(array $input): string
    {
        $objPau = (string)($input['obj_pau'] ?? '');
        $idUbi = (int)($input['id_ubi'] ?? 0);
        $tipoUbi = (string)($input['tipo_ubi'] ?? '');
        $dl = (string)($input['dl'] ?? '');
        $region = (string)($input['region'] ?? '');
        $active = (string)($input['active'] ?? '');
        $nombreUbi = (string)($input['nombre_ubi'] ?? '');
        $sv = (string)($input['sv'] ?? '');
        $sf = (string)($input['sf'] ?? '');

        $repo = $this->getRepository($objPau);
        $oUbi = $repo->findById($idUbi);

        if ($objPau === 'CasaDl' || $objPau === 'CasaEx') {
            $tipoCasa = (string)($input['tipo_casa'] ?? '');
            $plazas = (int)($input['plazas'] ?? 0);
            $plazasMin = (int)($input['plazas_min'] ?? 0);
            $numSacd = (int)($input['num_sacd'] ?? 0);
            if (empty($oUbi)) {
                $oUbi = new Casa();
                $id = $repo->getNewId();
                $oUbi->setId_auto($id);
                $oUbi->setId_ubi($repo->getNewIdUbi($id));
            }
            $oUbi->setTipo_casa($tipoCasa);
            $oUbi->setPlazas($plazas);
            $oUbi->setPlazas_min($plazasMin);
            $oUbi->setNum_sacd($numSacd);
        }

        if ($objPau === 'CentroDl' || $objPau === 'CentroEx') {
            $tipoCtr = (string)($input['tipo_ctr'] ?? '');
            $aTipoLabor = (array)($input['tipo_labor'] ?? []);
            $cdc = (string)($input['cdc'] ?? '');
            $idCtrPadre = (int)($input['id_ctr_padre'] ?? 0);
            $nBuzon = (int)($input['n_buzon'] ?? 0);
            $numPi = (int)($input['num_pi'] ?? 0);
            $numCartas = (int)($input['num_cartas'] ?? 0);
            $observ = (string)($input['observ'] ?? '');
            $numHabitIndiv = (int)($input['num_habit_indiv'] ?? 0);
            $plazas = (int)($input['plazas'] ?? 0);
            $numCartasMensuales = (int)($input['num_cartas_mensuales'] ?? 0);
            if (empty($oUbi)) {
                $oUbi = $objPau === 'CentroDl' ? new CentroDl() : new Centro();
                $id = $repo->getNewId();
                $oUbi->setId_auto($id);
                $oUbi->setId_ubi($repo->getNewIdUbi($id));
                $active = 'true';
                $sv = ConfigGlobal::mi_sfsv() === 1 ? 'true' : '';
                $sf = ConfigGlobal::mi_sfsv() === 2 ? 'true' : '';
            }
            $oUbi->setTipo_ctr($tipoCtr);
            $oUbi->setCdc($cdc);
            $oUbi->setId_ctr_padre($idCtrPadre);
            if ($objPau === 'CentroDl') {
                $oUbi->setN_buzon($nBuzon);
                $oUbi->setNum_pi($numPi);
                $oUbi->setNum_cartas($numCartas);
                $oUbi->setObserv($observ);
                $oUbi->setNum_habit_indiv($numHabitIndiv);
                $oUbi->setPlazas($plazas);
            }
            $oUbi->setNum_cartas_mensuales($numCartasMensuales);
            if (!empty($aTipoLabor)) {
                $valor = 0;
                foreach ($aTipoLabor as $bit) {
                    $valor += (int)$bit;
                }
                $oUbi->setTipo_labor($valor);
            }
        }

        $oUbi->setTipo_ubi($tipoUbi);
        $oUbi->setNombre_ubi($nombreUbi);
        $oUbi->setDl($dl);
        $oUbi->setRegion($region);
        $oUbi->setActive(is_true($active));
        if (method_exists($oUbi, 'setSv')) {
            $oUbi->setSv(is_true($sv));
        }
        if (method_exists($oUbi, 'setSf')) {
            $oUbi->setSf(is_true($sf));
        }

        if ($repo->Guardar($oUbi) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $repo->getErrorTxt();
        }
        return '';
    }
}
