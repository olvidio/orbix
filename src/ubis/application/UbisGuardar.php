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
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

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
        $objPau = input_string($input, 'obj_pau');

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

        $oUbi = $repo->findById(input_int($input, 'id_ubi'));
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
        $idUbi = input_int($input, 'id_ubi');
        $oUbi = $repo->findById($idUbi);
        if ($oUbi === null) {
            $oUbi = new Casa();
            $id = $repo->getNewId();
            $oUbi->setId_auto($id);
            $oUbi->setId_ubi($repo->getNewIdUbi($id));
        }

        $oUbi->setTipo_casa(input_string($input, 'tipo_casa'));
        $oUbi->setPlazas(input_int($input, 'plazas'));
        $oUbi->setPlazas_min(input_int($input, 'plazas_min'));
        $oUbi->setNum_sacd(input_int($input, 'num_sacd'));
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

        $idUbi = input_int($input, 'id_ubi');
        $oUbi = $repo->findById($idUbi);
        $active = input_string($input, 'active');
        $sv = input_string($input, 'sv');
        $sf = input_string($input, 'sf');
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
        $oUbi->setN_buzon(input_int($input, 'n_buzon'));
        $oUbi->setNum_pi(input_int($input, 'num_pi'));
        $oUbi->setNum_cartas(input_int($input, 'num_cartas'));
        $oUbi->setObserv(input_string($input, 'observ'));
        $oUbi->setNum_habit_indiv(input_int($input, 'num_habit_indiv'));
        $oUbi->setPlazas(input_int($input, 'plazas'));
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

        $idUbi = input_int($input, 'id_ubi');
        $oUbi = $repo->findById($idUbi);
        $active = input_string($input, 'active');
        $sv = input_string($input, 'sv');
        $sf = input_string($input, 'sf');
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

        $oUbi = $repo->findById(input_int($input, 'id_ubi'));
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
        $oUbi->setTipo_ctr(input_string($input, 'tipo_ctr'));
        $oUbi->setCdc(is_true(input_string($input, 'cdc')));
        $oUbi->setId_ctr_padre(input_int($input, 'id_ctr_padre'));
        if ($oUbi instanceof CentroDl) {
            $oUbi->setNum_cartas_mensuales(input_int($input, 'num_cartas_mensuales'));
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
        $oUbi->setTipo_ubi(input_string($input, 'tipo_ubi'));
        $oUbi->setNombre_ubi(input_string($input, 'nombre_ubi'));
        $oUbi->setDl(input_string($input, 'dl'));
        $oUbi->setRegion(input_string($input, 'region'));
        $oUbi->setActive(is_true($active ?? input_string($input, 'active')));

        $svVal = $sv ?? input_string($input, 'sv');
        $sfVal = $sf ?? input_string($input, 'sf');
        if ($oUbi instanceof Casa || $oUbi instanceof CentroDl || $oUbi instanceof CentroEx) {
            $oUbi->setSv(is_true($svVal));
            $oUbi->setSf(is_true($sfVal));
        }
    }
}
