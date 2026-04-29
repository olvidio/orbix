<?php

/**
 * Resuelve `$estilo_color` y `$tipo_menu` para incluir `colores.php` sin `global_object.inc`.
 * Usado desde hojas CSS PHP incluidas en vistas (p.ej. tessera) donde no debe montarse
 * el bootstrap completo (DI, ObtenerConfigSnapshot, etc.).
 */

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

/**
 * @return array{0: string, 1: string} [estilo_color, tipo_menu]
 */
function css_colores_estilo_desde_sesion(): array
{
    $estilo_color = 'azul';
    $tipo_menu = 'horizontal';
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return [$estilo_color, $tipo_menu];
    }
    $auth = $_SESSION['session_auth'] ?? null;
    if (!is_array($auth) || empty($auth['id_usuario']) || empty($auth['esquema'])) {
        return [$estilo_color, $tipo_menu];
    }
    $id_usuario = (int)$auth['id_usuario'];
    $esquema = (string)$auth['esquema'];

    $pdo = css_colores_pdo_for_esquema_usuario($esquema);
    if ($pdo === null) {
        return [$estilo_color, $tipo_menu];
    }

    $sql = 'SELECT preferencia FROM web_preferencias WHERE id_usuario = :id_usuario AND tipo = :tipo LIMIT 1';
    $st = $pdo->prepare($sql);
    if ($st === false || !$st->execute(['id_usuario' => $id_usuario, 'tipo' => 'estilo'])) {
        return [$estilo_color, $tipo_menu];
    }
    $row = $st->fetch(\PDO::FETCH_ASSOC);
    if ($row === false || empty($row['preferencia'])) {
        return [$estilo_color, $tipo_menu];
    }
    $parts = preg_split('/#/', (string)$row['preferencia']);
    $estilo_color = ($parts[0] ?? '') !== '' ? $parts[0] : 'azul';
    $tipo_menu = $parts[1] ?? 'horizontal';

    return [$estilo_color, $tipo_menu];
}

/**
 * Misma convención que {@see \src\usuarios\application\LoginProcesar::pdoForEsquema()}.
 */
function css_colores_pdo_for_esquema_usuario(string $esquema): ?\PDO
{
    try {
        if (substr($esquema, -1) === 'v') {
            $oConfigDB = new ConfigDB('sv-e_select');
            $config = $oConfigDB->getEsquema($esquema);

            return (new DBConnection($config))->getPDO();
        }
        if (substr($esquema, -1) === 'f') {
            $oConfigDB = new ConfigDB('sf-e');
            $config = $oConfigDB->getEsquema($esquema);

            return (new DBConnection($config))->getPDO();
        }
    } catch (\Throwable) {
        return null;
    }

    return null;
}
