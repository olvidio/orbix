<?php

namespace src\ubis\application;

use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;
use src\ubis\domain\entity\CentroEx;
use src\ubis\domain\entity\Direccion;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;
use function src\shared\domain\helpers\urlsafe_b64decode;
use function src\shared\domain\helpers\urlsafe_b64encode;

final class UbisTablaData
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
        private UbiFactory $ubiFactory,
    ) {
    }

    /**
     * @param array<string, mixed> $p
     * @return array<string, mixed>
     */
    public function execute(array $p): array
    {
        return $this->build($p);
    }

    /**
     * @param array<string, mixed> $p
     * @return array<string, mixed>
     */
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

        /** @var array<string, mixed> $aWhere */
        $aWhere = is_array($filtros['aWhere'] ?? null) ? $filtros['aWhere'] : [];
        /** @var array<string, string> $aOperador */
        $aOperador = is_array($filtros['aOperador'] ?? null) ? $filtros['aOperador'] : [];
        /** @var array<string, mixed> $aWhereD */
        $aWhereD = is_array($filtros['aWhereD'] ?? null) ? $filtros['aWhereD'] : [];
        /** @var array<string, string> $aOperadorD */
        $aOperadorD = is_array($filtros['aOperadorD'] ?? null) ? $filtros['aOperadorD'] : [];
        $Qobj_pauRaw = $filtros['Qobj_pau'] ?? '';
        $Qobj_pau = is_array($Qobj_pauRaw) ? $Qobj_pauRaw : (is_string($Qobj_pauRaw) ? $Qobj_pauRaw : '');
        $titulo = is_string($filtros['titulo'] ?? null) ? $filtros['titulo'] : '';
        $metodo = is_string($filtros['metodo'] ?? null) ? $filtros['metodo'] : '';
        $repoDir = $filtros['repoDir'] ?? '';
        $Qnombre_ubi = is_string($filtros['Qnombre_ubi'] ?? null) ? $filtros['Qnombre_ubi'] : '';
        $Qdl = is_string($filtros['Qdl'] ?? null) ? $filtros['Qdl'] : '';
        $Qregion = is_string($filtros['Qregion'] ?? null) ? $filtros['Qregion'] : '';
        $tipo_ubi = is_string($filtros['tipo_ubi'] ?? null) ? $filtros['tipo_ubi'] : '';

        if (empty($aWhere) && empty($aWhereD)) {
            return ['error' => _("debe poner algún criterio de búsqueda")];
        }

        // ----- Buscar ubis por nombre y por dirección -----
        $qcmb = is_string($in['Qcmb'] ?? null) ? $in['Qcmb'] : '';
        $cUbis = $this->buscarUbisPorNombre($aWhere, $aOperador, $Qobj_pau, $qcmb, $miSfsv);
        $cUbisD = $this->buscarUbisPorDireccion($aWhereD, $aOperadorD, $repoDir, $qcmb);

        // ----- Mezclar y deduplicar -----
        [$cUbisTot] = $this->combinarColecciones($cUbis, $cUbisD);

        // ----- Serializar condiciones para mantener estado -----
        $sWhere = urlsafe_b64encode(json_encode($aWhere, JSON_THROW_ON_ERROR));
        $sOperador = urlsafe_b64encode(json_encode($aOperador, JSON_THROW_ON_ERROR));
        $sWhereD = urlsafe_b64encode(json_encode($aWhereD, JSON_THROW_ON_ERROR));
        $sOperadorD = urlsafe_b64encode(json_encode($aOperadorD, JSON_THROW_ON_ERROR));

        $qtipo = is_string($in['Qtipo'] ?? null) ? $in['Qtipo'] : '';
        $qloc = is_string($in['Qloc'] ?? null) ? $in['Qloc'] : '';
        [$nueva_ficha, $pagina_link_spec] = $this->calcularNuevaFicha(
            $qtipo,
            $qloc,
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
        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('scl')) {
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
            'pagina_link_spec' => $pagina_link_spec,
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
     *
     * @param array<string, mixed> $p
     * @return array<string, mixed>
     */
    private function parseInput(array $p): array
    {
        $Qloc = input_string($p, 'loc');
        $Qtipo = input_string($p, 'tipo');
        $Qsimple = input_int($p, 'simple');
        if ($Qsimple === 1) {
            $Qtipo = 'tot';
            $Qloc = 'tot';
        }

        return [
            'Qloc' => $Qloc,
            'Qtipo' => $Qtipo,
            'Qsimple' => $Qsimple,
            'sWhere' => is_string($p['sWhere'] ?? null) ? $p['sWhere'] : '',
            'sOperador' => is_string($p['sOperador'] ?? null) ? $p['sOperador'] : '',
            'sWhereD' => is_string($p['sWhereD'] ?? null) ? $p['sWhereD'] : '',
            'sOperadorD' => is_string($p['sOperadorD'] ?? null) ? $p['sOperadorD'] : '',
            'metodo' => is_string($p['metodo'] ?? null) ? $p['metodo'] : '',
            'titulo' => is_string($p['titulo'] ?? null) ? $p['titulo'] : '',
            'Qcmb' => is_string($p['cmb'] ?? null) ? $p['cmb'] : '',
            'Qobj_pau' => $p['obj_pau'] ?? '',
            'Qid_sel' => isset($p['id_sel']) && is_string($p['id_sel']) && $p['id_sel'] !== '' ? $p['id_sel'] : null,
            'Qscroll_id' => isset($p['scroll_id']) && is_string($p['scroll_id']) && $p['scroll_id'] !== '' ? $p['scroll_id'] : null,
        ];
    }

    /**
     * Construye las condiciones aWhere/aWhereD a partir de parámetros de formulario.
     *
     * @param array<string, mixed> $p
     * @param array<string, mixed> $in
     * @return array<string, mixed>
     */
    private function buildFiltrosDesdeInput(array $p, array $in, int $miSfsv): array
    {
        $Qtipo = is_string($in['Qtipo'] ?? null) ? $in['Qtipo'] : '';
        $Qloc = is_string($in['Qloc'] ?? null) ? $in['Qloc'] : '';
        $QobjPauInput = $in['Qobj_pau'] ?? '';
        if (is_string($QobjPauInput)) {
            $Qobj_pau = $QobjPauInput;
        } elseif (is_array($QobjPauInput)) {
            /** @var list<string> $Qobj_pau */
            $Qobj_pau = array_values(array_filter($QobjPauInput, static fn (mixed $v): bool => is_string($v)));
        } else {
            $Qobj_pau = '';
        }
        $titulo = is_string($in['titulo'] ?? null) ? $in['titulo'] : '';

        $tipo_ubi = $Qtipo . $Qloc;
        // si es sf, el tipo_ubi = ctrsf
        if ($tipo_ubi === 'ctrdl' && $miSfsv === 2) {
            $tipo_ubi = 'ctrsf';
        }

        $aWhere = [];
        $aOperador = [];
        $aWhereD = [];
        $aOperadorD = [];

        $Qnombre_ubi = input_string($p, 'nombre_ubi');
        if ($Qnombre_ubi !== '') {
            $nom_ubi = str_replace('+', "\+", $Qnombre_ubi); // para los centros de la sss+
            $aWhere['nombre_ubi'] = $nom_ubi;
            $aOperador['nombre_ubi'] = 'sin_acentos';
            $aWhere['_ordre'] = 'tipo_ubi,nombre_ubi';
        }
        $Qregion = input_string($p, 'region');
        if ($Qregion !== '') {
            $aWhere['region'] = $Qregion;
            $aWhere['_ordre'] = 'nombre_ubi';
        }
        $Qdl = input_string($p, 'dl');
        if ($Qdl !== '') {
            $aWhere['dl'] = $Qdl;
            $aOperador['dl'] = 'sin_acentos';
            $aWhere['_ordre'] = 'dl';
        }
        $Qtipo_ctr = input_string($p, 'tipo_ctr');
        if ($Qtipo_ctr !== '') {
            $aWhere['tipo_ctr'] = $Qtipo_ctr;
            $aOperador['tipo_ctr'] = 'sin_acentos';
            $aWhere['_ordre'] = 'tipo_ctr';
        }
        $Qtipo_casa = input_string($p, 'tipo_casa');
        if ($Qtipo_casa !== '') {
            $aWhere['tipo_casa'] = $Qtipo_casa;
            $aOperador['tipo_casa'] = 'sin_acentos';
            $aWhere['_ordre'] = 'tipo_casa';
        }

        $Qciudad = input_string($p, 'ciudad');
        if ($Qciudad !== '') {
            $aWhereD['poblacion'] = $Qciudad;
            $aOperadorD['poblacion'] = 'sin_acentos';
            $aWhereD['_ordre'] = 'poblacion';
        }
        $Qpais = input_string($p, 'pais');
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
            if (!is_array($Qobj_pau)) {
                $QobjPauList = [$Qobj_pau];
            } else {
                $QobjPauList = $Qobj_pau;
            }
            foreach ($QobjPauList as $Qobj_pau_i) {
                $repoDir[] = $this->ubiRepositoryResolver->getDireccionRepositoryClass($Qobj_pau_i);
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
     * @param string|list<string> $Qobj_pau
     * @param array<string, mixed> $aWhere
     * @return array{0: string|list<string>, 1: string, 2: array<string, mixed>}
     */
    private function resolverObjPauYTitulo(
        string $Qtipo,
        string $Qloc,
        string|array $Qobj_pau,
        string $titulo,
        array $aWhere,
        int $miSfsv
    ): array {
        $oPerm = $_SESSION['oPerm'] ?? null;
        $permisoSf = $oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des'));

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
     *
     * @param array<string, mixed> $in
     * @return array<string, mixed>
     */
    private function buildFiltrosDesdeHash(array $in): array
    {
        $decodedWhere = json_decode(urlsafe_b64decode(is_string($in['sWhere'] ?? null) ? $in['sWhere'] : ''), true);
        $aWhere = is_array($decodedWhere) ? $decodedWhere : [];
        $decodedOperador = json_decode(urlsafe_b64decode(is_string($in['sOperador'] ?? null) ? $in['sOperador'] : ''), true);
        $aOperador = is_array($decodedOperador) ? $decodedOperador : [];
        $decodedWhereD = json_decode(urlsafe_b64decode(is_string($in['sWhereD'] ?? null) ? $in['sWhereD'] : ''), true);
        $aWhereD = is_array($decodedWhereD) ? $decodedWhereD : [];
        $decodedOperadorD = json_decode(urlsafe_b64decode(is_string($in['sOperadorD'] ?? null) ? $in['sOperadorD'] : ''), true);
        $aOperadorD = is_array($decodedOperadorD) ? $decodedOperadorD : [];

        $Qobj_pau = $in['Qobj_pau'] ?? '';
        $metodo = is_string($in['metodo'] ?? null) ? $in['metodo'] : '';
        $repoDir = '';
        if (!empty($Qobj_pau) && is_string($Qobj_pau)) {
            $metodo = $this->ubiRepositoryResolver->getMetodo($Qobj_pau);
            $repoDir = $this->ubiRepositoryResolver->getDireccionRepositoryClass($Qobj_pau);
        }

        $qtipo = is_string($in['Qtipo'] ?? null) ? $in['Qtipo'] : '';
        $qloc = is_string($in['Qloc'] ?? null) ? $in['Qloc'] : '';

        return [
            'aWhere' => $aWhere,
            'aOperador' => $aOperador,
            'aWhereD' => $aWhereD,
            'aOperadorD' => $aOperadorD,
            'Qobj_pau' => $Qobj_pau,
            'titulo' => is_string($in['titulo'] ?? null) ? $in['titulo'] : '',
            'metodo' => $metodo,
            'repoDir' => $repoDir,
            'Qnombre_ubi' => '',
            'Qdl' => '',
            'Qregion' => '',
            'tipo_ubi' => $qtipo . $qloc,
        ];
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperador
     * @return list<Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos>
     */
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
            if (!is_string($Qobj_pau_i)) {
                continue;
            }
            $metodo = $this->ubiRepositoryResolver->getMetodo($Qobj_pau_i);
            $UbiRepository = $this->ubiRepositoryResolver->getRepository($Qobj_pau_i);
            $cUbis_i = $UbiRepository->$metodo($aWhere, $aOperador);
            foreach ($cUbis_i as $ubi) {
                $cUbis[] = $ubi;
            }
        }

        return $cUbis;
    }

    /**
     * @param array<string, mixed> $aWhereD
     * @param array<string, string> $aOperadorD
     * @return list<Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos>
     */
    private function buscarUbisPorDireccion(array $aWhereD, array $aOperadorD, mixed $repoDir, string $Qcmb): array
    {
        $cUbisD = [];
        if (empty($aWhereD) || empty($repoDir)) {
            return $cUbisD;
        }

        foreach ((array)$repoDir as $repoDir_i) {
            if (!is_string($repoDir_i)) {
                continue;
            }
            if (!class_exists($repoDir_i)) {
                continue;
            }
            $DireccionesRepository = $this->ubiRepositoryResolver->getDireccionRepositoryByInterface($repoDir_i);
            $cDirecciones = $DireccionesRepository->getDirecciones($aWhereD, $aOperadorD) ?: [];
            $RelacionRepository = $this->ubiRepositoryResolver->getRelacionRepositoryForDireccion($repoDir_i);
            foreach ($cDirecciones as $oDireccion) {
                $id_direccion = $oDireccion->getId_direccion();
                $cIdUbis = $RelacionRepository->getUbisPorDireccion($id_direccion);
                foreach ($cIdUbis as $aUbi) {
                    if (!isset($aUbi['id_ubi'])) {
                        continue;
                    }
                    $idUbiRaw = $aUbi['id_ubi'];
                    if (!is_int($idUbiRaw) && !is_string($idUbiRaw) && !is_float($idUbiRaw) && !is_bool($idUbiRaw)) {
                        continue;
                    }
                    $oUbi = $this->ubiFactory->newUbi(is_numeric($idUbiRaw) ? (int) $idUbiRaw : (string) $idUbiRaw);
                    if ($oUbi === null) {
                        continue;
                    }
                    if (!is_true($Qcmb) && !$oUbi->isActive()) {
                        continue;
                    }
                    $cUbisD[] = $oUbi;
                }
            }
        }

        return $cUbisD;
    }

    /**
     * Combina las colecciones por nombre y por dirección haciendo intersección cuando corresponde,
     * descarta duplicados y ordena por región/nombre.
     *
     * @param list<Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos> $cUbis
     * @param list<Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos> $cUbisD
     * @return array{0: list<Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos>} [cUbisTot]
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
            $region = $oUbi->getRegion();
            $nombre = $oUbi->getNombre_ubi();
            $a_region[$key] = strtolower($region ?? '');
            $a_nom[$key] = strtolower($nombre);
        }

        array_multisort($a_region, SORT_LOCALE_STRING, SORT_ASC, $a_nom, SORT_LOCALE_STRING, SORT_ASC, $cUbisTot);

        return [array_values($cUbisTot)];
    }

    /**
     * Calcula (nueva_ficha, pagina_link) según haya o no resultados y el tipo de búsqueda.
     *
     * @param list<Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos> $cUbisTot
     * @return array{0: string, 1: array<string, mixed>|null}
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
        $pagina_link_spec = null;
        $sinResultados = count($cUbisTot) === 0;

        if ($Qtipo === 'tot' || $Qloc === 'tot') {
            if ($sinResultados) {
                $nueva_ficha = 'especificar';
                $pagina_link_spec = [
                    'path' => 'frontend/ubis/controller/ubis_buscar.php',
                    'query' => ['simple' => '2'],
                ];
            }
            return [$nueva_ficha, $pagina_link_spec];
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
        $pagina_link_spec = [
            'path' => 'frontend/ubis/controller/ubis_editar.php',
            'query' => $a_link,
        ];
        if ($sinResultados) {
            $nueva_ficha = 'aviso';
        }
        return [$nueva_ficha, $pagina_link_spec];
    }

    /**
     * Formatea la colección de ubis en la estructura a_valores de la tabla.
     *
     * @param list<Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos> $cUbisTot
     * @return array<int|string, mixed>
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
            if (!empty($cDirecciones)) {
                foreach ($cDirecciones as $oDireccion) {
                    // Comportamiento preexistente: sólo toma la última dirección.
                    $poblacion = $oDireccion->getPoblacionVo()->value();
                    $pais = $oDireccion->getPaisVo()?->value() ?? '';
                    $direccion = $oDireccion->getDireccionVo()?->value() ?? '';
                    $c_p = $oDireccion->getCodigoPostalVo()?->value() ?? '';
                }
            }

            $a_valores[$i] = [
                'sel' => $id_ubi,
                1 => [
                    'link_spec' => [
                        'path' => 'frontend/ubis/controller/home_ubis.php',
                        'query' => ['pau' => 'u', 'id_ubi' => $id_ubi],
                    ],
                    'valor' => $nombre_ubi,
                ],
                2 => $oUbi->getTipo_ubi(),
                3 => $oUbi->getDl(),
                4 => $oUbi->getRegion(),
                5 => $direccion,
                6 => $c_p,
                7 => $poblacion,
            ];
            // Nota: $pais se calcula pero no se muestra en la tabla actual.
            unset($pais);
        }

        return $a_valores;
    }
}
