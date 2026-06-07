<?php

namespace src\permisos\domain;

use src\procesos\domain\PermAccion;
use function src\shared\domain\helpers\is_true;

/**
 * Stub de permisos totales cuando el módulo procesos no está instalado.
 */
class PermisosActividadesTrue
{
    /** @var array<string, mixed> */
    protected array $aPermDl = [];

    /** @var array<string, mixed> */
    protected array $aPermOtras = [];

    protected string $sid_tipo_activ = '';

    private int $iid_activ = 0;

    private int $iid_tipo_proceso = 0;

    private bool $bpropia = true;

    private int $iid_fase = 0;

    private bool $btop = false;

    private int $idUsuario;

    public function __construct(int $iid_usuario)
    {
        $this->idUsuario = $iid_usuario;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function carregarTrue(string $sCondicion_usuario, string $dl_propia): void
    {
    }

    public function setActividad(int $id_activ, string $id_tipo_activ = '', string $dl_org = ''): void
    {
    }

    public function setId_fase(int $iid_fase): void
    {
        $this->iid_fase = $iid_fase;
    }

    public function getId_fase(): int
    {
        if ($this->iid_fase === 0) {
            echo 'No hay fase!!';
        }

        return $this->iid_fase;
    }

    public function getPermisoActual(string $sAfecta): PermAccion
    {
        return new PermAccion(15);
    }

    public function getPermisoActualPrev(string $sAfecta): PermAccion
    {
        if ($this->getIdTipoPrev() === false) {
            return new PermAccion(0);
        }

        return $this->getPermisoActual($sAfecta);
    }

    public function getPermisos(string $id_tipo_activ_txt = ''): mixed
    {
        if ($this->btop) {
            return false;
        }
        if ($id_tipo_activ_txt === '') {
            $id_tipo_activ_txt = $this->sid_tipo_activ;
        }
        if ($this->bpropia) {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermDl)) {
                return $this->aPermDl[$id_tipo_activ_txt];
            }

            return $this->getPermisosPrev($id_tipo_activ_txt);
        }
        if (array_key_exists($id_tipo_activ_txt, $this->aPermOtras)) {
            return $this->aPermOtras[$id_tipo_activ_txt];
        }

        return $this->getPermisosPrev($id_tipo_activ_txt);
    }

    public function getPermisosPrev(string $id_tipo_activ_txt = ''): mixed
    {
        if ($id_tipo_activ_txt === '') {
            $id_tipo_activ_txt = $this->sid_tipo_activ;
        }
        $prev_id_tipo = $this->getIdTipoPrev($id_tipo_activ_txt);
        if ($prev_id_tipo === false) {
            return false;
        }

        return $this->getPermisos($prev_id_tipo);
    }

    public function setId_tipo_activ(string $id_tipo_activ): void
    {
        $this->btop = ($id_tipo_activ === '......');
        $this->sid_tipo_activ = $id_tipo_activ;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->iid_activ = $id_activ;
    }

    public function getId_activ(): int
    {
        return $this->iid_activ;
    }

    public function setId_tipo_proceso(int $id_tipo_proceso): void
    {
        $this->iid_tipo_proceso = $id_tipo_proceso;
    }

    public function getId_tipo_proceso(): int
    {
        return $this->iid_tipo_proceso;
    }

    public function setPropia(bool|string $bpropia): void
    {
        $this->bpropia = is_true($bpropia);
    }

    /**
     * @return string|false
     */
    public function getIdTipoPrev(string $id_tipo_activ_txt = ''): string|false
    {
        if ($id_tipo_activ_txt === '') {
            $id_tipo_activ_txt = $this->sid_tipo_activ;
        }
        $match = [];
        $rta = preg_match('/(\d+)(\d)(\.*)/', $id_tipo_activ_txt, $match);
        if (empty($rta)) {
            if ($id_tipo_activ_txt === '1.....' || $id_tipo_activ_txt === '2.....' || $id_tipo_activ_txt === '3.....') {
                return '......';
            }
            $this->btop = true;

            return false;
        }

        $num_prev = $match[1];
        $pto = $match[3];
        $prev_id_tipo = $num_prev . '.' . $pto;
        $this->sid_tipo_activ = $prev_id_tipo;

        return $prev_id_tipo;
    }
}
