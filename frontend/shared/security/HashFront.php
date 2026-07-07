<?php
namespace frontend\shared\security;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;

class HashFront
{
    /**
     * Campos que el JS puede inyectar al enviar (p. ej. fnjs_solo_uno) y no forman parte del hash del formulario origen.
     *
     * @var list<string>
     */
    private const POST_CAMPOS_UI_DINAMICOS = ['id_sel', 'scroll_id', 'nav'];

    /**
     * Quita campos que el JS inyecta al enviar y no deben participar en el hash (ni al firmar ni al validar).
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function stripPostCamposUiDinamicos(array $data): array
    {
        foreach (self::POST_CAMPOS_UI_DINAMICOS as $campoUi) {
            unset($data[$campoUi]);
        }

        return $data;
    }

    /**
     * Dirección Url
     *
     * @var string
     */
    private string $sUrl = '';
    /**
     * campos_chk de Seguridad
     * Lista de campos separados por '!'.
     * campos de si o no.
     *
     * @var string
     */
    private string $sCamposChk = '';
    /**
     * camposForm de Seguridad
     * Lista de campos separados por '!'.
     * campos que se pasan con el formulario.
     *
     * @var string
     */
    private string $sCamposForm = '';
    /**
     * acamposHidden de Seguridad
     * Array de campos campos hidden y sus valores.
     *
     * @var array<string, mixed>
     */
    private array $aCamposHidden = [];
    /**
     * para poder tener un id distinto para un mismo nombre de campo hidden
     */
    private string $prefix = '';
    /**
     * camposNo de Seguridad
     * Lista de campos separados por '!'.
     * campos a no tener en cuenta para el hash.
     *
     * @var string
     */
    private string $sCamposNo = '';

    /**
     * aValoresCamposNo de Seguridad
     *
     * array con los campos y valores a no tener en cuenta para el hash.
     * para ponerlos otra vez en la query después de calcular los hash
     *
     * @var array<string, mixed>
     */
    private static array $aValoresCamposNo = [];

    /* CONSTRUCTOR ------------------------------ */
    public function __construct()
{
    // constructor vuit
}

    private static function asPostString(mixed $value): string
    {
        if ($value === null || $value === false || $value === '') {
            return '';
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return '';
    }

    private static function asScalarString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }

