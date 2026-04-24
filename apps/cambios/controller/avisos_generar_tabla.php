<?php
/**
 * Wrapper deprecado. El driver CLI/web vive en:
 *   src/cambios/infrastructure/cli/avisos_generar_tabla.php
 *
 * La logica de negocio esta en:
 *   src/cambios/application/AvisosGenerarTabla.php
 *
 * Se mantiene este fichero para que crontabs y `exec()` existentes que
 * apunten al path legacy sigan funcionando. Cuando todos los crontabs y
 * llamadas a `exec()` apunten al path nuevo podra eliminarse.
 */

require __DIR__ . '/../../../src/cambios/infrastructure/cli/avisos_generar_tabla.php';
