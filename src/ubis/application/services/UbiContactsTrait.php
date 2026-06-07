<?php

namespace src\ubis\application\services;

use src\shared\infrastructure\DependencyResolver;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;
use src\ubis\domain\entity\Direccion;

trait UbiContactsTrait
{
    abstract public function getId_ubi(): int;

    abstract public function getIdUbiVo(): object;

    /**
     * @return list<Direccion|null>
     */
    public function getDireccionesGral(string $ordre = 'principal DESC'): array
    {
        $aClassName = explode('\\', static::class);
        $childClassName = end($aClassName);

        $repoCasaDireccion = match ($childClassName) {
            'Centro' => DependencyResolver::get(RelacionCentroDireccionRepositoryInterface::class),
            'CentroDl' => DependencyResolver::get(RelacionCentroDlDireccionRepositoryInterface::class),
            'CentroEx' => DependencyResolver::get(RelacionCentroExDireccionRepositoryInterface::class),
            'Casa' => DependencyResolver::get(RelacionCasaDireccionRepositoryInterface::class),
            'CasaDl' => DependencyResolver::get(RelacionCasaDlDireccionRepositoryInterface::class),
            'CasaEx' => DependencyResolver::get(RelacionCasaExDireccionRepositoryInterface::class),
            default => null,
        };

        $repoDireccion = match ($childClassName) {
            'Centro' => DependencyResolver::get(DireccionCentroRepositoryInterface::class),
            'CentroDl' => DependencyResolver::get(DireccionCentroDlRepositoryInterface::class),
            'CentroEx' => DependencyResolver::get(DireccionCentroExRepositoryInterface::class),
            'Casa' => DependencyResolver::get(DireccionCasaRepositoryInterface::class),
            'CasaDl' => DependencyResolver::get(DireccionCasaDlRepositoryInterface::class),
            'CasaEx' => DependencyResolver::get(DireccionCasaExRepositoryInterface::class),
            default => null,
        };

        if ($repoCasaDireccion === null || $repoDireccion === null) {
            return [];
        }

        $idUbiValue = $this->getId_ubi();
        $cUbixDireccion = $repoCasaDireccion->getDireccionesPorUbi($idUbiValue);
        $dirs = [];
        if ($cUbixDireccion !== false) {
            foreach ($cUbixDireccion as $aUbixDireccion) {
                if (!isset($aUbixDireccion['id_direccion'])) {
                    continue;
                }
                $idDireccionRaw = $aUbixDireccion['id_direccion'];
                if (!is_int($idDireccionRaw) && !is_numeric($idDireccionRaw)) {
                    continue;
                }
                $id_direccion = is_int($idDireccionRaw) ? $idDireccionRaw : (int) $idDireccionRaw;
                $dirs[] = $repoDireccion->findById($id_direccion);
            }
        }

        return $dirs;
    }

    public function emailPrincipalOPrimero(int $desc_teleco = 13): string
    {
        $TelecoUbiRepository = $this->resolveTelecoRepository();
        if ($TelecoUbiRepository === null) {
            return '';
        }

        /** @var array<string, mixed> $aWhere */
        $aWhere = [
            'id_ubi' => $this->getId_ubi(),
            'id_tipo_teleco' => 3,
        ];

        if ($desc_teleco !== 13) {
            $aWhere['id_desc_teleco'] = $desc_teleco;
        }

        $cTelecos = $TelecoUbiRepository->getTelecos($aWhere);
        if ($cTelecos === []) {
            return '';
        }

        $oTeleco = $cTelecos[0];
        return $oTeleco->getNumTelecoVo()->value();
    }

    public function getTeleco(string $tipo_teleco, int|string $desc_teleco, string $separador = ''): string
    {
        $TelecoUbiRepository = $this->resolveTelecoRepository();
        if ($TelecoUbiRepository === null) {
            return '';
        }

        $id_tipo_teleco = match ($tipo_teleco) {
            'telf' => 1,
            'fax' => 4,
            'e-mail' => 3,
            default => 0,
        };

        /** @var array<string, mixed> $aWhere */
        $aWhere = [
            'id_ubi' => $this->getId_ubi(),
            'id_tipo_teleco' => $id_tipo_teleco,
        ];

        if ($desc_teleco !== '*' && $desc_teleco !== '' && $desc_teleco !== 0) {
            $aWhere['id_desc_teleco'] = $desc_teleco;
        }

        $cTelecos = $TelecoUbiRepository->getTelecos($aWhere);
        $tels = '';
        $separador = $separador === '' ? '.-<br>' : $separador;

        $DescTelecoRepository = DependencyResolver::get(DescTelecoRepositoryInterface::class);
        foreach ($cTelecos as $oTelecoUbi) {
            $iDescTel = $oTelecoUbi->getId_desc_teleco();
            $num_teleco = trim($oTelecoUbi->getNumTelecoVo()->value());
            if ($desc_teleco === '*' && !empty($iDescTel)) {
                $oDescTel = $DescTelecoRepository->findById((int) $iDescTel);
                $desc = $oDescTel?->getDescTelecoVo()?->value() ?? '';
                $tels .= $num_teleco . '(' . $desc . ')' . $separador;
            } else {
                $tels .= $num_teleco . $separador;
            }
        }

        if ($tels === '') {
            return '';
        }

        return substr($tels, 0, -strlen($separador));
    }

    /**
     * @return TelecoCtrRepositoryInterface|TelecoCtrDlRepositoryInterface|TelecoCtrExRepositoryInterface|TelecoUbiRepositoryInterface|TelecoCdcDlRepositoryInterface|TelecoCdcExRepositoryInterface|null
     */
    private function resolveTelecoRepository(): TelecoCtrRepositoryInterface|TelecoCtrDlRepositoryInterface|TelecoCtrExRepositoryInterface|TelecoUbiRepositoryInterface|TelecoCdcDlRepositoryInterface|TelecoCdcExRepositoryInterface|null
    {
        $aClassName = explode('\\', static::class);
        $childClassName = end($aClassName);

        return match ($childClassName) {
            'Centro' => DependencyResolver::get(TelecoCtrRepositoryInterface::class),
            'CentroDl' => DependencyResolver::get(TelecoCtrDlRepositoryInterface::class),
            'CentroEx' => DependencyResolver::get(TelecoCtrExRepositoryInterface::class),
            'Casa' => DependencyResolver::get(TelecoUbiRepositoryInterface::class),
            'CasaDl' => DependencyResolver::get(TelecoCdcDlRepositoryInterface::class),
            'CasaEx' => DependencyResolver::get(TelecoCdcExRepositoryInterface::class),
            default => null,
        };
    }
}
