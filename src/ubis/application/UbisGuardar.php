<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEx;
final class UbisGuardar
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $objPau = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'obj_pau');

        return match ($objPau) {
            'CasaDl' => $this->guardarCasaDl($input),
            'CasaEx' => $this->guardarCasaEx($input),
            'CentroDl' => $this->guardarCentroDl($input),
            'CentroEx' => $this->guardarCentroEx($input),
            'Casa' => $this->guardarCasa($input),
            'Centro' => $this->guardarCentro($input),
            default => throw new \InvalidArgumentException("obj_pau desconocido: $objPau"),
        };
    }

    /**
     * @param array<string, mixed> $input
     */
    private function guardarCasaDl(array $input): string
    {
        $repo = $this->ubiRepositoryResolver->getRepository('CasaDl');
        if (!$repo instanceof CasaDlRepositoryInterface) {
            return _('No existe la clase del ubi');
        }

        return $this->guardarCasaConRepo($repo, $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function guardarCasaEx(array $input): string
    {
        $repo = $this->ubiRepositoryResolver->getRepository('CasaEx');
        if (!$repo instanceof CasaExRepositoryInterface) {
            return _('No existe la clase del ubi');
        }

        return $this->guardarCasaConRepo($repo, $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function guardarCasa(array $input): string
    {
        $repo = $this->ubiRepositoryResolver->getRepository('Casa');
        if (!$repo instanceof CasaRepositoryInterface) {
            return _('No existe la clase del ubi');
        }

        $oUbi = $repo->findById(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi'));
        if ($oUbi === null) {
            return _('no se encuentra el ubi');
        }

        $this->aplicarCamposComunes($oUbi, $input);

        if ($repo->Guardar($oUbi) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $repo->getErrorTxt();
        }

        return '';
    }

    /**
     * @param array<string, mixed> $input
     */
    private function guardarCasaConRepo(CasaDlRepositoryInterface|CasaExRepositoryInterface $repo, array $input): string
    {
        $idUbi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $oUbi = $repo->findById($idUbi);
        if ($oUbi === null) {
            $oUbi = new Casa();
            $id = $repo->getNewId();
            $oUbi->setId_auto($id);
            $oUbi->setId_ubi($repo->getNewIdUbi($id));
        }

        $oUbi->setTipo_casa(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'tipo_casa'));
        $oUbi->setPlazas(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'plazas'));
        $oUbi->setPlazas_min(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'plazas_min'));
        $oUbi->setNum_sacd(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'num_sacd'));
        $this->aplicarCamposComunes($oUbi, $input);

        if ($repo->Guardar($oUbi) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $repo->getErrorTxt();
        }

        return '';
    }

    /**
     * @param array<string, mixed> $input
     */
    private function guardarCentroDl(array $input): string
    {
        $repo = $this->ubiRepositoryResolver->getRepository('CentroDl');
        if (!$repo instanceof CentroDlRepositoryInterface) {
            return _('No existe la clase del ubi');
        }

        $idUbi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $oUbi = $repo->findById($idUbi);
        $active = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'active');
        $sv = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sv');
        $sf = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sf');
        if ($oUbi === null) {
            $oUbi = new CentroDl();
            $id = $repo->getNewId();
            $oUbi->setId_auto($id);
            $oUbi->setId_ubi($repo->getNewIdUbi($id));
            $active = 'true';
            $sv = ConfigGlobal::mi_sfsv() === 1 ? 'true' : '';
            $sf = ConfigGlobal::mi_sfsv() === 2 ? 'true' : '';
        }

        $this->aplicarCamposCentro($oUbi, $input);
        $oUbi->setN_buzon(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'n_buzon'));
        $oUbi->setNum_pi(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'num_pi'));
        $oUbi->setNum_cartas(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'num_cartas'));
        $oUbi->setObserv(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ'));
        $oUbi->setNum_habit_indiv(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'num_habit_indiv'));
        $oUbi->setPlazas(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'plazas'));
        $this->aplicarCamposComunes($oUbi, $input, $active, $sv, $sf);

        if ($repo->Guardar($oUbi) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $repo->getErrorTxt();
        }

        return '';
    }

    /**
     * @param array<string, mixed> $input
     */
    private function guardarCentroEx(array $input): string
    {
        $repo = $this->ubiRepositoryResolver->getRepository('CentroEx');
        if (!$repo instanceof CentroExRepositoryInterface) {
            return _('No existe la clase del ubi');
        }

        $idUbi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $oUbi = $repo->findById($idUbi);
        $active = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'active');
        $sv = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sv');
        $sf = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sf');
        if ($oUbi === null) {
            $oUbi = new CentroEx();
            $id = $repo->getNewId();
            $oUbi->setId_auto($id);
            $oUbi->setId_ubi($repo->getNewIdUbi($id));
            $active = 'true';
            $sv = ConfigGlobal::mi_sfsv() === 1 ? 'true' : '';
            $sf = ConfigGlobal::mi_sfsv() === 2 ? 'true' : '';
        }

        $this->aplicarCamposCentro($oUbi, $input);
        $this->aplicarCamposComunes($oUbi, $input, $active, $sv, $sf);

        if ($repo->Guardar($oUbi) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $repo->getErrorTxt();
        }

        return '';
    }

    /**
     * @param array<string, mixed> $input
     */
    private function guardarCentro(array $input): string
    {
        $repo = $this->ubiRepositoryResolver->getRepository('Centro');
        if (!$repo instanceof CentroRepositoryInterface) {
            return _('No existe la clase del ubi');
        }

        $oUbi = $repo->findById(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi'));
        if ($oUbi === null) {
            return _('no se encuentra el ubi');
        }

        $this->aplicarCamposComunes($oUbi, $input);

        if ($repo->Guardar($oUbi) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $repo->getErrorTxt();
        }

        return '';
    }

    /**
     * @param array<string, mixed> $input
     */
    private function aplicarCamposCentro(CentroDl|CentroEx $oUbi, array $input): void
    {
        $oUbi->setTipo_ctr(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'tipo_ctr'));
        $oUbi->setCdc(\src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'cdc')));
        $oUbi->setId_ctr_padre(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ctr_padre'));
        if ($oUbi instanceof CentroDl) {
            $oUbi->setNum_cartas_mensuales(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'num_cartas_mensuales'));
        }

        $aTipoLabor = $input['tipo_labor'] ?? [];
        if (!is_array($aTipoLabor)) {
            $aTipoLabor = [];
        }
        if ($aTipoLabor !== []) {
            $valor = 0;
            foreach ($aTipoLabor as $bit) {
                $valor += is_numeric($bit) ? (int) $bit : 0;
            }
            $oUbi->setTipo_labor($valor);
        }
    }

    /**
     * @param array<string, mixed> $input
     */
    private function aplicarCamposComunes(
        Casa|Centro|CentroDl|CentroEx $oUbi,
        array $input,
        ?string $active = null,
        ?string $sv = null,
        ?string $sf = null,
    ): void {
        $oUbi->setTipo_ubi(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'tipo_ubi'));
        $oUbi->setNombre_ubi(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nombre_ubi'));
        $oUbi->setDl(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'dl'));
        $oUbi->setRegion(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'region'));
        $oUbi->setActive(\src\shared\domain\helpers\FuncTablasSupport::isTrue($active ?? \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'active')) ?? false);

        $svVal = $sv ?? \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sv');
        $sfVal = $sf ?? \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sf');
        if ($oUbi instanceof Casa || $oUbi instanceof CentroDl || $oUbi instanceof CentroEx) {
            $oUbi->setSv(\src\shared\domain\helpers\FuncTablasSupport::isTrue($svVal));
            $oUbi->setSf(\src\shared\domain\helpers\FuncTablasSupport::isTrue($sfVal));
        }
    }
}
