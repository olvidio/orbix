<?php

namespace src\permisos\domain;

use src\procesos\domain\PermAccion;

/**
 * Stub de permisos totales cuando el módulo procesos no está instalado.
 *
 * Extiende {@see PermisosActividades} para que `instanceof PermisosActividades`
 * sea verdadero y los callers no necesiten lógica especial.
 */
class PermisosActividadesTrue extends PermisosActividades
{
    /**
     * Constructor ligero: solo guarda el id de usuario, sin repositorios ni SQL.
     */
    public function __construct(int $idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    /**
     * Serialización mínima: solo el id de usuario.
     * Evita depender de la serialización del padre (propiedades privadas, repos, etc.).
     */
    public function __serialize(): array
    {
        return ['idUsuario' => $this->idUsuario];
    }

    /**
     * Restaura solo el id de usuario; acepta tanto el formato propio como
     * el del padre (sesión creada antes de que True extendiera PermisosActividades).
     */
    public function __unserialize(array $data): void
    {
        // Formato propio (nuevo) o heredado del padre.
        $this->idUsuario = (int) ($data['idUsuario'] ?? 0);
    }

    public function setActividad(int $id_activ, ?string $id_tipo_activ = null, ?string $dl_org = null): void
    {
        if ($id_tipo_activ !== null && $id_tipo_activ !== '' && $dl_org !== null && $dl_org !== '') {
            parent::setActividad($id_activ, $id_tipo_activ, $dl_org);
        }
    }

    public function getPermisoActual(string $sAfecta): PermAccion
    {
        return new PermAccion($this->bpropia ? 15 : 3);
    }

    public function getPermisoOn(int|string $iAfecta): PermAccion
    {
        return new PermAccion(15);
    }

    /**
     * Sin módulo procesos se permite crear cualquier actividad.
     *
     * @return array{of_responsable_txt: string, status: int}
     */
    public function getPermisoCrear(bool $dl_propia): array
    {
        return ['of_responsable_txt' => '', 'status' => 0];
    }

    /**
     * Sin módulo procesos no hay fases que precargar.
     *
     * @param list<int> $aFases
     */
    public function setFasesCompletadas(array $aFases = []): void
    {
    }
}
