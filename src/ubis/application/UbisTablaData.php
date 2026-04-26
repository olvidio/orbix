<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionUbiDireccionRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use web\Hash;
use function src\shared\domain\helpers\is_true;
use function src\shared\domain\helpers\urlsafe_b64decode;
use function src\shared\domain\helpers\urlsafe_b64encode;

final class UbisTablaData
{
    use ProvidesRepositories;

    public static function execute(array $p): array
    {
        return (new self())->build($p);
    }

    private function build(array $p): array
    {
        $miSfsv = ConfigGlobal::mi_sfsv();
        $in = $this->parseInput($p);

        // ----- Resolver condiciones de búsqueda -----
        if (empty($in['sWhere'])) {
            $filtros = $this->buildFiltrosDesdeInput($p, $in, $miSfsv);
        } else {
            $filtros = $this->buildFiltrosDesdeHash($in);
        }

        $aWhere = $filtros['aWhere'];
        $aOperador = $filtros['aOperador'];
        $aWhereD = $filtros['aWhereD'];
        $aOperadorD = $filtros['aOperadorD'];
        $Qobj_pau = $filtros['Qobj_pau'];
        $titulo = $filtros['titulo'];
        $metodo = $filtros['metodo'];
        $repoDir = $filtros['repoDir'];
        $Qnombre_ubi = $filtros['Qnombre_ubi'];
        $Qdl = $filtros['Qdl'];
        $Qregion = $filtros['Qregion'];
        $tipo_ubi = $filtros['tipo_ubi'];

        if (empty($aWhere) && empty($aWhereD)) {
            return ['error' => _("debe poner algún criterio de búsqueda")];
        }

        // ----- Buscar ubis por nombre y por dirección -----
        $cUbis = $this->buscarUbisPorNombre($aWhere, $aOperador, $Qobj_pau, $in['Qcmb'], $miSfsv);
        $cUbisD = $this->buscarUbisPorDireccion($aWhereD, $aOperadorD, $repoDir, $in['Qcmb']);

        // ----- Mezclar y deduplicar -----
        [$cUbisTot] = $this->combinarColecciones($cUbis, $cUbisD);

        // ----- Serializar condiciones para mantener estado -----
        $sWhere = urlsafe_b64encode(json_encode($aWhere), JSON_THROW_ON_ERROR);
        $sOperador = urlsafe_b64encode(json_encode($aOperador), JSON_THROW_ON_ERROR);
        $sWhereD = urlsafe_b64encode(json_encode($aWhereD), JSON_THROW_ON_ERROR);
        $sOperadorD = urlsafe_b64encode(json_encode($aOperadorD), JSON_THROW_ON_ERROR);

        [$nueva_ficha, $pagina_link] = $this->calcularNuevaFicha(
            $in['Qtipo'],
            $in['Qloc'],
            $cUbisTot,
            $Qobj_pau,
            $tipo_ubi,
            $Qnombre_ubi,
            $Qdl,
            $Qregion
        );

        $aGoBack = [
            'tipo' => $in['Qtipo'],
            'loc' => $in['Qloc'],
            'sWhere' => $sWhere,
            'sOperador' => $sOperador,
            'obj_pau' => $Qobj_pau,
            'sWhereD' => $sWhereD,
            'sOperadorD' => $sOperadorD,
            'metodo' => $metodo,
            'titulo' => $titulo,
        ];

        $a_botones = [['txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)"]];
        if ($_SESSION['oPerm']->have_perm_oficina('scl')) {
            $a_botones[] = ['txt' => _("eliminar"), 'click' => "fnjs_borrar(this.form)"];
        }

        $a_cabeceras = [
            ['name' => ucfirst(_("nombre del centro")), 'formatter' => 'clickFormatter'],
            _("tipo"),
            _("dl"),
            ucfirst(_("región")),
            ucfirst(_("dirección")),
            _("cp"),
            ucfirst(_("ciudad")),
        ];

        $a_valores = $this->formatearFilas($cUbisTot, $in['Qid_sel'], $in['Qscroll_id']);

        return [
            'titulo' => $titulo,
            'nueva_ficha' => $nueva_ficha,
            'pagina_link' => $pagina_link,
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'a_botones' => $a_botones,
            'go_back' => $aGoBack,
            'hash_hidden' => [
                'tipo' => $in['Qtipo'],
                'loc' => $in['Qloc'],
                'sWhere' => $sWhere,
                'sOperador' => $sOperador,
                'obj_pau' => $Qobj_pau,
                'metodo' => $metodo,
                'titulo' => $titulo,
            ],
        ];
    }

    /**
     * Normaliza los parámetros de entrada del request.
     */
    private function parseInput(array $p): array
    {
        $Qloc = (string)($p['loc'] ?? '');
        $Qtipo = (string)($p['tipo'] ?? '');
        $Qsimple = (int)($p['simple'] ?? 0);
        if ($Qsimple === 1) {
            $Qtipo = 'tot';
            $Qloc = 'tot';
        }

        return [
            'Qloc' => $Qloc,
            'Qtipo' => $Qtipo,
            'Qsimple' => $Qsimple,
            'sWhere' => (string)($p['sWhere'] ?? ''),
            'sOperador' => (string)($p['sOperador'] ?? ''),
            'sWhereD' => (string)($p['sWhereD'] ?? ''),
            'sOperadorD' => (string)($p['sOperadorD'] ?? ''),
            'metodo' => (string)($p['metodo'] ?? ''),
            'titulo' => (string)($p['titulo'] ?? ''),
            'Qcmb' => (string)($p['cmb'] ?? ''),
            'Qobj_pau' => $p['obj_pau'] ?? '',
            'Qid_sel' => isset($p['id_sel']) && (string)$p['id_sel'] !== '' ? $p['id_sel'] : null,
            'Qscroll_id' => isset($p['scroll_id']) && (string)$p['scroll_id'] !== '' ? $p['scroll_id'] : null,
        ];
    }

    /**
     * Construye las condiciones aWhere/aWhereD a partir de parámetros de formulario.
     */
    private function buildFiltrosDesdeInput(array $p, array $in, int $miSfsv): array
    {
        $Qtipo = $in['Qtipo'];
        $Qloc = $in['Qloc'];
        $Qobj_pau = $in['Qobj_pau'];
        $titulo = $in['titulo'];

        $tipo_ubi = $Qtipo . $Qloc;
        // si es sf, el tipo_ubi = ctrsf
        if ($tipo_ubi === 'ctrdl' && $miSfsv === 2) {
            $tipo_ubi = 'ctrsf';
        }

        $aWhere = [];
        $aOperador = [];
        $aWhereD = [];
        $aOperadorD = [];

        $Qnombre_ubi = (string)($p['nombre_ubi'] ?? '');
        if ($Qnombre_ubi !== '') {
            $nom_ubi = str_replace('+', "\+", $Qnombre_ubi); // para los centros de la sss+
            $aWhere['nombre_ubi'] = $nom_ubi;
            $aOperador['nombre_ubi'] = 'sin_acentos';
            $aWhere['_ordre'] = 'tipo_ubi,nombre_ubi';
        }
        $Qregion = (string)($p['region'] ?? '');
        if ($Qregion !== '') {
            $aWhere['region'] = $Qregion;
            $aWhere['_ordre'] = 'nombre_ubi';
        }
        $Qdl = (string)($p['dl'] ?? '');
        if ($Qdl !== '') {
            $aWhere['dl'] = $Qdl;
            $aOperador['dl'] = 'sin_acentos';
            $aWhere['_ordre'] = 'dl';
        }
        $Qtipo_ctr = (string)($p['tipo_ctr'] ?? '');
        if ($Qtipo_ctr !== '') {
            $aWhere['tipo_ctr'] = $Qtipo_ctr;
            $aOperador['tipo_ctr'] = 'sin_acentos';
            $aWhere['_ordre'] = 'tipo_ctr';
        }
        $Qtipo_casa = (string)($p['tipo_casa'] ?? '');
        if ($Qtipo_casa !== '') {
            $aWhere['tipo_casa'] = $Qtipo_casa;
            $aOperador['tipo_casa'] = 'sin_acentos';
            $aWhere['_ordre'] = 'tipo_casa';
        }

        $Qciudad = (string)($p['ciudad'] ?? '');
        if ($Qciudad !== '') {
            $aWhereD['poblacion'] = $Qciudad;
            $aOperadorD['poblacion'] = 'sin_acentos';
            $aWhereD['_ordre'] = 'poblacion';
        }
        $Qpais = (string)($p['pais'] ?? '');
        if ($Qpais !== '') {
            $aWhereD['pais'] = $Qpais;
            $aOperadorD['pais'] = 'sin_acentos';
            $aWhereD['_ordre'] = 'pais';
        }

        [$Qobj_pau, $titulo, $aWhere] = $this->resolverObjPauYTitulo(
            $Qtipo,
            $Qloc,
            $Qobj_pau,
            $titulo,
            $aWhere,
            $miSfsv
        );

        $repoDir = '';
        if (!empty($Qobj_pau)) {
            $repoDir = [];
            foreach ((array)$Qobj_pau as $Qobj_pau_i) {
                $repoDir[] = $this->getDireccionRepositoryClass($Qobj_pau_i);
            }
        }

        return [
            'aWhere' => $aWhere,
            'aOperador' => $aOperador,
            'aWhereD' => $aWhereD,
            'aOperadorD' => $aOperadorD,
            'Qobj_pau' => $Qobj_pau,
            'titulo' => $titulo,
            'metodo' => $in['metodo'],
            'repoDir' => $repoDir,
            'Qnombre_ubi' => $Qnombre_ubi,
            'Qdl' => $Qdl,
            'Qregion' => $Qregion,
            'tipo_ubi' => $tipo_ubi,
        ];
    }

    /**
     * Decide el objeto pau y el título de la tabla según ($Qtipo, $Qloc).
     *
     * @return array{0: mixed, 1: string, 2: array} [Qobj_pau, titulo, aWhere]
     */
    private function resolverObjPauYTitulo(
        string $Qtipo,
        string $Qloc,
        mixed $Qobj_pau,
        string $titulo,
        array $aWhere,
        int $miSfsv
    ): array {
        $permisoSf = $_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('des');

        switch ($Qtipo) {
            case 'ctr':
                switch ($Qloc) {
                    case 'dl':
                        $titulo = ucfirst(_("tabla de centros de la delegación"));
                        $Qobj_pau = 'CentroDl';
                        break;
                    case 'ex':
                        $titulo = ucfirst(_("tabla de centros de fuera de la delegación"));
                        $Qobj_pau = 'CentroEx';
                        break;
                    case 'sf':
                        if ($permisoSf) {
                            $titulo = ucfirst(_("tabla de centros de la delegación femenina"));
                            $Qobj_pau = 'CentroDl';
                        }
                        break;
                    case 'tot':
                        $titulo = ucfirst(_("tabla de toda las casas y centros"));
                        $Qobj_pau = 'Centro';
                        if ($miSfsv === 1) {
                            $aWhere['sv'] = 't';
                        } elseif ($miSfsv === 2) {
                            $aWhere['sf'] = 't';
                        }
                        break;
                }
                break;
            case 'cdc':
                switch ($Qloc) {
                    case 'dl':
                        $titulo = ucfirst(_("tabla de casas de la delegación"));
                        $Qobj_pau = 'CasaDl';
                        // Para que salgan todas si no hay otro criterio.
                        $aWhere['active'] = 't';
                        break;
                    case 'ex':
                        $titulo = ucfirst(_("tabla de casas de fuera de la delegación"));
                        $Qobj_pau = 'CasaEx';
                        break;
                    case 'sf':
                        if ($permisoSf) {
                            $titulo = ucfirst(_("tabla de casas de la sf"));
                            $Qobj_pau = 'CasaDl';
                            $aWhere['sf'] = 't';
                        }
                        break;
                    case 'tot':
                        $titulo = ucfirst(_("tabla de toda las casas y centros"));
                        $Qobj_pau = 'Casa';
                        break;
                }
                break;
            case 'tot':
                switch ($Qloc) {
                    case 'dl':
                        $titulo = ucfirst(_("tabla de casas y centros de la delegación"));
                        $Qobj_pau = 'Centro';
                        break;
                    case 'ex':
                        $titulo = ucfirst(_("tabla de casas y centros de fuera de la delegación"));
                        $Qobj_pau = 'Centro';
                        break;
                    case 'sf':
                        if ($permisoSf) {
                            $titulo = ucfirst(_("tabla de toda las casas y centros"));
                            $Qobj_pau = 'Centro';
                            $aWhere['sf'] = 't';
                        }
                        break;
                    case 'tot':
                        $Qobj_pau = ['Centro', 'Casa'];
                        $titulo = ucfirst(_("tabla de toda las casas y centros"));
                        break;
                }
                break;
        }

        return [$Qobj_pau, $titulo, $aWhere];
    }

    /**
     * Reconstruye las condiciones a partir del hash serializado (navegación posterior).
     */
    private function buildFiltrosDesdeHash(array $in): array
    {
        $aWhere = json_decode(urlsafe_b64decode($in['sWhere']), true) ?: [];
        $aOperador = json_decode(urlsafe_b64decode($in['sOperador']), true) ?: [];
        $aWhereD = json_decode(urlsafe_b64decode($in['sWhereD']), true) ?: [];
        $aOperadorD = json_decode(urlsafe_b64decode($in['sOperadorD']), true) ?: [];

        $Qobj_pau = $in['Qobj_pau'];
        $metodo = $in['metodo'];
        $repoDir = '';
        if (!empty($Qobj_pau)) {
            $metodo = $this->getMetodo($Qobj_pau);
            $repoDir = $this->getDireccionRepositoryClass($Qobj_pau);
        }

        return [
            'aWhere' => $aWhere,
            'aOperador' => $aOperador,
            'aWhereD' => $aWhereD,
            'aOperadorD' => $aOperadorD,
            'Qobj_pau' => $Qobj_pau,
            'titulo' => $in['titulo'],
            'metodo' => $metodo,
            'repoDir' => $repoDir,
            'Qnombre_ubi' => '',
            'Qdl' => '',
            'Qregion' => '',
            'tipo_ubi' => $in['Qtipo'] . $in['Qloc'],
        ];
    }

    private function buscarUbisPorNombre(array &$aWhere, array $aOperador, mixed $Qobj_pau, string $Qcmb, int $miSfsv): array
    {
        $cUbis = [];
        if (empty($aWhere)) {
            return $cUbis;
        }

        if (!is_true($Qcmb)) {
            $aWhere['active'] = 't';
        }
        // Filtro sf/sv por defecto cuando se busca sólo por nombre (si no ya está
        // filtrado por otra columna, igualmente hace falta distinguir casas/centros).
        if ($miSfsv === 1) {
            $aWhere['sv'] = 't';
        } elseif ($miSfsv === 2) {
            $aWhere['sf'] = 't';
        }

        if (empty($Qobj_pau)) {
            return $cUbis;
        }

        foreach ((array)$Qobj_pau as $Qobj_pau_i) {
            $metodo = $this->getMetodo($Qobj_pau_i);
            $UbiRepository = $this->getRepository($Qobj_pau_i);
            $cUbis_i = $UbiRepository->$metodo($aWhere, $aOperador);
            foreach ($cUbis_i as $ubi) {
                $cUbis[] = $ubi;
            }
        }

        return $cUbis;
    }

    private function buscarUbisPorDireccion(array $aWhereD, array $aOperadorD, mixed $repoDir, string $Qcmb): array
    {
        $cUbisD = [];
        if (empty($aWhereD) || empty($repoDir)) {
            return $cUbisD;
        }

        foreach ((array)$repoDir as $repoDir_i) {
            $DireccionesRepository = $GLOBALS['container']->get($repoDir_i);
            $cDirecciones = $DireccionesRepository->getDirecciones($aWhereD, $aOperadorD) ?: [];
            $repoRelacion = $this->relacionDireccionPara($repoDir_i);
            $RelacionRepository = $GLOBALS['container']->get($repoRelacion);
            foreach ($cDirecciones as $oDireccion) {
                $id_direccion = $oDireccion->getId_direccion();
                $cIdUbis = $RelacionRepository->getUbisPorDireccion($id_direccion);
                foreach ($cIdUbis as $aUbi) {
                    $oUbi = Ubi::NewUbi($aUbi['id_ubi']);
                    if ($oUbi === null) {
                        continue;
                    }
                    if (!is_true($Qcmb) && method_exists($oUbi, 'isActive') && !$oUbi->isActive()) {
                        continue;
                    }
                    $cUbisD[] = $oUbi;
                }
            }
        }

        return $cUbisD;
    }

    /**
     * Mapea cada repositorio de dirección al repositorio de relación ubi↔dirección correspondiente.
     */
    private function relacionDireccionPara(string $direccionRepoClass): string
    {
        $map = [
            DireccionCentroDlRepositoryInterface::class => RelacionCentroDlDireccionRepositoryInterface::class,
            DireccionCentroExRepositoryInterface::class => RelacionCentroExDireccionRepositoryInterface::class,
            DireccionCentroRepositoryInterface::class => RelacionCentroDireccionRepositoryInterface::class,
            DireccionCasaDlRepositoryInterface::class => RelacionCasaDlDireccionRepositoryInterface::class,
            DireccionCasaExRepositoryInterface::class => RelacionCasaExDireccionRepositoryInterface::class,
            DireccionCasaRepositoryInterface::class => RelacionCasaDireccionRepositoryInterface::class,
        ];
        return $map[$direccionRepoClass] ?? RelacionUbiDireccionRepositoryInterface::class;
    }

    /**
     * Combina las colecciones por nombre y por dirección haciendo intersección cuando corresponde,
     * descarta duplicados y ordena por región/nombre.
     *
     * @return array{0: array} [cUbisTot]
     */
    private function combinarColecciones(array $cUbis, array $cUbisD): array
    {
        $aUbisIntersec = [];
        if (!empty($cUbis) && !empty($cUbisD)) {
            $aUbis = array_map(static fn($oUbi) => $oUbi->getId_ubi(), $cUbis);
            $aUbisD = array_map(static fn($oUbi) => $oUbi->getId_ubi(), $cUbisD);
            $aUbisIntersec = array_values(array_intersect($aUbis, $aUbisD));
        } elseif (!empty($cUbisD)) {
            $cUbis = $cUbisD;
        }

        $aUbisIntersecLookup = !empty($aUbisIntersec) ? array_flip($aUbisIntersec) : [];
        $aUbis = [];
        $cUbisTot = [];
        $a_region = [];
        $a_nom = [];
        foreach ($cUbis as $key => $oUbi) {
            $id_ubi = $oUbi->getId_ubi();
            if (!empty($aUbisIntersecLookup) && !isset($aUbisIntersecLookup[$id_ubi])) {
                continue;
            }
            if (isset($aUbis[$id_ubi])) {
                continue;
            }
            $aUbis[$id_ubi] = true;
            $cUbisTot[$key] = $oUbi;
            $a_region[$key] = strtolower($oUbi->getRegion() ?? '');
            $a_nom[$key] = strtolower($oUbi->getNombre_ubi() ?? '');
        }

        array_multisort($a_region, SORT_LOCALE_STRING, SORT_ASC, $a_nom, SORT_LOCALE_STRING, SORT_ASC, $cUbisTot);

        return [$cUbisTot];
    }

    /**
     * Calcula (nueva_ficha, pagina_link) según haya o no resultados y el tipo de búsqueda.
     *
     * @return array{0: string, 1: string}
     */
    private function calcularNuevaFicha(
        string $Qtipo,
        string $Qloc,
        array $cUbisTot,
        mixed $Qobj_pau,
        string $tipo_ubi,
        string $Qnombre_ubi,
        string $Qdl,
        string $Qregion
    ): array {
        $nueva_ficha = '';
        $pagina_link = '';
        $sinResultados = count($cUbisTot) === 0;

        if ($Qtipo === 'tot' || $Qloc === 'tot') {
            if ($sinResultados) {
                $nueva_ficha = 'especificar';
                $pagina_link = Hash::link('frontend/ubis/controller/ubis_buscar.php?' . http_build_query(['simple' => '2']));
            }
            return [$nueva_ficha, $pagina_link];
        }

        $nueva_ficha = 'nueva';
        $a_link = [
            'obj_pau' => $Qobj_pau,
            'tipo_ubi' => $tipo_ubi,
            'nombre_ubi' => $Qnombre_ubi,
            'nuevo' => 1,
            'dl' => $Qdl,
            'region' => $Qregion,
        ];
        $pagina_link = Hash::link(ConfigGlobal::getWeb() . '/frontend/ubis/controller/ubis_editar.php?' . http_build_query($a_link));
        if ($sinResultados) {
            $nueva_ficha = 'aviso';
        }
        return [$nueva_ficha, $pagina_link];
    }

    /**
     * Formatea la colección de ubis en la estructura a_valores de la tabla.
     */
    private function formatearFilas(array $cUbisTot, mixed $Qid_sel, mixed $Qscroll_id): array
    {
        $a_valores = [];
        if (!empty($Qid_sel)) {
            $a_valores['select'] = $Qid_sel;
        }
        if (!empty($Qscroll_id)) {
            $a_valores['scroll_id'] = $Qscroll_id;
        }

        $i = 0;
        foreach ($cUbisTot as $oUbi) {
            $i++;
            $id_ubi = $oUbi->getId_ubi();
            $nombre_ubi = $oUbi->getNombre_ubi();

            $cDirecciones = $oUbi->getDirecciones();
            $poblacion = '';
            $pais = '';
            $direccion = '';
            $c_p = '';
            if (is_array($cDirecciones) && !empty($cDirecciones)) {
                foreach ($cDirecciones as $oDireccion) {
                    // Comportamiento preexistente: sólo toma la última dirección.
                    $poblacion = $oDireccion->getPoblacionVo()->value();
                    $pais = $oDireccion->getPaisVo()?->value() ?? '';
                    $direccion = $oDireccion->getDireccionVo()?->value() ?? '';
                    $c_p = $oDireccion->getCodigoPostalVo()?->value() ?? '';
                }
            }

            $pagina = Hash::link('frontend/ubis/controller/home_ubis.php?' . http_build_query(['pau' => 'u', 'id_ubi' => $id_ubi]));

            $a_valores[$i]['sel'] = $id_ubi;
            $a_valores[$i][1] = ['ira' => $pagina, 'valor' => $nombre_ubi];
            $a_valores[$i][2] = $oUbi->getTipo_ubi();
            $a_valores[$i][3] = $oUbi->getDl();
            $a_valores[$i][4] = $oUbi->getRegion();
            $a_valores[$i][5] = $direccion;
            $a_valores[$i][6] = $c_p;
            $a_valores[$i][7] = $poblacion;
            // Nota: $pais se calcula pero no se muestra en la tabla actual.
            unset($pais);
        }

        return $a_valores;
    }
}
