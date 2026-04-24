<?php
/**
 * Wrapper deprecado. El driver CLI vive en:
 *   src/cambios/infrastructure/cli/avisos_generar_mails.php
 *
 * La logica de negocio esta en:
 *   src/cambios/application/AvisosEnviarMails.php
 *
 * Se mantiene este fichero para que crontabs existentes que apunten al
 * path legacy sigan funcionando. Cuando todos los crontabs apunten al
 * path nuevo podra eliminarse.
 */

require __DIR__ . '/../../../src/cambios/infrastructure/cli/avisos_generar_mails.php';