    private static function serverString(string $key, string $default = ''): string
    {
        $value = $_SERVER[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /** @return array<string, mixed> */
    public function export(): array
    {
        return [
            'aValoresCamposNo' => self::$aValoresCamposNo,
            'sCamposNo' => $this->sCamposNo,
            'prefix' => $this->prefix,
            'aCamposHidden' => $this->aCamposHidden,
            'sCamposForm' => $this->sCamposForm,
            'sCamposChk' => $this->sCamposChk,
            'sUrl' => $this->sUrl,
        ];
    }

    /** @param array<string, mixed> $data */
    public function import(array $data): void
    {
        self::$aValoresCamposNo = is_array($data['aValoresCamposNo'] ?? null) ? $data['aValoresCamposNo'] : [];
        $this->sCamposNo = is_string($data['sCamposNo'] ?? null) ? $data['sCamposNo'] : '';
        $this->prefix = is_string($data['prefix'] ?? null) ? $data['prefix'] : '';
        $this->aCamposHidden = is_array($data['aCamposHidden'] ?? null) ? $data['aCamposHidden'] : [];
        $this->sCamposForm = is_string($data['sCamposForm'] ?? null) ? $data['sCamposForm'] : '';
        $this->sCamposChk = is_string($data['sCamposChk'] ?? null) ? $data['sCamposChk'] : '';
        $this->sUrl = is_string($data['sUrl'] ?? null) ? $data['sUrl'] : '';
    }

    /**
     * Para validar los parámetros enviados via POST. Recalcula el hash y lo compara con el
     * que se pasa a trasvés del POST.
     *
     * @param array<string, mixed> $aPOST (normalmente $_POST)
     */
    public function validatePost(array $aPOST): void
{
    if (isset($aPOST['h'])) {
        $salta = 0;
        $salta_txt = '';
        $horig = '';
        $h1 = self::asPostString($aPOST['h']);
        // hash de los campos hidden
        $hh = self::asPostString($aPOST['hh'] ?? null);
        // campos hidden. (separados por !).
        $hhc = self::asPostString($aPOST['hhc'] ?? null);
        // campos del formulario. (separados por '!').
        //$hc = empty($aPOST['hc'])? '' : $aPOST['hc'];
        // campos que se deben quitar del hash. (separados por '!').
        $hno = self::asPostString($aPOST['hno'] ?? null);
        if (!empty($hno)) {
            $a_campos_no = explode('!', $hno);
            foreach ($a_campos_no as $campo) {
                unset($aPOST[$campo]);
            }
        }
        $hchk = self::asPostString($aPOST['hchk'] ?? null);

        //si es hnov es 1, es para no tener en cuenta los valores de los parametros en el hash.
        $hnov = self::asPostString($aPOST['hnov'] ?? null);
        $hpos = self::asPostString($aPOST['hpos'] ?? null);

        if (OrbixRuntime::isDebug()) {
            // Url original de la que se ha hecho el hash. Para comparar con la actual.
            $horig = self::asPostString($aPOST['horig'] ?? null);
            $hhorig = self::asPostString($aPOST['hhorig'] ?? null);
            /*
            $horig = empty($aPOST['horig'])? '' : rawurldecode($aPOST['horig']);
            $hhorig = empty($aPOST['hhorig'])? '' : rawurldecode($aPOST['hhorig']);
            */
        }
        // Si es un form, paso la lista de campos posibles.
        if (isset($aPOST['hh'])) {
            unset($aPOST['PHPSESSID']);
            unset($aPOST['atras']);
            unset($aPOST['h']);
            //unset($aPOST['hc']);
            unset($aPOST['horig']);
            unset($aPOST['hh']);
            unset($aPOST['hhc']);
            unset($aPOST['hhorig']);
            unset($aPOST['hno']);
            unset($aPOST['hchk']);
            unset($aPOST['hnov']);

            // Que los campos hidden sean los mismos y con los mismos valores.
            //lista de campos hidden
            $a_campos = explode('!', $hhc);
            $aCampos = [];
            foreach ($a_campos as $campo) {
                if (empty($campo)) continue;
                // no puedo usar empty por los valores '0'
                if (isset($aPOST[$campo])) {
                    //$aCampos[$campo] = rawurldecode($aPOST[$campo]);
                    $aCampos[$campo] = $aPOST[$campo];
                } else {
                    $aCampos[$campo] = '';
                }
            }
            $h2 = self::getHashArray($aCampos)['hash'];
            $sUrl = self::getHashArray($aCampos)['orig'];

            if ($hh !== $h2) {
                $salta = 1;
                if (OrbixRuntime::isDebug()) {
                    $salta_txt = _("llegan campos hidden modificados");
                    $horig = $hhorig;
                }
            } else {
                // Si vengo por web\Posicion, el hash es de toda la url.
                // Con los formularios, como en algunos casos se cambia el action, sólo compruebo los campos.
                if ($hpos == 1) {
                    $requestPath = parse_url($this->realFullUrl(), PHP_URL_PATH);
                    $requestPath = is_string($requestPath) ? $requestPath : '';
                    $rta = self::mdForPosicionBack($requestPath, $aPOST);
                } else {
                    // Que los campos checkbox sean los mismos sin tener en cuenta los mismos valores.
                    // Solo para forms normales: add_hash (posicion) usa los valores reales.
                    //lista de campos chck
                    $a_campos = explode('!', $hchk);
                    foreach ($a_campos as $campo) {
                        if (empty($campo)) continue;
                        $aPOST[$campo] = '';
                    }
                    // El hash de los campos Form, Tiene que ser sin valores, pues cuando se ha
                    //  generado, los campos estaban vacíos.
                    $rta = self::getHashArray($aPOST, 1);
                }
                $h2 = $rta['hash'];
                $sUrl = $rta['orig'];
                if ($h1 !== $h2) {
                    $salta = 1;
                    if (OrbixRuntime::isDebug()) {
                        $salta_txt = _("llegan distinto número o nombre de los campos que se dice que se envían");
                    }
                }
            }
        } else {
            if ($hpos == 1) {
                // Atrás desde Posicion::mostrarNavAtras (add_hash sin meta-hash de formulario).
                $requestPath = parse_url($this->realFullUrl(), PHP_URL_PATH);
                $requestPath = is_string($requestPath) ? $requestPath : '';
                $rta = self::mdForPosicionBack($requestPath, $aPOST);
                $h2 = $rta['hash'];
                $sUrl = $rta['orig'];
                if ($h1 !== $h2) {
                    $salta = 1;
                    $salta_txt = _("url modificada");
                }
            } else {
                unset($aPOST['PHPSESSID']);
                unset($aPOST['atras']);
                unset($aPOST['h']);
                unset($aPOST['horig']);
                unset($aPOST['hh']);
                unset($aPOST['hhc']);
                unset($aPOST['hhorig']);
                // campos que se deben quitar del hash. (separados por '!').
                $hno = self::asPostString($aPOST['hno'] ?? null);
                if ($hno !== '') {
                    $a_campos_no = explode('!', $hno);
                    foreach ($a_campos_no as $campo) {
                        unset($aPOST[$campo]);
                    }
                }
                unset($aPOST['hno']);
                unset($aPOST['hchk']);
                unset($aPOST['hnov']);
                ksort($aPOST);

                if ($hnov === '1') { // borro posibles los valores de los campos
                    foreach ($aPOST as $camp => $valor) {
                        $aPOST[$camp] = '';
                    }
                }

                $sUrl = $this->realFullUrl();
                if ($aPOST !== []) {
                    array_walk($aPOST, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
                    $sUrl .= '?' . http_build_query($aPOST);
                }

                $rta = self::md($sUrl);
                $h2 = $rta['hash'];
                $sUrl = $rta['orig'];

                if ($h1 !== $h2) {
                    $salta = 1;
                    $salta_txt = _("url modificada");
                }
            }
        }
        if ($salta === 1) {
            if (OrbixRuntime::isDebug()) {
                $salta_txt .= '<br>';
                $requestUri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])
                    ? $_SERVER['REQUEST_URI']
                    : '';
                $salta_txt .= 'script: ' . $requestUri . '<br>';
                $salta_txt .= "url (h1) emisor  : $horig<br>";
                $salta_txt .= "url (h2) receptor: $sUrl<br>";
                //$salta_txt .= "h1: $h1; h2: $h2;  ".var_dump($h1===$h2);
                echo $salta_txt;
                $err = _("problema general de seguridad") . "\n";
                //$_SESSION['oGestorErrores']->addErrorSec($err, $salta_txt, __LINE__, __FILE__);
                exit();
            } else {
                $salta_txt = isset($_SERVER['PHP_SELF']) && is_string($_SERVER['PHP_SELF'])
                    ? $_SERVER['PHP_SELF']
                    : '';
                $err = _("problema general de seguridad") . "\n";
                $err .= _("para ver más detalles define ORBIX_FRONT_DEBUG=1 en el entorno del frontend");
                //$_SESSION['oGestorErrores']->addErrorSec($err, $salta_txt, __LINE__, __FILE__);
                // Para salir de la sesión (solo si hay sesión PHP activa; p.e. CLI o bootstrap sin session_start).
                if (session_status() === PHP_SESSION_ACTIVE) {
                    $_SESSION = [];
                    // Si se desea destruir la sesión completamente, borre también la cookie de sesión.
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        $sessionName = session_name();
                        setcookie(is_string($sessionName) ? $sessionName : 'PHPSESSID', '',
                            ['expires' => time() - 42000,
                                'path' => $params["path"],
                                'domain' => $params["domain"],
                                'secure' => $params["secure"],
                                'httponly' => $params["httponly"],
                                'sameSite' => 'Strict',
                            ]
                        );
                    }
                    session_regenerate_id(true);
                    session_destroy();
                }
                $pagina_exit = '/' . OrbixRuntime::webdir() . '/index.php';
                header("Location: $pagina_exit");
                die();
            }
        }
    } else {
        //evito los scripts y si va por command line (no existe REQUEST URI)
        $salta = 0;
        $requestUri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])
            ? $_SERVER['REQUEST_URI']
            : null;
        if ($requestUri === null) {
            $salta = 1;
        } else {
            if (strpos($requestUri, '/index.php') !== false) {
                $salta = 1;
            }
            if (strpos($requestUri, 'udm4') !== false) {
                $salta = 1;
            }
        }
        if ($salta === 0 && (OrbixRuntime::miIdRole() === 1) && $requestUri !== null) {
            if (!str_contains($requestUri, '2fa.php')) {
                echo '<div>' . $requestUri . ':  ' . _("página con seguridad desactivada") . '</div>';
            }
        }
    }
}

    /**
     * Devuelve el html para poner dentro de un form, con los campos necesarios para
     * enviar y calcular el hash.
     * Se incluyen los campos hidden que se hayan definido en la clase Hash
     * Se incluyen los campos de comprobacion (h, hc, horig, hh, hhc, hhorig, hno, hchkk, hnov)
     *     h: el hash de los campos del form (hc) logicamente sin valor, pues se puede cambiar en el form.
     *     hh: el hash de los campos Hidden (hhc) con sus valores, que no se pueden cambiar.
     *
     * @return string html
     */
    public function getCamposHtml(): string
{
    $this->addHiddenToForm();

    $CamposFormSorted = $this->ordenarQuery($this->sCamposForm);
    $rta = self::md($CamposFormSorted);
    $HashCamposForm = $rta['hash'];
    $HashCamposFormOrig = $rta['orig'];

    $CamposHidden = $this->array2stringCamposHidden();
    $aCamposHidden = $this->getArrayCamposHidden();
    $CamposNo = $this->sCamposNo;
    $CamposChk = $this->sCamposChk;

    $aCamposNo = [];
    if (!empty($CamposNo)) {
        $aCamposNo = explode('!', $CamposNo);
    }
    $aCampos = [];
    foreach ($aCamposHidden as $campo => $valor) {
        //los camposNo, valor = ''.
        if (!empty($CamposNo) && in_array($campo, $aCamposNo)) {
            $aCampos[$campo] = '';
        } else {
            $aCampos[$campo] = $valor;
        }
    }

    $rta = self::getHashArray($aCampos);
    $CamposHidden_hash = $rta['hash'];
    $CamposHidden_horig = $rta['orig'];

    $html = "<input type=\"Hidden\" name=\"h\" value=\"$HashCamposForm\" >\n";
    if (OrbixRuntime::isDebug()) {
        $html .= "<input type=\"Hidden\" name=\"horig\" value=\"$HashCamposFormOrig\" >\n";
    }
    if (!empty($CamposNo)) {
        $html .= "<input type=\"Hidden\" name=\"hno\" value=\"$CamposNo\">\n";
    }
    if (!empty($CamposChk)) {
        $html .= "<input type=\"Hidden\" name=\"hchk\" value=\"$CamposChk\">\n";
    }
    $html .= "<input type=\"Hidden\" name=\"hhc\" value=\"$CamposHidden\" >\n";
    $html .= "<input type=\"Hidden\" name=\"hh\" value=\"$CamposHidden_hash\" >\n";
    if (OrbixRuntime::isDebug()) {
        $html .= "<input type=\"Hidden\" name=\"hhorig\" value=\"$CamposHidden_horig\" >\n";
    }
    $html .= $this->getCamposHiddenHtml();
    return $html;
}

    /**
     * Añade el hash al final del string que se le pasa como url.
     *
     * @param string $sUrl_full
     * @return string
     */
    public static function link(string $sUrl_full): string
    {
        //$sUrl_org = $sUrl_full;
        $sUrl_full = self::ordenarParam($sUrl_full);
        // Normalizar quitando sf/ESQUEMA para hash consistente
        $sUrl_hash = self::normalizarUrlSinSfEsquema($sUrl_full);
        $rta = self::md($sUrl_hash);
        $sUrlHash = $rta['hash'];
        $horig = $rta['orig'];

        $aParam = [];
        $aParam['h'] = $sUrlHash;
        if (OrbixRuntime::isDebug()) {
            $aParam['horig'] = $horig;
        }
        array_walk($aParam, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
        if (strpos($sUrl_full, '?') === false) {
            $sUrl_full .= '?' . http_build_query($aParam);
        } else {
            $sUrl_full .= '&' . http_build_query($aParam);
        }
        return $sUrl_full;
    }

    /**
     *  Como el link. (añade el hash de los parámetros)
     *  Cuando se llama a una url del mismo servidor.
     *  Se mantiene el puerto interno donde corre el nginx/apache
     *  aunque el acceso desde el exterior sea desde otro puerto (caso de la sf).
     *
     * @param string $sUrl_full
     * @return string
     */
    public static function cmdConParametros(string $sUrl_full): string
    {
        $sUrl_org = self::link($sUrl_full);

        $sUrl_int = str_replace(OrbixRuntime::webPortSf(), OrbixRuntime::webPort(), $sUrl_org);
        $sUrl_int = self::normalizarUrlSinSfEsquema($sUrl_int);
        return $sUrl_int;
    }

    /**
     *  Cuando se llama a una url del mismo servidor.
     *  Se mantiene el puerto interno donde corre el nginx/apache
     *  aunque el acceso desde el exterior sea desde otro puerto (caso de la sf).
     *
     * @param string $sUrl_full
     * @return string
     */
    public static function cmdSinParametros(string $sUrl_full): string
    {
        //$sUrl_int = str_replace(OrbixRuntime::webPortSf(), OrbixRuntime::webPort(), $sUrl_full);
        return self::normalizarUrlSinSfEsquema($sUrl_full);
    }

    /**
     * Calcula el hash de los campos para añadir en los link.
     * Genera la url completa: url + camposHidden + hash
     *    campos hidden con sus calores
     *    camposNo no se tienen en cuenta. (se pueden añadir al final en las funciones javascript)
     *    camposForm sin valores. (se deben añadir al final en las funciones javascript)
     *
     * Devuelve la url + camposHidden + los parámetros h.
     */
    public function linkConVal(): string
{
    $this->addHiddenToForm();
    $CamposFormSorted = $this->ordenarQuery($this->sCamposForm);

    $url = $this->sUrl;
    $sUrl_full = self::FullPath($url);

    $rta = self::md($CamposFormSorted);
    $HashCamposForm = $rta['hash'];
    $HashCamposFormOrig = $rta['orig'];

    $CamposNo = $this->sCamposNo;
    $aCamposNo = [];
    if (!empty($CamposNo)) {
        $aCamposNo = explode('!', $CamposNo);
    }
    $CamposHidden_orig = '';
    $CamposHidden = '';
    $CamposHidden_hash = '';
    if (!empty($this->aCamposHidden)) {
        $aCampos = [];
        $CamposHidden = $this->array2stringCamposHidden();
        foreach ($this->aCamposHidden as $campo => $valor) {
            //los camposNo, valor = ''.
            if (!empty($CamposNo) && in_array($campo, $aCamposNo)) {
                $aCampos[$campo] = '';
            } else {
                $aCampos[$campo] = $valor;
            }
        }

        $rta = self::getHashArray($aCampos);
        $CamposHidden_hash = $rta['hash'];
        $CamposHidden_orig = $rta['orig'];
    }

    $sQuery = '';
    $sQuery .= "h=$HashCamposForm";
    if (!empty($CamposNo)) {
        $sQuery .= "&hno=$CamposNo";
    }
    $CamposChk = $this->sCamposChk;
    if (!empty($CamposChk)) {
        $sQuery .= "&hchk=$CamposChk";
    }
    $sQuery .= "&hhc=$CamposHidden";
    $sQuery .= "&hh=$CamposHidden_hash";

    if (OrbixRuntime::isDebug()) {
        // OJO. Si paso los parámetros normalmente, la lista de campos orig
        //    se interpreta como campos a añadir.
        //$sQuery .= "&horig=$HashCamposFormOrig";
        //$sQuery .= "&hhorig=$CamposHidden_orig";
        $sQuery .= '&horig=' . rawurlencode($HashCamposFormOrig);
        $sQuery .= '&hhorig=' . rawurlencode($CamposHidden_orig);
    }

    $sQuery .= '&' . $this->array2queryCamposHidden();

    return $sQuery;
}

    /**
     * Calcula el hash de los campos del form sin valores
     * para añadir en los link
     *
     * Devuelve una cadena para añadir a la url con los parámetros: hnov=1, h, horig
     *    hnov sirve para indicar al receptor que el hash se ha hecho sin los valores.
     *
     * @return string
     */
    public function linkSinVal(): string
{
    return $this->buildLinkSinVal(false);
}

    /**
     * Igual que `linkSinVal()` pero devolviendo siempre `&hnov=1&h=...` como
     * primer separador, pensado para concatenar tras una cadena de parametros
     * ya montada (p.e. en el body POST de un AJAX: `id_x=1&id_y=2` + hash).
     *
     * Usa esta variante cuando el consumidor hace:
     *     'id_zona=' + id_zona + '<?= $h ?>'
     * y usa `linkSinVal()` cuando el consumidor hace:
     *     $url . $oHash->linkSinVal()
     *
     * Devuelve una cadena del tipo `&hnov=1&h=...[&horig=...]`. Si no hay
     * campos que firmar devuelve igualmente `&hnov=1&h=...`.
     *
     * @return string
     */
    public function linkSinValParams(): string
{
    return $this->buildLinkSinVal(true);
}

    private function buildLinkSinVal(bool $asParams): string
{
    $CamposFormSorted = $this->ordenarQuery($this->sCamposForm);

    $url = $this->sUrl;
    $sUrl_full = self::FullPath($url);
    if (!empty($CamposFormSorted)) {
        $sUrl_full .= '?' . $CamposFormSorted;
    }

    $rta = self::md($sUrl_full);
    $HashCamposForm = $rta['hash'];
    $HashCamposFormOrig = $rta['orig'];

    if ($asParams) {
        // El consumidor ya trae parametros (campo=valor&...); encadenamos
        // con '&' para no romper el parsing del body POST.
        $firstSep = '&';
    } else {
        // Primer separador: si la base ya lleva query (?foo=), encadenar
        // con '&'; si no, hace falta '?' (evita .../ruta&hnov=1 que rompe
        // el routing al concatenar a la URL).
        $firstSep = str_contains((string)$url, '?') ? '&' : '?';
    }
    $query = $firstSep . 'hnov=1&h=' . $HashCamposForm;
    if (!empty($this->sCamposNo)) {
        $query .= '&hno=' . rawurlencode($this->sCamposNo);
    }
    if (OrbixRuntime::isDebug()) {
        $query .= '&horig=' . rawurlencode($HashCamposFormOrig);
    }
    return $query;
}

    /**
     * @param array<string, mixed> $aParam
     * @return array<string, mixed>
     */
    private static function prepareParametrosPosicionBack(array $aParam): array
    {
        self::$aValoresCamposNo = [];
        foreach (['h', 'hh', 'hhc', 'horig', 'hhorig', 'hc', 'hchk', 'hno', 'hnov'] as $metaHashKey) {
            unset($aParam[$metaHashKey]);
        }
        unset($aParam['PHPSESSID'], $aParam['atras']);
        $aParam = self::stripPostCamposUiDinamicos($aParam);
        $aParam['hpos'] = 1;

        return $aParam;
    }

    /**
     * Hash de vuelta por pila ({@see add_hash} y {@see validatePost} con `hpos=1`).
     *
     * @param array<string, mixed> $aParam POST crudo o ya preparado con {@see prepareParametrosPosicionBack}
     * @return array{orig: string, hash: string}
     */
    private static function mdForPosicionBack(string $scriptPath, array $aParam, bool $alreadyPrepared = false): array
    {
        $prepared = $alreadyPrepared ? $aParam : self::prepareParametrosPosicionBack($aParam);
        $aParamSorted = self::ordenarArrayParam($prepared);
        $sUrl = self::FullPath($scriptPath);
        if ($aParamSorted !== []) {
            array_walk($aParamSorted, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
            $sUrl .= '?' . http_build_query($aParamSorted);
        }

        return self::md($sUrl);
    }

    /**
     * Sólo la usa web\Posicion.
     *   => elimino hnov. (si existiera). Se cuenta todos los valores de los campos.
     *   => añado hpos (viene de web\Posicion y no un formulario normal)
     *          para indicar al receptor que el hash se calcula con la url incluida.
     *
     * @param array<string, mixed>|string|null $aParam
     */
    public static function add_hash(array|string|null $aParam, string $url): string
{
    if ($aParam === null || $aParam === '') {
        /** @var array<string, mixed> $aParam */
        $aParam = [];
    } elseif (is_string($aParam)) {
        $aParam = self::string2array($aParam);
    }
    $prepared = self::prepareParametrosPosicionBack($aParam);
    $rta = self::mdForPosicionBack($url, $prepared, true);
    $h2 = $rta['hash'];
    $horig = $rta['orig'];

    $prepared['h'] = $h2;
    // después de calcular el hash, añado los campos que no afectan
    $hno = '';
    foreach (self::$aValoresCamposNo as $campo => $value) {
        $prepared[$campo] = $value;
        $hno .= '!' . $campo;
    }
    if (!empty($hno)) {
        $prepared['hno'] = $hno;
    }
    if (OrbixRuntime::isDebug()) {
        //$query .= '&horig='.$horig;
        $prepared['horig'] = $horig;
    }
    array_walk($prepared, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
    $query = http_build_query($prepared);
    return $query;
}

    /**
     * Devuelve los campos en una cadena para usar en llamadas ajax
     */
    public function getParamAjax(): string
{
    $sUrl = $this->getUrl();
    $this->addHiddenToForm();

    $CamposFormSorted = $this->ordenarQuery($this->sCamposForm);
    $rta = self::md($CamposFormSorted);
    $HashCamposForm = $rta['hash'];
    $HashCamposFormOrig = $rta['orig'];

    $CamposHidden = $this->array2stringCamposHidden();
    $aCamposHidden = $this->getArrayCamposHidden();
    $CamposNo = $this->sCamposNo;
    $CamposChk = $this->sCamposChk;

    $aCamposNo = [];
    if (!empty($CamposNo)) {
        $aCamposNo = explode('!', $CamposNo);
    }
    $aCamposH = [];
    foreach ($aCamposHidden as $campo => $valor) {
        //los camposNo, valor = ''.
        if (!empty($CamposNo) && in_array($campo, $aCamposNo)) {
            $aCamposH[$campo] = '';
        } else {
            $aCamposH[$campo] = $valor;
        }
    }
    $rtaH = self::getHashArray($aCamposH);
    $hh = $rtaH['hash'];
    $sparam = $rtaH['orig'];

    $h = $HashCamposForm;
    $hhc = $CamposHidden;

    return "h=$h&hh=$hh&hhc=$hhc&" . $sparam;

}

    /**
     * Devuelve los campos en una cadena para usar en llamadas ajax
     *
     * return string $json_param .= "$parametro: '$valor'";
     */
    public function getParamAjaxEnArray(): string
{
    $paramHash = $this->getParamsHash();
    $json_param = "'h':'" . $paramHash['h'] . "', 'hh':'" . $paramHash['hh'] . "', 'hhc':'" . $paramHash['hhc'] . "'";
    $json_param .= ",'hno':'" . $paramHash['hno'] . "', 'horig':'" . $paramHash['horig'] . "'";

    $aCamposHidden = $this->getArrayCamposHidden();
    foreach ($aCamposHidden as $campo => $valor) {
        $json_param .= ', ';
        $json_param .= "'" . $campo . "': '" . self::asScalarString($valor) . "'";
    }

    return $json_param;
}

    /** @return array<string, mixed> */
    public function getArrayCampos(): array
{
    $paramHash = $this->getParamsHash();
    $aCamposHidden = $this->getArrayCamposHidden();

    return array_merge($paramHash, $aCamposHidden);
}

    /**
     * @return array{h: string, hh: string, hhc: string, hno: string, horig: string}
     */
    private function getParamsHash(): array
{
    $this->addHiddenToForm();

    $CamposFormSorted = $this->ordenarQuery($this->sCamposForm);
    $rta = self::md($CamposFormSorted);
    $HashCamposForm = $rta['hash'];

    $CamposHidden = $this->array2stringCamposHidden();
    $aCamposHidden = $this->getArrayCamposHidden();
    $CamposNo = $this->sCamposNo;

    $aCamposNo = [];
    if (!empty($CamposNo)) {
        $aCamposNo = explode('!', $CamposNo);
    }
    $aCamposH = [];
    foreach ($aCamposHidden as $campo => $valor) {
        //los camposNo, valor = ''.
        if (!empty($CamposNo) && in_array($campo, $aCamposNo)) {
            $aCamposH[$campo] = '';
        } else {
            $aCamposH[$campo] = $valor;
        }
    }
    $rtaH = self::getHashArray($aCamposH);
    $hh = $rtaH['hash'];
    $sparam = $rtaH['orig'];

    $h = $HashCamposForm;
    $hhc = $CamposHidden;

    return [
        'h' => $h,
        'hh' => $hh,
        'hhc' => $hhc,
        'hno' => $CamposNo,
        'horig' => $sparam,
    ];
}


    /* METODES GET AND SETTERS  -----------------------------------------------------------*/
    public function setUrl(string $sUrl): void
    {
        $this->sUrl = $sUrl;
    }

    public function getUrl(): string
    {
        return $this->sUrl;
    }

    public function setCamposChk(string $sCamposChk): void
    {
        $this->sCamposChk = $sCamposChk;
    }

    public function getCamposChk(): string
    {
        return $this->sCamposChk;
    }

    public function setCamposForm(string $sCamposForm): void
    {
        $this->sCamposForm = $sCamposForm;
    }

    public function getCamposForm(): string
    {
        return $this->sCamposForm;
    }

    /** @param array<string, mixed> $aCamposHidden */
    public function setArrayCamposHidden(array $aCamposHidden): void
    {
        $this->aCamposHidden = $aCamposHidden;
    }

    /** @return array<string, mixed> */
    public function getArrayCamposHidden(): array
    {
        return $this->aCamposHidden;
    }

    public function setCamposNo(string $sCamposNo): void
    {
        $this->sCamposNo = $sCamposNo;
    }

    public function getCamposNo(): string
    {
        return $this->sCamposNo;
    }
    /* MÉTODOS PRIVADOS -----------------------------------------------------------*/

    /**
     * Devuelve el html de los campos hidden con su valor
     *
     * @return string html
     */
    private function getCamposHiddenHtml(): string
{
    $prefix = $this->prefix === '' ? '' : $this->prefix . '_';
    $aCamposHidden = $this->aCamposHidden;
    $sCamposHiddenHtml = '';
    foreach ($aCamposHidden as $campo => $valor) {
        if (is_array($valor)) {
            $i = 0;
            foreach ($valor as $val) {
                $nom = $campo . "[$i]";
                $sCamposHiddenHtml .= '<input type="hidden" id="' . $prefix . $nom . '" name="' . $nom . '" value="' . self::asScalarString($val) . "\">\n";
                $i++;
            }

        } else {
            $sCamposHiddenHtml .= '<input type="hidden" id="' . $prefix . $campo . '" name="' . $campo . '" value="' . self::asScalarString($valor) . "\">\n";
        }
    }
    return $sCamposHiddenHtml;
}


    /**
     * Calcula el HashFront(md5) del string que se le pasa. Se añade el id_session y algún carácter más.
     * Por lo que sólo sirve para la misma session.
     *
     * @param string $str
     * @return array{orig: string, hash: string}
     */
    private static function md(string $str): array
{
    $str = rawurldecode(trim($str));

    return [
        'orig' => $str,
        'hash' => md5($str . session_id() . "a+front+"),
    ];
}

    /**
     * Ordenar, para asegurar que es el mismo orden al crearlo que al comprobar
     * No tiene en cuenta los valores
     * me salto los camposNo.
     * Añado los campos Chk (si el valor es null, no se pasan).
     *
     * @param ?string $sCampos (separados por '!')
     * @return string
     */
    private function ordenarQuery(?string $sCampos): string
{
    $sCampos = $sCampos ?? '';
    $a_campos = explode('!', $sCampos);
    $CamposNo = $this->sCamposNo;
    $aCamposNo = [];
    if (!empty($CamposNo)) {
        $aCamposNo = explode('!', $CamposNo);
    }
    $CamposChk = $this->sCamposChk;
    $aCamposChk = [];
    if (!empty($CamposChk)) {
        $aCamposChk = explode('!', $CamposChk);
        $a_campos = array_merge($a_campos, $aCamposChk);
    }

    $sQuery = '';
    sort($a_campos);
    $aCampos = [];
    foreach ($a_campos as $campo) {
        if (!empty($CamposNo) && in_array($campo, $aCamposNo)) continue; //me salto los camposNo.
        // ???????????me salto los campos vacios que no sean chk.
        if (empty($campo) && !in_array($campo, $aCamposChk)) continue; //me salto los campos vacios que no sean chk.
        //if (empty($campo)) continue;
        $sQuery .= empty($sQuery) ? $campo . '=' : '&' . $campo . '=';
        $aCampos[$campo] = '';
    }

    array_walk($aCampos, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
    $sQuery = http_build_query($aCampos);

    return $sQuery;
}

    /**
     * @param array<string, mixed> $aCampos
     * @return array{orig: string, hash: string}
     */
    private static function getHashArray(array $aCampos, int $sin_valor = 0): array
{
    $aCampos['hnov'] = $sin_valor;
    $aParamSorted = self::ordenarArrayParam($aCampos);
    $sUrl_full = '';
    if (!empty($aParamSorted)) $sUrl_full = http_build_query($aParamSorted, '', '&');
    $sUrl_full = str_replace('%21', '!', $sUrl_full);

    $rta = self::md($sUrl_full);
    return $rta;
}

    /**
     * Afagir a la llista de camps Form, els camps hidden.
     *
     */
    private function addHiddenToForm(): void
{
    $sCamposForm = $this->array2stringCamposHidden();
    $this->sCamposForm .= empty($this->sCamposForm) ? $sCamposForm : '!' . $sCamposForm;
}

    /**
     * Devuelve los camposHidden del array en un srting para una url
     *
     * @return string
     */
    private function array2queryCamposHidden(): string
{
    $sCamposHidden = '';
    $aCamposHidden = $this->aCamposHidden;
    if (!empty($aCamposHidden)) {
        foreach ($aCamposHidden as $campo => $valor) {
            $valorStr = self::asScalarString($valor);
            $sCamposHidden .= $sCamposHidden === '' ? $campo . '=' . $valorStr : '&' . $campo . '=' . $valorStr;
        }
    } else { //hay que pasar algo para que lo identifique como formulario y no un link.
        $this->setArrayCamposHidden(array('hola' => 'adios'));
        $sCamposHidden = $this->array2stringCamposHidden();
    }
    return $sCamposHidden;
}

    /**
     * Devuelve los camposHidden del array en un srting separados por '!'
     *
     * @return string
     */
    private function array2stringCamposHidden(): string
{
    $sCamposHidden = '';
    $aCamposHidden = $this->aCamposHidden;
    if (!empty($aCamposHidden)) {
        foreach ($aCamposHidden as $campo => $valor) {
            $sCamposHidden .= empty($sCamposHidden) ? $campo : '!' . $campo;
        }
    } else { //hay que pasar algo para que lo identifique como formulario y no un link.
        $this->setArrayCamposHidden(array('hola' => 'adios'));
        $sCamposHidden = $this->array2stringCamposHidden();
    }
    return $sCamposHidden;
}

    /**
     * Normaliza la URL quitando el segmento sf/ESQUEMA del path.
     * Para que el hash sea el mismo tanto desde el exterior (sf) como desde el interior.
     * Ej: /orbixsf/H-dlbf/apps/... → /orbix/apps/...
     */
    private static function normalizarUrlSinSfEsquema(string $url): string
    {
        $web_path = OrbixRuntime::webPath(); // e.g., "/orbix"
        $esquema = getenv('ESQUEMA'); // e.g., "H-dlbf"
        if (!empty($esquema)) {
            // quitar sf/ESQUEMA: /orbixsf/H-dlbf/ → /orbix/
            $url = str_replace($web_path . 'sf/' . $esquema, $web_path, $url);
        }
        // quitar sf sin esquema: /orbixsf/ → /orbix/
        $url = str_replace($web_path . 'sf', $web_path, $url);
        return $url;
    }

    private function realFullUrl(): string
{
    /* Con el cambio de rutas, no coincide el uri de origen y destino.
     *  El emisor apunta algo asi: /orbix/src/usuarios/preferencias_guardar
     *  y el receptor a lo real: /orbix/src/usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php
     */
    $requestUriRaw = self::serverString('REQUEST_URI');
    $request_uri = parse_url($requestUriRaw, PHP_URL_PATH);
    $request_uri = is_string($request_uri) ? $request_uri : '';
    $pattern = '/(^.*src\/[^\/]+\/)infrastructure\/ui\/http\/controllers\/([^\/]+)\.php$/';
    $replacement = '$1$2';
    $request_uri_modificada = preg_replace($pattern, $replacement, $request_uri);
    $request_uri_modificada = is_string($request_uri_modificada) ? $request_uri_modificada : $request_uri;
    $request_uri_modificada = self::normalizarUrlSinSfEsquema($request_uri_modificada);

    /*
    *  El HTTP_HOST ya lleva el puerto, Pero si el nameserver es la ip NO
    *
    * En la instalación exterior NO va. Seguramente depende si es apache o nginx
     * hay que hacer directamente SERVER_NAME + SERVER_PORT
    *
    */
    $serverAddr = self::serverString('SERVER_ADDR');
    $serverName = self::serverString('SERVER_NAME');
    $requestScheme = self::serverString('REQUEST_SCHEME', 'http');
    $httpHost = self::serverString('HTTP_HOST');
    $serverPort = self::serverString('SERVER_PORT');

    if ($serverAddr === $serverName) {
        $port = $serverPort === '' ? '' : ':' . $serverPort;
        $sUrl = $requestScheme . '://' . $serverName . $port . $request_uri_modificada;
    } else {
        if (str_contains($httpHost, ':')) {
            $sUrl = $requestScheme . '://' . $httpHost . $request_uri_modificada;
        } else {
            $port = $serverPort === '' ? '' : ':' . $serverPort;
            $sUrl = $requestScheme . '://' . $serverName . $port . $request_uri_modificada;
        }
    }
    return $sUrl;
}

    private static function FullPath(string $sPath): string
{
    if ($sPath !== '') {
        if (substr($sPath, 0, 4) === 'http') {
            // lo dejo com está
        } else {
            $sPath = (substr($sPath, 0, 1) === '/') ? $sPath : '/' . $sPath;
        }
        $serverName = self::serverString('SERVER_NAME');
        if (strpos($sPath, $serverName) === false) {
            if (strpos($sPath, OrbixRuntime::webPath()) === false) {
                $sPath = AppUrlConfig::getPublicAppBaseUrl() . $sPath;
            } else {
                $sPath = OrbixRuntime::webServer() . OrbixRuntime::getWebPort() . $sPath;
            }
        }
    }
    // Normalizar quitando sf/ESQUEMA para que el hash coincida con el receptor
    $sPath = self::normalizarUrlSinSfEsquema($sPath);
    return $sPath;
}

    /**
     * Convierte `campo[0]`, `campo[1]`, … en la clave canónica `campo` cuando el hash del
     * formulario se generó solo con `campo` (p. ej. {@see DesplegableArray} + CasasQue).
     *
     * @param array<string, mixed> $aPOST
     * @param list<string> $canonicalBases
     * @return array<string, mixed>
     */
    private static function collapseIndexedPostKeysToCanonical(array $aPOST, array $canonicalBases): array
    {
        foreach ($canonicalBases as $base) {
            // PHP agrupa `id_cdc[0]=…` en `$_POST['id_cdc'][0]`, no como clave literal `id_cdc[0]`.
            if (isset($aPOST[$base]) && is_array($aPOST[$base])) {
                $aPOST[$base] = '';
            }
            $pattern = '/^' . preg_quote($base, '/') . '\[\d+]$/';
            $hadIndexed = false;
            foreach (array_keys($aPOST) as $k) {
                if (preg_match($pattern, (string)$k)) {
                    $hadIndexed = true;
                    unset($aPOST[$k]);
                }
            }
            if ($hadIndexed && !array_key_exists($base, $aPOST)) {
                $aPOST[$base] = '';
            }
        }

        return $aPOST;
    }

    /**
     * Devuelve los parametros preparados para calcular el hash.
     *
     * Ordena los parametros para que al calcular el hash  estén siempre en el mismo orden.
     * Quita los parametros que no se deben incluir en el hash.
     * Elimina los valores de los parametros si hnov=1.
     *
     * @param array<string, mixed> $aParam
     * @return array<string, mixed>
     */
    private static function ordenarArrayParam(array $aParam): array
{
    if ($aParam !== []) {
        $aPOST = $aParam;
        // campos que se deben quitar del hash; separados por !.
        $hno = self::asPostString($aPOST['hno'] ?? null);
        if ($hno !== '') {
            $a_campos_no = explode('!', $hno);
            foreach ($a_campos_no as $campo) {
                if (isset($aPOST[$campo])) {
                    self::$aValoresCamposNo[$campo] = $aPOST[$campo];
                    unset($aPOST[$campo]);
                }
            }
        }
        // Indica que los campos deben estar sin valores en el hash;
        $hnov = self::asPostString($aPOST['hnov'] ?? null);
        if ($hnov === '1') { // borro posibles los valores de los campos
            foreach ($aPOST as $camp => $valor) {
                $aPOST[$camp] = '';
            }
        }
        //var_dump($aPOST);
        unset($aPOST['PHPSESSID']);
        unset($aPOST['atras']);
        unset($aPOST['h']);
        unset($aPOST['hc']);
        unset($aPOST['horig']);
        unset($aPOST['hh']);
        unset($aPOST['hhc']);
        unset($aPOST['hhorig']);
        unset($aPOST['hno']);
        unset($aPOST['hchk']);
        unset($aPOST['hnov']);
        foreach ($aPOST as $key => $val) {
            if (str_starts_with($key, 'scroll_id_')) {
                unset($aPOST[$key]);
            }
        }
        $aPOST = self::stripPostCamposUiDinamicos($aPOST);
        // DesplegableArray envía `name="id_cdc[n]"` → PHP usa `$_POST['id_cdc'][n]` (array) o, en
        // entornos raros, claves literales `id_cdc[n]`. El hash del formulario usa la clave
        // canónica `id_cdc` (ver ordenarQuery sobre sCamposForm). Sin normalizar, validatePost
        // puede fallar → 302 a index.php.
        $aPOST = self::collapseIndexedPostKeysToCanonical($aPOST, ['id_cdc']);
        ksort($aPOST);
        array_walk($aPOST, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
        return $aPOST;
    }

    return [];
}

    private static function ordenarParam(string $sUrl): string
{
    $aParam = parse_url($sUrl);
    if ($aParam === false) { // la url puede contener ip en vez de nombre
        $matches = [];
        $regex = "^(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?";
        preg_match("@$regex@", $sUrl, $matches);
        $aParam = [
            'path' => $matches[3] ?? '',
            'query' => !empty($matches[5]) ? $matches[5] : '',
        ];
    }
    $path = is_string($aParam['path'] ?? null) ? $aParam['path'] : '';
    $sPath = $path !== '' ? self::FullPath($path) : self::FullPath('');
    if (!empty($aParam['query'])) {
        $aQuery = self::string2array($aParam['query']);
        $aQuerySorted = self::ordenarArrayParam($aQuery);
        $sUrl = $sPath . '?' . http_build_query($aQuerySorted);
    } else {
        $sUrl = $sPath;
    }
    return $sUrl;
}

    /**
     * Convierte una cadena http query en un array.
     *
     * @param non-empty-string $and separador '&'
     * @return array<string, string>
     */
    private static function string2array(string $sParam, string $and = '&'): array
{
    $aParam = [];
    if (!empty($sParam)) { //si no hay no hace falta ordenar nada.
        $sParam = urldecode($sParam);
        $aParejas = explode($and, $sParam);
        foreach ($aParejas as $pareja) {
            $alist = explode('=', $pareja);
            $campo = $alist[0];
            // ojo con los ceros. esto no funciona:
            //$valor = empty($alist[1])? '' : $alist[1];
            $valor = !isset($alist[1]) ? '' : $alist[1];
            $aParam[$campo] = $valor;
        }
    }
    return $aParam;
}


    /**
     * @return mixed
     */
    public function getPrefix()
{
    return $this->prefix;
}

    public function setPrefix(string $prefix): void
{
    $this->prefix = $prefix;
}

}
