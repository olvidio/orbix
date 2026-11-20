<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

/**
 * Convierte el bloque de pantalla que el usuario exporta (HTML) en un fichero: html, html en zip, hoja de
 * cálculo (.ods) o texto (.odt).
 *
 * El título que llega de la petición solo se usa para el nombre de la descarga, ya saneado
 * ({@see nombreDeDescarga()}); los ficheros de trabajo van a un directorio temporal propio con nombres
 * fijos, y la orden `xsltproc` recibe todos sus argumentos con `escapeshellarg`.
 */
final class ExportadorDocumento
{
    /** Hoja de estilos y número de estilo de las listas que tienen formato propio. */
    private const POR_REFERENCIA = [
        'encargossacd/view/lista_com_ctr.phtml' => ['html2ootext_list_com_ctr.xslt', '3'],
        'frontend/encargossacd/view/listas_com_ctr.phtml' => ['html2ootext_list_com_ctr.xslt', '3'],
        'encargossacd/view/lista_com_sacd.phtml' => ['html2ootext_list_com_sacd.xslt', '3'],
        'frontend/encargossacd/view/listas_com_sacd.phtml' => ['html2ootext_list_com_sacd.xslt', '3'],
        'actividadessacd/view/com_sacd_activ_print.phtml' => ['html2ootext_com_sacd.xslt', '2'],
    ];

    private string $dirOdf;

    public function __construct(?string $dirOdf = null)
    {
        $this->dirOdf = $dirOdf ?? dirname(__DIR__, 3) . '/libs/export/ODF';
    }

    /** Nombre de fichero para la descarga: letras, cifras, «_» y «-»; lo demás se sustituye. */
    public static function nombreDeDescarga(string $titulo, string $extension): string
    {
        $nombre = (string) preg_replace('/[^\p{L}\p{N}_-]+/u', '_', $titulo);
        $nombre = trim($nombre, '_');
        if ($nombre === '') {
            $nombre = 'export';
        }

        return mb_substr($nombre, 0, 100) . '.' . $extension;
    }

    /**
     * @return array{contenido: string, nombre: string, mime: string}
     */
    public function generar(string $tipo, string $titulo, string $documento, string $referencia): array
    {
        switch ($tipo) {
            case 'html':
                return [
                    'contenido' => $this->htmlLimpio($documento),
                    'nombre' => self::nombreDeDescarga($titulo, 'html'),
                    'mime' => 'application/octet-stream',
                ];
            case 'html_zip':
                $nombreHtml = self::nombreDeDescarga($titulo !== '' ? $titulo : 'planning', 'html');
                return [
                    'contenido' => $this->zip([$nombreHtml => $this->htmlLimpio($documento)]),
                    'nombre' => self::nombreDeDescarga($titulo !== '' ? $titulo : 'planning', 'zip'),
                    'mime' => 'application/octet-stream',
                ];
            case 'odfc':
                $contenido = $this->transformar($this->htmlParaOdf($documento), 'html2odfcalc.xslt');
                return [
                    'contenido' => $this->odf($contenido, '1', 'spreadsheet'),
                    'nombre' => self::nombreDeDescarga($titulo, 'ods'),
                    'mime' => 'application/vnd.oasis.opendocument.spreadsheet',
                ];
            case 'odft':
                [$xslt, $estilo] = self::POR_REFERENCIA[$referencia] ?? ['html2odftext.xslt', '1'];
                $contenido = $this->transformar($this->htmlParaOdf($documento), $xslt);
                return [
                    'contenido' => $this->odf($contenido, $estilo, 'text'),
                    'nombre' => self::nombreDeDescarga($titulo, 'odt'),
                    'mime' => 'application/vnd.oasis.opendocument.text',
                ];
        }
        throw new \InvalidArgumentException('Tipo de exportación no admitido');
    }

    private function htmlLimpio(string $documento): string
    {
        $documento = (string) preg_replace('/<img(.*?)>/mi', '', $documento);

        return '<meta charset="utf-8">' . stripslashes($documento);
    }

    private function htmlParaOdf(string $documento): string
    {
        self::arreglarAmpersands($documento, 0);
        $documento = html_entity_decode($documento, ENT_NOQUOTES, 'UTF-8');
        $documento = '<meta charset="utf-8">' . stripslashes($documento);

        return (string) preg_replace('/<form.*>/', '', $documento);
    }

