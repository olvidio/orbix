<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashF;

/**
 * Botón «?» y panel lateral de ayuda contextual del shell (`index.php`).
 *
 * Pinta el HTML/JS que, al pulsar el botón, pregunta a `/src/shared/ayuda_pantalla` por la
 * pantalla cargada en `#main` (atributo `refe`) y muestra sus tareas del manual con enlace a la
 * sección concreta. Es independiente del layout (legacy, burger, pills, pills2).
 */
final class AyudaContextualRender
{
    /** Tareas que se muestran en el panel; el resto queda en «ver todas en el manual». */
    private const LIMITE_TAREAS = 6;

    public static function render(): string
    {
        $urlAyudaPantalla = AppUrlConfig::srcBrowserUrl('/src/shared/ayuda_pantalla');
        $oHash = new HashF();
        $oHash->setUrl($urlAyudaPantalla);
        $oHash->setCamposForm('controller!limite');

        $base = AppUrlConfig::getPublicAppBaseUrl();

        $oView = new ViewNewPhtml('frontend\shared\controller');
        return $oView->renderizar('ayuda_contextual.phtml', [
            'urlAyudaPantalla' => $urlAyudaPantalla,
            'hashAyudaPantalla' => $oHash->getParamAjaxEnArray(),
            'urlManual' => $base . '/frontend/shared/controller/manual.php',
            'urlPreguntar' => $base . '/frontend/shared/controller/ayuda_preguntar.php',
            'limiteTareas' => self::LIMITE_TAREAS,
        ], false);
    }
}
