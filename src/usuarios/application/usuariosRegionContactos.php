<?php

namespace src\usuarios\application;

use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermDl;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

class usuariosRegionContactos
{
    public function __construct(
        private UsuarioGrupoRepositoryInterface $usuarioGrupoRepository,
        private UsuarioRepositoryInterface $usuarioRepository,
        private PermMenuRepositoryInterface $permMenuRepository,
    ) {
    }

    /**
     * @return array{error: string, data: array<string, mixed>}
     */
    public function execute(string $region = ''): array
    {
        $codigos = self::parseCodigosRegion($region);
        if (count($codigos) > 1) {
            return $this->contactosDeVariasRegiones($codigos);
        }
        if (count($codigos) === 1) {
            return $this->contactosDeUnaRegion($codigos[0]);
        }
        if (strcasecmp(trim($region), 'todos') === 0) {
            return [
                'error' => '',
                'data' => [
                    'success' => true,
                    'contactos' => [],
                ],
            ];
        }

        return $this->contactosDeUnaRegion($region);
    }

    /**
     * Códigos de esquema (sin sufijo v/f) separados por coma. Ignora `todos`.
     *
     * @return list<string>
     */
    public static function parseCodigosRegion(string $region): array
    {
        $partes = preg_split('/\s*,\s*/', trim($region)) ?: [];
        $out = [];
        foreach ($partes as $codigo) {
            if ($codigo === '' || strcasecmp($codigo, 'todos') === 0) {
                continue;
            }
            if (!in_array($codigo, $out, true)) {
                $out[] = $codigo;
            }
        }

        return $out;
    }

    /**
     * @param array<string, array{email: string, cargo: string, nombre?: string, region?: string}> $existentes
     * @param array<string, array{email: string, cargo: string, nombre?: string, region?: string}> $nuevos
     * @return array<string, array{email: string, cargo: string, nombre: string, region: string}>
     */
    public static function fusionarContactos(array $existentes, array $nuevos, string $codigo): array
    {
        foreach ($nuevos as $nom => $info) {
            $nombre = $info['nombre'] ?? $nom;
            $existentes[$codigo . '|' . $nombre] = [
                'email' => $info['email'],
                'cargo' => $info['cargo'],
                'nombre' => $nombre,
                'region' => $codigo,
            ];
        }

        return $existentes;
    }

    /**
     * @param list<string> $codigos
     * @return array{error: string, data: array<string, mixed>}
     */
    private function contactosDeVariasRegiones(array $codigos): array
    {
        $aContactos = [];
        foreach ($codigos as $codigo) {
            $parcial = $this->contactosDeUnaRegion($codigo);
            if ($parcial['error'] !== '') {
                continue;
            }
            $mapa = $parcial['data']['contactos'] ?? [];
            if (!is_array($mapa)) {
                continue;
            }
            /** @var array<string, array{email: string, cargo: string, nombre?: string, region?: string}> $mapa */
            $aContactos = self::fusionarContactos($aContactos, $mapa, $codigo);
        }

        return [
            'error' => '',
            'data' => [
                'success' => true,
                'contactos' => $aContactos,
            ],
        ];
    }

    /**
     * @return array{error: string, data: array<string, mixed>}
     */
    private function contactosDeUnaRegion(string $region): array
    {
        $error_txt = '';
        $esquema = $region . 'v';
        try {
            $oConfigDB = new ConfigDB('sv-e_select');
            $config = $oConfigDB->getEsquema($esquema);
            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();
        } catch (\Throwable $e) {
            $error_txt = 'Error al obtener la conexión a la base de datos: ' . $e->getMessage();

            return ['error' => $error_txt, 'data' => []];
        }

        $this->usuarioGrupoRepository->setoDbl_select($oDevelPC);
        $this->usuarioRepository->setoDbl_select($oDevelPC);
        $cUsuariosRegion = $this->usuarioRepository->getUsuarios(['id_role' => 3], ['id_role' => '>']);

        $aContactos = [];
        foreach ($cUsuariosRegion as $oUsuario) {
            $email = $oUsuario->getEmailAsString();
            if ($email === null || $email === '') {
                continue;
            }
            $id_usuario = $oUsuario->getId_usuario();
            $usuario = $oUsuario->getUsuarioAsString();
            $nom_usuario = $oUsuario->getNomUsuarioAsString() ?? $usuario;

            $cGrupos = $this->usuarioGrupoRepository->getUsuariosGrupos(['id_usuario' => $id_usuario]);
            $iperm_menu = 0;
            $this->permMenuRepository->setoDbl_select($oDevelPC);
            foreach ($cGrupos as $UsuarioGrupo) {
                $id_grupo = $UsuarioGrupo->getId_grupo();
                $cPermMenu = $this->permMenuRepository->getPermMenus(['id_usuario' => $id_grupo]);
                foreach ($cPermMenu as $oPermMenu) {
                    $iperm_menu = $iperm_menu | $oPermMenu->getMenu_perm();
                }
            }
            $_SESSION['iPermMenus'] = $iperm_menu;
            $oPerm = new PermDl();
            $oPerm->setAccion($iperm_menu);

            if ($oPerm->have_perm_oficina('est') === true
                || $oPerm->have_perm_oficina('sm') === true
                || $oPerm->have_perm_oficina('agd') === true) {
                $aContactos[$nom_usuario] = [
                    'email' => $email,
                    'cargo' => MenuDlPermissionBits::listaTxt2($iperm_menu),
                ];
            }
        }

        return [
            'error' => $error_txt,
            'data' => [
                'success' => true,
                'contactos' => $aContactos,
            ],
        ];
    }
}