    /** Quita o escapa los «&» que no son una entidad HTML (el transformador no los admite). */
    private static function arreglarAmpersands(string &$html, int $desde): void
    {
        while ($desde <= strlen($html) && ($posAmp = strpos($html, '&', $desde)) !== false) {
            $posPuntoComa = strpos($html, ';', $posAmp + 1);
            if ($posPuntoComa === false) {
                $html = substr_replace($html, '', $posAmp, 1);
                return;
            }
            $trozo = substr($html, $posAmp, $posPuntoComa - $posAmp + 1);
            if (preg_match('/&(#[0-9]+|[A-Z|a-z|0-9]+);/', $trozo) === 0) {
                $html = substr_replace($html, '&amp;', $posAmp, 1);
                $desde = $posAmp + 5;
            } else {
                $desde = $posAmp + 1;
            }
        }
    }

    /** Aplica una hoja XSLT de `libs/export/ODF/xslt` al HTML y devuelve el `content.xml` resultante. */
    private function transformar(string $html, string $xslt): string
    {
        $dir = $this->directorioTemporal();
        $entrada = $dir . '/entrada.txt';
        $salida = $dir . '/salida.xml';
        try {
            file_put_contents($entrada, $html);
            $orden = 'xsltproc --html -o ' . escapeshellarg($salida) . ' '
                . escapeshellarg($this->dirOdf . '/xslt/' . $xslt) . ' ' . escapeshellarg($entrada) . ' 2>&1';
            $lineas = [];
            $codigo = 0;
            exec($orden, $lineas, $codigo);
            $contenido = is_file($salida) ? (string) file_get_contents($salida) : '';
            if ($contenido === '') {
                // El detalle va al registro: a la respuesta no se le enseña ni la orden ni su salida.
                error_log('ExportadorDocumento: xsltproc (' . $codigo . '): ' . implode(' | ', array_slice($lineas, 0, 5)));
                throw new \RuntimeException('No se ha podido convertir el documento');
            }

            return $contenido;
        } finally {
            @unlink($entrada);
            @unlink($salida);
            @rmdir($dir);
        }
    }

    private function odf(string $contentXml, string $estilo, string $tipoDoc): string
    {
        require_once dirname($this->dirOdf) . '/odf.php';
        $odf = new \odf();
        $odf->parse((string) file_get_contents($this->dirOdf . '/content_ini.xml'));

        $estilos = $this->dirOdf . '/' . $estilo . ($tipoDoc === 'text' ? '_styles_txt.xml' : '_styles.xml');
        $ajustes = $this->dirOdf . '/' . $estilo . '_settings.xml';
        $mime = $tipoDoc === 'text' ? 'application/vnd.oasis.opendocument.text' : 'application/vnd.oasis.opendocument.spreadsheet';

        $ficheros = [
            'mimetype' => $mime,
            // El texto que sale de xsltproc ya trae las cabeceras.
            'content.xml' => $contentXml,
            'meta.xml' => (string) $odf->getMeta('es-ES'),
            'styles.xml' => is_file($estilos) ? (string) file_get_contents($estilos) : '',
            'settings.xml' => is_file($ajustes) ? (string) file_get_contents($ajustes) : '',
            'manifest.rdf' => (string) $odf->getManifestRdf(),
            'META-INF/manifest.xml' => (string) $odf->getManifest($tipoDoc),
        ];
        $directorios = ['Configurations2/accelerator', 'Configurations2/images', 'Configurations2/popupmenu',
            'Configurations2/statusbar', 'Configurations2/floater', 'Configurations2/menubar',
            'Configurations2/progressbar', 'Configurations2/toolbar'];

        return $this->zip($ficheros, $directorios);
    }

    /**
     * @param array<string, string> $ficheros nombre dentro del zip => contenido
     * @param list<string> $directorios directorios vacíos
     */
    private function zip(array $ficheros, array $directorios = []): string
    {
        $dir = $this->directorioTemporal();
        $ruta = $dir . '/salida.zip';
        try {
            $zip = new \ZipArchive();
            if ($zip->open($ruta, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('No se ha podido crear el fichero comprimido');
            }
            foreach ($ficheros as $nombre => $contenido) {
                $zip->addFromString($nombre, $contenido);
            }
            foreach ($directorios as $d) {
                $zip->addEmptyDir($d);
            }
            $zip->close();

            return (string) file_get_contents($ruta);
        } finally {
            @unlink($ruta);
            @rmdir($dir);
        }
    }

    private function directorioTemporal(): string
    {
        $dir = sys_get_temp_dir() . '/orbix_export_' . bin2hex(random_bytes(12));
        if (!mkdir($dir, 0700) && !is_dir($dir)) {
            throw new \RuntimeException('No se ha podido crear el directorio temporal');
        }

        return $dir;
    }
}
