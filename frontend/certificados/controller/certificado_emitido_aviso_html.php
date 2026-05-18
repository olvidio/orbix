<?php

/**
 * Muestra un aviso HTML legible (p. ej. en la ventana del PDF) y termina la petición.
 */
function certificado_emitido_echo_aviso_y_salir(string $mensaje, string $titulo = ''): never
{
    if ($titulo === '') {
        $titulo = _('Antes de generar el certificado');
    }
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars(_('Certificado'), ENT_QUOTES, 'UTF-8') ?></title>
    </head>
    <body style="font-family: sans-serif; margin: 1.5rem;">
    <div class="certificado-aviso-config" role="alert"
         style="max-width: 42rem; padding: 1rem 1.25rem; border: 1px solid #c9a227; background: #fffbea; color: #3d3500; line-height: 1.5;">
        <p style="margin: 0 0 0.75rem; font-weight: bold;"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></p>
        <div><?= $mensaje ?></div>
    </div>
    </body>
    </html>
    <?php
    exit;
}
