<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\web\NavStack;
use frontend\shared\web\Posicion;

/**
 * Navegación con {@see Posicion} tras `FrontBootstrap::boot()`.
 *
 * Guía: `docs/dev/posicion_nav_post_frontbootstrap.md`
 */
final class ListNavSupport
{
public static function scrollIdFromPost(): string
{
    foreach ($_POST as $k => $v) {
        if (!is_string($k) || !str_starts_with($k, 'scroll_id_')) {
            continue;
        }
        if (!is_scalar($v)) {
            continue;
        }
        $s = (string) $v;
        if ($s !== '' && $s !== '0') {
            return $s;
        }
    }

    $raw = filter_input(INPUT_POST, 'scroll_id');

    return is_scalar($raw) ? (string) $raw : '';
}

public static function selFromPost(): array
{
    if (array_key_exists('sel', $_POST)) {
        $fromPost = self::selArrayFromRaw($_POST['sel']);
        if ($fromPost !== []) {
            return $fromPost;
        }
    }

    $raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($raw)) {
        $fromFilter = self::selArrayFromRaw($raw);
        if ($fromFilter !== []) {
            return $fromFilter;
        }
    }

    $fromIdSel = self::idSelForLista($_POST['id_sel'] ?? null);
    if (self::idSelIsEmpty($fromIdSel)) {
        return [];
    }

    return is_array($fromIdSel) ? $fromIdSel : [$fromIdSel];
}

/**
 * @return list<string>
 */
private static function selArrayFromRaw(mixed $raw): array
{
    if (!is_array($raw)) {
        if (is_scalar($raw) && (string) $raw !== '') {
            return [(string) $raw];
        }

        return [];
    }

    $out = [];
    foreach ($raw as $item) {
        if ($item === false) {
            continue;
        }
        $s = (string) $item;
        if ($s !== '') {
            $out[] = $s;
        }
    }

    return $out;
}

public static function idSelForLista(mixed $raw): string|array
{
    if (is_array($raw)) {
        $out = [];
        foreach ($raw as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $s = (string) $item;
            if ($s !== '') {
                $out[] = $s;
            }
        }
        if ($out === []) {
            return '';
        }

        return count($out) === 1 ? $out[0] : $out;
    }
    if (is_string($raw) && $raw !== '') {
        return $raw;
    }
    if (is_int($raw) || is_float($raw)) {
        return (string) $raw;
    }

    return '';
}

public static function idSelIsEmpty(string|array $sel): bool
{
    return $sel === '' || $sel === [];
}

public static function sessionPosition(): ?array
{
    $stack = self::navStackSessionEntries();
    if ($stack === []) {
        return null;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = [];
    foreach ($stack as $idx => $entry) {
        $position[$idx] = [
            'url' => $entry['url'] ?? '',
            'bloque' => $entry['bloque'] ?? '#main',
            'parametros' => is_array($entry['state'] ?? null) ? $entry['state'] : [],
        ];
    }

    return $position;
}

public static function sessionStackEntry(int|string $index): ?array
{
    $position = self::sessionPosition();
    if ($position === null || !array_key_exists($index, $position)) {
        return null;
    }

    return $position[$index];
}

public static function normalizeIdSel(mixed $raw): string|array
{
    $normalized = self::idSelForLista($raw);

    return self::idSelIsEmpty($normalized) ? '' : $normalized;
}

public static function idSelFromPost(): string|array
{
    // sel[] (checkbox/grid) manda sobre id_sel oculto, que puede quedar desactualizado tras cambiar fila.
    $aSel = self::selFromPost();
    if ($aSel === []) {
        return '';
    }

    return self::idSelForLista($aSel);
}

public static function persistSelectionToPosicion(Posicion $oPosicion, int $n = 1): void
{
    self::syncNavStateAt($oPosicion, $n, self::buildSelectionStatePatchFromPost());
}

/**
 * Parche id_sel / scroll_id desde POST (NavStack v2 updateStateAt).
 *
 * @return array<string, mixed>
 */
public static function buildSelectionStatePatchFromPost(): array
{
    $persist = [];
    $aSel = self::selFromPost();
    if ($aSel !== []) {
        $persist['id_sel'] = count($aSel) === 1 ? $aSel[0] : $aSel;
    }
    $scrollId = self::scrollIdFromPost();
    if ($scrollId !== '' && $scrollId !== '0') {
        $persist['scroll_id'] = $scrollId;
    }

    return $persist;
}

/**
 * Sincroniza el state de una entrada de la pila v2 (p. ej. padre al entrar en impresión).
 *
 * @param array<string, mixed> $state
 */
public static function syncNavStateAt(Posicion $oPosicion, int $n, array $state): void
{
    if ($state !== []) {
        $oPosicion->nav()->updateStateAt($n, $state);
    }
}

public static function persistSelectionOnListPage(Posicion $oPosicion, string|array $idSel, string $scrollId = '', bool $returningViaStack = false): void
{
    if (self::idSelIsEmpty($idSel) && $scrollId === '') {
        return;
    }

    $persist = [];
    if (!self::idSelIsEmpty($idSel)) {
        $persist['id_sel'] = $idSel;
    }
    if ($scrollId !== '') {
        $persist['scroll_id'] = $scrollId;
    }

    // NavStack v2: actualizar state de la entrada n bajo la cima.
    self::syncNavStateAt($oPosicion, $returningViaStack ? 1 : 0, $persist);
}

public static function buildDossierReturnParametros(): array
{
    $parametros = [];
    foreach (['pau', 'obj_pau', 'id_dossier', 'queSel', 'que', 'permiso', 'bloque', 'clase_info'] as $key) {
        if (!isset($_POST[$key]) || !is_scalar($_POST[$key])) {
            continue;
        }
        $s = (string) $_POST[$key];
        if ($s !== '') {
            $parametros[$key] = $s;
        }
    }

    $idPau = 0;
    if (isset($_POST['id_pau']) && is_scalar($_POST['id_pau'])) {
        $idPau = (int) $_POST['id_pau'];
    }
    if ($idPau <= 0) {
        $idPauRaw = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
        $idPau = is_int($idPauRaw) ? $idPauRaw : 0;
    }
    if ($idPau <= 0) {
        $aSel = self::selFromPost();
        if ($aSel !== []) {
            $idPau = (int) strtok((string) $aSel[0], '#');
        }
    }
    if ($idPau > 0) {
        $parametros['id_pau'] = $idPau;
    }
    if (!isset($parametros['pau']) && $idPau > 0) {
        $parametros['pau'] = 'a';
    }

    $idSel = self::idSelFromPost();
    if (!self::idSelIsEmpty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    $scrollId = self::scrollIdFromPost();
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
}

public static function buildDossiersVerStackParametros(): array
{
    $parametros = self::buildDossierReturnParametros();
    foreach (['mod', 'id_activ', 'depende', 'refresh'] as $key) {
        if (array_key_exists($key, $_POST) && is_scalar($_POST[$key]) && (string) $_POST[$key] !== '') {
            $s = (string) $_POST[$key];
        } else {
            $raw = filter_input(INPUT_POST, $key);
            if (!is_scalar($raw) || (string) $raw === '') {
                continue;
            }
            $s = (string) $raw;
        }
        if ($key === 'mod' && self::isDossiersEphemeralMod($s)) {
            continue;
        }
        $parametros[$key] = $s;
    }

    return $parametros;
}

/**
 * enter() o refresh in-place de dossiers_ver (no crece la pila en POST refresh=1).
 *
 * Equivalente v2 de {@see pararRecordarForDossiersRefresh()}: con refresh=1 solo se
 * actualiza el state del tope dossiers; nunca se llama a enter().
 */
public static function enterOrRefreshDossiersVer(Posicion $oPosicion): void
{
    $navState = self::buildDossiersVerStackParametros();
    $identity = self::buildDossiersVerNavIdentity($navState);

    $isRefresh = isset($_POST['refresh']) && is_scalar($_POST['refresh']) && (int) $_POST['refresh'] > 0;
    if (!$isRefresh) {
        $refreshRaw = filter_input(INPUT_POST, 'refresh', FILTER_VALIDATE_INT);
        $isRefresh = is_int($refreshRaw) && $refreshRaw > 0;
    }

    if ($isRefresh) {
        if (self::tryRefreshDossiersVerAt($oPosicion, $identity, $navState, 0)
            || self::tryRefreshDossiersVerBySegment($oPosicion, $navState, 0)
            || self::tryRefreshDossiersVerOnTop($oPosicion, $navState)
        ) {
            self::pruneDuplicateDossiersVerSegments($oPosicion, $navState);
        }

        return;
    }

    $oPosicion->nav()->enter(
        self::dossiersVerDefaultUrl(),
        '#main',
        $identity,
        $navState,
    );
}

/**
 * @param array<string, mixed> $identity
 * @param array<string, mixed> $navState
 */
private static function tryRefreshDossiersVerAt(
    Posicion $oPosicion,
    array $identity,
    array $navState,
    int $n,
): bool {
    $nav = $oPosicion->nav();
    $entry = $nav->peek($n);
    if ($entry === null) {
        return false;
    }
    $entryUrl = is_string($entry['url'] ?? null) ? $entry['url'] : '';
    if (!str_contains($entryUrl, 'dossiers_ver.php')) {
        return false;
    }
    $entryIdentity = is_array($entry['identity'] ?? null) ? $entry['identity'] : [];
    if (NavStack::pageKey($entryUrl, $entryIdentity) !== NavStack::pageKey($entryUrl, $identity)) {
        return false;
    }
    $nav->updateStateAt($n, $navState);

    return true;
}

/**
 * @param array<string, mixed> $navState
 */
private static function tryRefreshDossiersVerBySegment(
    Posicion $oPosicion,
    array $navState,
    int $n,
): bool {
    $nav = $oPosicion->nav();
    $entry = $nav->peek($n);
    if ($entry === null) {
        return false;
    }
    $entryUrl = is_string($entry['url'] ?? null) ? $entry['url'] : '';
    if (!str_contains($entryUrl, 'dossiers_ver.php')) {
        return false;
    }
    $segmentKey = self::dossiersSegmentKeyFromParametros($navState);
    if ($segmentKey === '' || self::dossiersSegmentKeyFromEntry($entry) !== $segmentKey) {
        return false;
    }
    $nav->updateStateAt($n, $navState);

    return true;
}

/**
 * Último recurso en refresh: si el tope ya es dossiers_ver, actualizar sin enter().
 *
 * @param array<string, mixed> $navState
 */
private static function tryRefreshDossiersVerOnTop(Posicion $oPosicion, array $navState): bool
{
    if (!self::stackTopIsDossiersVer()) {
        return false;
    }
    $oPosicion->nav()->updateStateAt(0, $navState);

    return true;
}

/**
 * @param array<string, mixed> $navState
 */
public static function pruneDuplicateDossiersVerSegments(Posicion $oPosicion, array $navState): void
{
    $segmentKey = self::dossiersSegmentKeyFromParametros($navState);
    if ($segmentKey === '') {
        return;
    }

    $oPosicion->nav()->collapseDuplicateDossiersSegments(
        static fn (array $entry): string => self::dossiersSegmentKeyFromEntry($entry),
        $segmentKey,
    );
}

/**
 * @param array<string, mixed> $entry
 */
public static function dossiersSegmentKeyFromEntry(array $entry): string
{
    /** @var array<string, mixed> $identity */
    $identity = is_array($entry['identity'] ?? null) ? $entry['identity'] : [];
    /** @var array<string, mixed> $state */
    $state = is_array($entry['state'] ?? null) ? $entry['state'] : [];

    return self::dossiersSegmentKeyFromParametros(array_merge($identity, $state));
}

public static function stackParentIsDossiersVer(int $n = 1): bool
{
    $url = self::stackEntryUrl($n);

    return $url !== '' && str_contains($url, 'dossiers_ver.php');
}

public static function stackTopParametros(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    /** @var list<array<string, mixed>> $stack */
    $stack = is_array($_SESSION['nav']['stack'] ?? null) ? $_SESSION['nav']['stack'] : [];
    session_write_close();
    if ($stack === []) {
        return [];
    }
    $top = $stack[count($stack) - 1];
    $state = $top['state'] ?? [];

    return is_array($state) ? $state : [];
}

public static function dossiersSegmentKeyFromParametros(array $parametros): string
{
    $parts = [];
    foreach (['queSel', 'id_dossier', 'pau', 'obj_pau', 'id_pau', 'clase_info'] as $key) {
        if (!array_key_exists($key, $parametros)) {
            continue;
        }
        $val = $parametros[$key];
        if (is_scalar($val) && (string) $val !== '') {
            $parts[] = $key . '=' . (string) $val;
        }
    }
    $mod = isset($parametros['mod']) && is_scalar($parametros['mod']) ? (string) $parametros['mod'] : '';
    if ($mod !== '' && !self::isDossiersEphemeralMod($mod)) {
        $parts[] = 'mod=' . $mod;
    }

    return implode('|', $parts);
}

public static function dossiersSegmentChangedVsStackTop(): bool
{
    if (!self::stackTopIsDossiersVer()) {
        return false;
    }
    $stored = self::stackTopParametros();
    if ($stored === []) {
        return false;
    }
    $current = self::buildReturnParametrosFromPost();

    return self::dossiersSegmentKeyFromParametros($current)
        !== self::dossiersSegmentKeyFromParametros($stored);
}

public static function dossiersParametrosIsAsistentesSegment(array $parametros): bool
{
    $queSel = isset($parametros['queSel']) && is_scalar($parametros['queSel']) ? (string) $parametros['queSel'] : '';
    $idDossier = isset($parametros['id_dossier']) ? (int) $parametros['id_dossier'] : 0;

    return $queSel === 'asis' || $idDossier === 3101;
}

public static function dossiersCurrentIsAsistentesSegment(): bool
{
    if (self::dossiersParametrosIsAsistentesSegment(self::buildReturnParametrosFromPost())) {
        return true;
    }

    return self::dossiersParametrosIsAsistentesSegment(self::stackTopParametros());
}

public static function mostrarLeftSlideFromDossiers(Posicion $oPosicion): string
{
    return $oPosicion->mostrarNavAtrasFromDossiers();
}

public static function persistAsistentesDossierSnapshot(Posicion $oPosicion): void
{
    unset($oPosicion);
    $parametros = self::stackTopParametros();
    if ($parametros === []) {
        return;
    }
    $queSel = isset($parametros['queSel']) && is_scalar($parametros['queSel']) ? (string) $parametros['queSel'] : '';
    $idDossier = isset($parametros['id_dossier']) ? (int) $parametros['id_dossier'] : 0;
    if ($queSel !== 'asis' && $idDossier !== 3101) {
        return;
    }
    foreach (array_merge(self::metaHashPostKeys(), self::stackEphemeralPostKeys()) as $key) {
        unset($parametros[$key]);
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['list_nav_asistentes_dossier_snapshot'] = $parametros;
    session_write_close();
}

public static function stackHasAsistentesDossierForActiv(int $idActiv): bool
{
    if ($idActiv <= 0) {
        return false;
    }
    foreach (self::navStackSessionEntries() as $entry) {
        $url = is_string($entry['url'] ?? null) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            continue;
        }
        $parametros = is_array($entry['state'] ?? null) ? $entry['state'] : [];
        $queSel = isset($parametros['queSel']) && is_scalar($parametros['queSel']) ? (string) $parametros['queSel'] : '';
        $idDossier = isset($parametros['id_dossier']) ? (int) $parametros['id_dossier'] : 0;
        $idPau = isset($parametros['id_pau']) ? (int) $parametros['id_pau'] : 0;
        if (($queSel === 'asis' || $idDossier === 3101) && $idPau === $idActiv) {
            return true;
        }
    }

    return false;
}

public static function ensureAsistentesDossierBeforeActividadSelectChild(Posicion $oPosicion, int $idActiv): void
{
    unset($oPosicion, $idActiv);
}

public static function stackHasDossiersVer(): bool
{
    foreach (self::navStackSessionEntries() as $entry) {
        $url = is_string($entry['url'] ?? null) ? $entry['url'] : '';
        if (str_contains($url, 'dossiers_ver.php')) {
            return true;
        }
    }

    return false;
}

public static function stackTopIsDossiersVer(): bool
{
    return self::stackParentIsDossiersVer(0);
}

public static function findBestDossiersStackEntry(): ?array
{
    $best = null;
    foreach (self::navStackSessionEntries() as $entry) {
        $url = is_string($entry['url'] ?? null) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            continue;
        }
        $best = [
            'url' => $url,
            'bloque' => $entry['bloque'] ?? '#main',
            'parametros' => is_array($entry['state'] ?? null) ? $entry['state'] : [],
        ];
    }

    return $best;
}

public static function findBestDossiersStackKey(): int
{
    $stack = self::navStackSessionEntries();
    for ($idx = count($stack) - 1; $idx >= 0; $idx--) {
        $url = is_string($stack[$idx]['url'] ?? null) ? $stack[$idx]['url'] : '';
        if (str_contains($url, 'dossiers_ver.php')) {
            return $idx;
        }
    }

    return -1;
}

public static function dossierChildFormUrlFragments(): array
{
    return [
        'form_cargos_de_actividad',
        'form_cargos_personas_en_actividad',
        'form_asistentes_a_una_actividad',
        'form_actividades_de_una_persona',
        'form_asignaturas_de_una_actividad',
        'form_matriculas_de_una_persona',
        'form_matriculas_de_una_actividad',
        'form_notas_de_una_persona',
        'acta_notas.php',
    ];
}

public static function isDossierChildFormUrl(string $url): bool
{
    foreach (self::dossierChildFormUrlFragments() as $fragment) {
        if (str_contains($url, $fragment)) {
            return true;
        }
    }

    return false;
}

public static function isDossierShellOrChildUrl(string $url): bool
{
    return str_contains($url, 'dossiers_ver.php') || self::isDossierChildFormUrl($url);
}

public static function actividadSelectChildUrlFragments(): array
{
    return [
        'plan_estudios_ca.php',
        'lista_clases_ca.php',
        'posibles_asignaturas_ca.php',
        'lista_asistentes.php',
        'tabla_peticiones.php',
        'lista_habitaciones.php',
        'resumen_plazas.php',
        'actividad_proceso.php',
        'actividad_ver.php',
    ];
}

public static function isActividadSelectChildUrl(string $url): bool
{
    foreach (self::actividadSelectChildUrlFragments() as $fragment) {
        if (str_contains($url, $fragment)) {
            return true;
        }
    }

    return false;
}

public static function isSkippableBetweenActividadSelectAndChild(string $url): bool
{
    return self::isDossierShellOrChildUrl($url)
        || self::isActividadSelectChildUrl($url);
}

public static function reindexPositionStack(): void
{
}

public static function purgeDossierChildFormsFromStack(): void
{
}

public static function purgeDossierNavigationFromStack(): void
{
}

public static function findStackKeyByUrlContains(string $needle): int
{
    if ($needle === '') {
        return -1;
    }
    $stack = self::navStackSessionEntries();
    for ($idx = count($stack) - 1; $idx >= 0; $idx--) {
        $url = is_string($stack[$idx]['url'] ?? null) ? $stack[$idx]['url'] : '';
        if ($url !== '' && str_contains($url, $needle)) {
            return $idx;
        }
    }

    return -1;
}

public static function refreshStackEntryAtIndex(Posicion $oPosicion, int $stackIndex, string $bloque = '#main'): bool
{
    unset($bloque);
    if ($stackIndex < 0 || $oPosicion->nav()->peek($stackIndex) === null) {
        return false;
    }
    $parametros = self::buildReturnParametrosFromPost();
    if ($parametros === []) {
        return false;
    }
    $oPosicion->nav()->updateStateAt($stackIndex, $parametros);

    return true;
}

public static function stackTopIsDossierChildForm(): bool
{
    $url = self::stackEntryUrl(0);

    return $url !== '' && self::isDossierChildFormUrl($url);
}

public static function olvidarForwardFromDossiersSlot(int $preferredIndex): void
{
    unset($preferredIndex);
}

public static function backStepsToListParentFromDossiers(int $max = 15): int
{
    $stack = self::navStackSessionEntries();
    $len = count($stack);
    for ($steps = 1; $steps < $len && $steps <= $max; $steps++) {
        $idx = $len - 1 - $steps;
        $url = is_string($stack[$idx]['url'] ?? null) ? $stack[$idx]['url'] : '';
        if ($url !== '' && !self::isSkippableBetweenActividadSelectAndChild($url)) {
            return max(1, $steps);
        }
    }

    return 1;
}

public static function mostrarLeftSlideToListParentFromDossiers(Posicion $oPosicion): string
{
    $n = self::navBackStepsFromDossiersVer($oPosicion->nav());

    return $oPosicion->mostrarNavAtras($n);
}

public static function dossiersVerDefaultUrl(): string
{
    $phpSelf = $_SERVER['PHP_SELF'] ?? '';

    return is_string($phpSelf) && str_contains($phpSelf, 'dossiers_ver.php')
        ? $phpSelf
        : 'frontend/dossiers/controller/dossiers_ver.php';
}

public static function backStepsToDossiersParent(int $max = 10): int
{
    $stack = self::navStackSessionEntries();
    $len = count($stack);
    for ($steps = 1; $steps < $len && $steps <= $max; $steps++) {
        $idx = $len - 1 - $steps;
        $url = is_string($stack[$idx]['url'] ?? null) ? $stack[$idx]['url'] : '';
        if (str_contains($url, 'dossiers_ver.php')) {
            return $steps;
        }
    }

    return 1;
}

public static function mostrarLeftSlideToDossiersParent(Posicion $oPosicion): string
{
    return $oPosicion->mostrarNavAtrasToDossiersParent();
}

public static function jsAtrasToDossiersParent(Posicion $oPosicion): string
{
    return $oPosicion->jsNavAtrasToDossiersParent();
}

public static function goAtrasToDossiersParent(Posicion $oPosicion): string
{
    return $oPosicion->jsNavAtrasToDossiersParent();
}

public static function persistDossierParentSelectionIfDossier(Posicion $oPosicion): void
{
    $steps = self::backStepsToDossiersParent();
    if ($steps < 1 || !self::stackParentIsDossiersVer($steps)) {
        return;
    }
    self::persistSelectionToPosicion($oPosicion, $steps);
}

public static function ensureDossiersOnStackBeforeChild(): void
{
}

public static function pararRecordarForDossiersRefresh(int $Qrefresh): int
{
    if ($Qrefresh <= 0) {
        return 0;
    }

    return self::stackTopIsDossiersVer() ? 1 : 0;
}

public static function bootDossierChildRecordar(Posicion $oPosicion, int $parar = 0): void
{
    unset($oPosicion, $parar);
}

public static function persistDossierReturnToPosicion(Posicion $oPosicion, int $n = 1): void
{
    if (!self::stackParentIsDossiersVer($n)) {
        return;
    }
    // recordar() en dossiers_ver ya guardó el POST completo; no reemplazar por un subconjunto.
}

public static function mergeDossierReturn(array $extra): array
{
    return array_merge(self::buildDossierReturnParametros(), $extra);
}

public static function metaHashPostKeys(): array
{
    return ['h', 'hh', 'hhc', 'horig', 'hhorig', 'hc', 'hchk', 'hno', 'hnov'];
}

public static function stackEphemeralPostKeys(): array
{
    return ['stack', 'Gstack'];
}

public static function buildReturnParametrosFromPost(?array $override = null): array
{
    $parametros = $_POST;
    foreach (array_merge(self::metaHashPostKeys(), self::stackEphemeralPostKeys()) as $key) {
        unset($parametros[$key]);
    }
    if ($override !== null) {
        $parametros = array_merge($parametros, $override);
    }

    return $parametros;
}

public static function mergeSelectionIntoReturnParametros(array $parametros, string|array|null $idSel = '', string $scrollId = ''): array
{
    $idSel = $idSel ?? '';
    if (!self::idSelIsEmpty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
}

public static function mergeSelectionForRecordar(array $parametros, mixed $idSel = null, mixed $scrollId = ''): array
{
    $normalizedIdSel = $idSel === null ? '' : self::normalizeIdSel($idSel);

    return self::mergeSelectionIntoReturnParametros(
        $parametros,
        self::idSelIsEmpty($normalizedIdSel) ? null : $normalizedIdSel,
        is_scalar($scrollId) ? (string) $scrollId : '',
    );
}

public static function persistRecordarEntry(Posicion $oPosicion, array $parametros): void
{
    self::persistCleanReturnToPosicion($oPosicion, $parametros, 0);
}

public static function clearInheritedStackForRecordar(Posicion $oPosicion): void
{
    unset($oPosicion);
}

public static function bootRecordar(Posicion $oPosicion, int $parar = 0): void
{
    unset($oPosicion, $parar);
}

public static function bootListPageAfterStackReturn(Posicion $oPosicion, int $stackFromPost): void
{
    unset($oPosicion, $stackFromPost);
}

public static function bootActividadSelectChildRecordar(Posicion $oPosicion, int $parar = 0): void
{
    unset($oPosicion, $parar);
}

public static function persistActividadSelectChildEntry(Posicion $oPosicion, array $extra = []): void
{
    self::enterActividadSelectChildNav($oPosicion, '#main', $extra);
}

/**
 * enter() NavStack v2 desde un hijo de actividad_select (lista_asistentes, resumen_plazas, …).
 *
 * @param array<string, mixed> $extra
 */
public static function enterActividadSelectChildNav(Posicion $oPosicion, string $bloque = '#main', array $extra = []): void
{
    $idActiv = 0;
    if (isset($extra['id_activ']) && is_numeric($extra['id_activ'])) {
        $idActiv = (int) $extra['id_activ'];
    }
    if ($idActiv <= 0) {
        $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
        $idActiv = is_int($idActivRaw) ? $idActivRaw : 0;
    }
    if ($idActiv <= 0) {
        $aSel = self::selFromPost();
        if ($aSel !== []) {
            $idActiv = (int) strtok((string) $aSel[0], '#');
        }
    }

    $navState = $extra;
    $aSel = self::selFromPost();
    if ($aSel !== []) {
        $navState['sel'] = $aSel;
    }
    foreach (['queSel', 'mod', 'obj_pau', 'pau', 'permiso'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $navState[$key] = (string) $raw;
        }
    }
    $navState = self::mergeSelectionIntoReturnParametros(
        $navState,
        self::idSelFromPost(),
        self::scrollIdFromPost(),
    );
    if ($idActiv > 0) {
        $navState['id_activ'] = $idActiv;
    }

    $oPosicion->nav()->enter(
        (string) ($_SERVER['PHP_SELF'] ?? ''),
        $bloque,
        $idActiv > 0 ? ['id_activ' => $idActiv] : [],
        $navState,
    );
    self::syncActividadSelectParentSelection($oPosicion);
}

public static function refreshStackEntryOnReturn(Posicion $oPosicion, int $stackIndex): void
{
    if (self::refreshStackEntryAtIndex($oPosicion, $stackIndex)) {
        return;
    }
    $fallback = self::findBestDossiersStackKey();
    if ($fallback >= 0) {
        self::refreshStackEntryAtIndex($oPosicion, $fallback);
    }
}

public static function bootChildFromListRecordar(Posicion $oPosicion, int $parar = 0): void
{
    unset($oPosicion, $parar);
}

public static function persistParentIfUrl(Posicion $oPosicion, array $parametros, string $urlMustContain): void
{
    if ($parametros === []) {
        return;
    }
    $parentUrl = self::stackEntryUrl(1);
    if ($parentUrl === '' || !str_contains($parentUrl, $urlMustContain)) {
        return;
    }
    self::syncNavStateAt($oPosicion, 1, $parametros);
}

public static function buildActividadQueReturnParametros(array $state): array
{
    $parametros = [];
    foreach ([
        'modo', 'que', 'status', 'id_tipo_activ', 'filtro_lugar', 'id_ubi', 'nom_activ',
        'periodo', 'year', 'dl_org', 'empiezamin', 'empiezamax',
        'fases_on', 'fases_off', 'publicado', 'listar_asistentes',
        'sasistentes', 'sactividad', 'sactividad2', 'extendida', 'ssfsv',
    ] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    $idSel = self::normalizeIdSel($state['id_sel'] ?? null);
    $scrollIdRaw = $state['scroll_id'] ?? null;
    $scrollId = is_scalar($scrollIdRaw) ? (string) $scrollIdRaw : '';

    return self::mergeSelectionIntoReturnParametros($parametros, $idSel === '' ? null : $idSel, $scrollId);
}

public static function buildActividadSelectReturnParametros(array $state): array
{
    $parametros = self::buildActividadQueReturnParametros($state);
    foreach (['ssfsv', 'sasistentes', 'sactividad', 'sactividad2'] as $extra) {
        if (!array_key_exists($extra, $state)) {
            continue;
        }
        $val = $state[$extra];
        if ($val === '' || $val === null) {
            continue;
        }
        $parametros[$extra] = $val;
    }

    return $parametros;
}

public static function buildListaActivQueReturnParametros(array $state): array
{
    $parametros = [];
    foreach ([
        'que', 'seccion', 'status', 'empiezamin', 'empiezamax', 'asist', 'c_activ', 'tit_list_grupo',
    ] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    return $parametros;
}

public static function buildListaActivReturnParametros(array $state): array
{
    $parametros = [];
    foreach ([
        'que', 'status', 'id_tipo_activ', 'filtro_lugar', 'id_ubi', 'periodo', 'year', 'dl_org',
        'empiezamin', 'empiezamax', 'c_activ', 'asist', 'seccion', 'ssfsv', 'sasistentes',
        'sactividad', 'snom_tipo', 'titulo', 'tit_list_grupo',
    ] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    $idSel = self::normalizeIdSel($state['id_sel'] ?? null);
    $scrollIdRaw = $state['scroll_id'] ?? null;
    $scrollId = is_scalar($scrollIdRaw) ? (string) $scrollIdRaw : '';

    return self::mergeSelectionIntoReturnParametros($parametros, $idSel === '' ? null : $idSel, $scrollId);
}

public static function buildListaActividadesSgReturnParametros(array $state): array
{
    $parametros = [];
    foreach ([
        'que', 'tipo_activ_sg', 'id_ubi', 'periodo', 'year', 'dl_org', 'status',
        'empiezamin', 'empiezamax', 'filtro_lugar',
    ] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    $idSel = self::normalizeIdSel($state['id_sel'] ?? null);
    $scrollIdRaw = $state['scroll_id'] ?? null;
    $scrollId = is_scalar($scrollIdRaw) ? (string) $scrollIdRaw : '';

    return self::mergeSelectionIntoReturnParametros($parametros, $idSel === '' ? null : $idSel, $scrollId);
}

public static function buildListaSrCsvQueReturnParametros(array $state): array
{
    $parametros = [];
    foreach ([
        'periodo', 'year', 'empiezamin', 'empiezamax', 'c_activ', 'status', 'id_cdc',
    ] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    return $parametros;
}

public static function buildListaSrCsvReturnParametros(array $state): array
{
    $parametros = self::buildListaSrCsvQueReturnParametros($state);
    if (!array_key_exists('que', $state)) {
        return $parametros;
    }
    $que = $state['que'];
    if ($que !== '' && $que !== null) {
        $parametros['que'] = $que;
    }

    return $parametros;
}

public static function buildActividadesCentroQueReturnParametros(array $state): array
{
    $parametros = [];
    foreach ([
        'tipo_ctr', 'tipo_lista', 'ver_ctr', 'periodo', 'year', 'empiezamin', 'empiezamax',
    ] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    return $parametros;
}

public static function buildActividadVerReturnParametros(array $state): array
{
    $parametros = [];
    foreach ([
        'mod', 'obj_pau', 'id_activ', 'id_tipo_activ', 'ssfsv', 'sasistentes', 'sactividad', 'snom_tipo',
    ] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    return self::mergeSelectionIntoReturnParametros(
        $parametros,
        self::normalizeIdSel($state['id_sel'] ?? null) ?: null,
        is_scalar($state['scroll_id'] ?? null) ? (string) ($state['scroll_id'] ?? '') : '',
    );
}

/**
 * Sincroniza el state del padre al entrar en lista_activ (NavStack v2).
 *
 * @param array<string, mixed> $state
 */
public static function syncListaActivParent(Posicion $oPosicion, array $state): void
{
    $parent = $oPosicion->nav()->peek(1);
    if ($parent === null) {
        return;
    }
    $parentUrl = is_string($parent['url'] ?? null) ? $parent['url'] : '';
    if (str_contains($parentUrl, 'lista_activ_que.php')) {
        self::syncNavStateAt($oPosicion, 1, self::buildListaActivQueReturnParametros($state));
    } elseif (str_contains($parentUrl, 'actividad_que.php')) {
        self::syncNavStateAt($oPosicion, 1, self::buildActividadQueReturnParametros($state));
    }
}

public static function repersistStackEntryFromGstack(?int $gstackOverride = null, array $paramKeys = []): void
{
    unset($gstackOverride, $paramKeys);
}

public static function actividadSelectControllerSuffix(): string
{
    return 'frontend/actividades/controller/actividad_select.php';
}

public static function rewriteStackEntryUrl(int $gstack, string $requiredSuffix): void
{
    unset($gstack, $requiredSuffix);
}

public static function persistActividadQueParent(Posicion $oPosicion, array $parametros): void
{
    self::persistParentIfUrl($oPosicion, $parametros, 'actividad_que.php');
}

public static function stackEntryUrl(int $n = 0): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    /** @var list<array<string, mixed>> $stack */
    $stack = is_array($_SESSION['nav']['stack'] ?? null) ? $_SESSION['nav']['stack'] : [];
    session_write_close();
    $idx = count($stack) - 1 - $n;
    if ($idx < 0 || !isset($stack[$idx])) {
        return '';
    }
    $url = $stack[$idx]['url'] ?? '';

    return is_string($url) ? $url : '';
}

public static function buildDossiersVerFromActividadSelectPost(): array
{
    $parametros = [];
    foreach (['queSel', 'que', 'id_dossier', 'permiso', 'mod', 'pau', 'obj_pau'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }
    if (!isset($parametros['pau'])) {
        $parametros['pau'] = 'a';
    }
    if (!isset($parametros['obj_pau'])) {
        $parametros['obj_pau'] = 'Actividad';
    }
    $aSel = self::selFromPost();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
        $first = $aSel[0];
        $idPau = (int) strtok((string) $first, '#');
        if ($idPau > 0) {
            $parametros['id_pau'] = $idPau;
        }
    }

    return self::mergeSelectionIntoReturnParametros(
        $parametros,
        self::idSelFromPost(),
        self::scrollIdFromPost(),
    );
}

public static function bootDossiersFromActividadSelect(Posicion $oPosicion, int $parar = 0): void
{
    unset($oPosicion, $parar);
}

public static function persistCleanReturnToPosicion(Posicion $oPosicion, array $parametros, int $n = 0): void
{
    self::syncNavStateAt($oPosicion, $n, $parametros);
}

public static function restoreSelectionFromStackPost(): array
{
    $idSel = self::idSelFromPost();

    return [
        'id_sel' => self::idSelIsEmpty($idSel) ? '' : $idSel,
        'scroll_id' => self::scrollIdFromPost(),
    ];
}

public static function buildPersonasQueReturnParametros(array $state): array
{
    $parametros = [];
    foreach (['na', 'que', 'tipo', 'tabla', 'exacto', 'cmb', 'nombre', 'apellido1', 'apellido2', 'centro'] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null) {
            continue;
        }
        $parametros[$key] = $val;
    }
    if (array_key_exists('es_sacd', $state)) {
        $esSacd = $state['es_sacd'];
        if (is_int($esSacd)) {
            $parametros['es_sacd'] = $esSacd;
        } elseif (is_string($esSacd) && is_numeric($esSacd)) {
            $parametros['es_sacd'] = (int) $esSacd;
        }
    }

    $idSel = self::normalizeIdSel($state['id_sel'] ?? null);
    $scrollIdRaw = $state['scroll_id'] ?? null;
    $scrollId = is_scalar($scrollIdRaw) ? (string) $scrollIdRaw : '';

    return self::mergeSelectionIntoReturnParametros($parametros, $idSel === '' ? null : $idSel, $scrollId);
}

public static function buildPersonasSelectReturnParametros(): array
{
    $parametros = [];
    foreach (['que', 'exacto', 'cmb', 'nombre', 'apellido1', 'apellido2', 'centro', 'tabla', 'na', 'tipo'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }
    $esSacd = filter_input(INPUT_POST, 'es_sacd', FILTER_VALIDATE_INT);
    if (is_int($esSacd)) {
        $parametros['es_sacd'] = $esSacd;
    }
    $idSel = self::idSelFromPost();
    if (!self::idSelIsEmpty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    $scrollId = self::scrollIdFromPost();
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
}

public static function buildActaSelectReturnParametros(): array
{
    $parametros = [];
    foreach (['titulo', 'acta'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }
    $idSel = self::idSelFromPost();
    if (!self::idSelIsEmpty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    $scrollId = self::scrollIdFromPost();
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
}

public static function buildTesseraReturnParametros(): array
{
    $parametros = self::buildPersonasSelectReturnParametros();

    $idNom = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);
    if (is_int($idNom) && $idNom > 0) {
        $parametros['id_nom'] = $idNom;
    }
    $idTabla = filter_input(INPUT_POST, 'id_tabla');
    if (is_scalar($idTabla) && (string) $idTabla !== '') {
        $parametros['id_tabla'] = (string) $idTabla;
    }
    $aSel = self::selFromPost();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    }

    return $parametros;
}

public static function buildCertificadoImprimirParentReturnParametros(): array
{
    $parametros = array_merge(
        self::buildDossierReturnParametros(),
        self::buildPersonasSelectReturnParametros(),
    );
    $aSel = self::selFromPost();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    }
    $idNom = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);
    if (is_int($idNom) && $idNom > 0) {
        $parametros['id_nom'] = $idNom;
    }

    return $parametros;
}

public static function buildE43ParentReturnParametros(): array
{
    $parametros = self::buildDossierReturnParametros();
    $idPau = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
    if (is_int($idPau) && $idPau > 0) {
        $parametros['id_pau'] = $idPau;
    }
    $aSel = self::selFromPost();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    }

    return $parametros;
}

public static function buildActaNotasReturnParametros(): array
{
    $parametros = self::buildDossierReturnParametros();

    $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
    if (is_int($idActivRaw) && $idActivRaw > 0) {
        $parametros['id_activ'] = $idActivRaw;
    }
    $idAsigRaw = filter_input(INPUT_POST, 'id_asignatura', FILTER_VALIDATE_INT);
    if (is_int($idAsigRaw) && $idAsigRaw > 0) {
        $parametros['id_asignatura'] = $idAsigRaw;
    }

    $aSel = self::selFromPost();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    } elseif (
        isset($parametros['id_activ'], $parametros['id_asignatura'])
        && is_int($parametros['id_activ'])
        && is_int($parametros['id_asignatura'])
    ) {
        $parametros['sel'] = [$parametros['id_activ'] . '#' . $parametros['id_asignatura']];
    }

    foreach (['opcional', 'primary_key_s', 'id_nivel'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }

    return $parametros;
}

public static function persistActaNotasReturnToPosicion(Posicion $oPosicion, int $n = 0): void
{
    $parametros = self::buildActaNotasReturnParametros();
    if ($parametros === [] || !isset($parametros['id_activ'], $parametros['id_asignatura'])) {
        return;
    }
    self::persistCleanReturnToPosicion($oPosicion, $parametros, $n);
}

public static function buildActaImprimirParentReturnParametros(): array
{
    $actaNotas = self::buildActaNotasReturnParametros();
    if (isset($actaNotas['id_activ'], $actaNotas['id_asignatura'])) {
        return $actaNotas;
    }

    return self::buildActaSelectReturnParametros();
}

public static function persistActaImprimirParentReturnToPosicion(Posicion $oPosicion, int $n = 1): void
{
    self::persistCleanReturnToPosicion(
        $oPosicion,
        self::buildActaImprimirParentReturnParametros(),
        $n,
    );
}

public static function persistTesseraImprimirParentReturnToPosicion(Posicion $oPosicion, int $n = 1): void
{
    self::persistCleanReturnToPosicion(
        $oPosicion,
        self::buildTesseraReturnParametros(),
        $n,
    );
}

public static function persistTesseraReturnToPosicion(Posicion $oPosicion, int $n = 0): void
{
    self::persistCleanReturnToPosicion(
        $oPosicion,
        self::buildTesseraReturnParametros(),
        $n,
    );
}

public static function persistCertificadoImprimirParentReturnToPosicion(Posicion $oPosicion, int $n = 1): void
{
    self::persistCleanReturnToPosicion(
        $oPosicion,
        self::buildCertificadoImprimirParentReturnParametros(),
        $n,
    );
}

public static function persistE43ParentReturnToPosicion(Posicion $oPosicion, int $n = 1): void
{
    self::persistCleanReturnToPosicion(
        $oPosicion,
        self::buildE43ParentReturnParametros(),
        $n,
    );
}

public static function persistActaSelectReturnToPosicion(Posicion $oPosicion, int $n = 0): void
{
    self::persistCleanReturnToPosicion(
        $oPosicion,
        self::buildActaSelectReturnParametros(),
        $n,
    );
}

public static function persistPersonasSelectReturnToPosicion(Posicion $oPosicion, int $n = 0): void
{
    self::persistCleanReturnToPosicion(
        $oPosicion,
        self::buildPersonasSelectReturnParametros(),
        $n,
    );
}

public static function applyRestoredSelectionToApiPayload(
    array &$apiPayload,
    mixed $restoredIdSelFromStack = null,
    mixed $restoredScrollIdFromStack = null,
): void {
    $idSel = $restoredIdSelFromStack;
    if ($idSel === null || $idSel === '') {
        $idSel = self::idSelFromPost();
    } else {
        $idSel = self::normalizeIdSel($idSel);
    }
    if (!self::idSelIsEmpty($idSel)) {
        $apiPayload['restored_id_sel'] = $idSel;
    }

    $scroll = is_scalar($restoredScrollIdFromStack) ? (string) $restoredScrollIdFromStack : '';
    if ($scroll === '' || $scroll === '0') {
        $scroll = self::scrollIdFromPost();
    }
    if ($scroll !== '' && $scroll !== '0') {
        $apiPayload['restored_scroll_id'] = $scroll;
    }
}

/**
 * Identity NavStack v2 para una entrada de dossiers_ver (segmento de dossier).
 *
 * @param array<string, mixed> $state
 * @return array<string, mixed>
 */
public static function buildDossiersVerNavIdentity(array $state): array
{
    $identity = [];
    foreach (['queSel', 'id_dossier', 'pau', 'obj_pau', 'id_pau', 'clase_info'] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if (is_scalar($val) && (string) $val !== '') {
            $identity[$key] = $val;
        }
    }
    $mod = isset($state['mod']) && is_scalar($state['mod']) ? (string) $state['mod'] : '';
    if ($mod !== '' && !self::isDossiersEphemeralMod($mod)) {
        $identity['mod'] = $mod;
    }

    return $identity;
}

/**
 * mod transitorio de refresh/AJAX en dossiers: no forma parte de la identity NavStack.
 */
private static function isDossiersEphemeralMod(string $mod): bool
{
    return in_array($mod, ['refresh', 'matricular', 'sel_es_asistente'], true);
}

/**
 * Identity NavStack v2 para un formulario hijo de dossier (segmento #fichaNNNN).
 *
 * @param array<string, mixed> $state
 * @return array<string, mixed>
 */
public static function buildDossierChildNavIdentity(array $state): array
{
    $identity = self::buildDossiersVerNavIdentity($state);
    foreach (['id_activ', 'id_nom', 'id_asignatura', 'id_cargo'] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if (!is_scalar($val)) {
            continue;
        }
        $s = (string) $val;
        if ($s === '' || $s === '0') {
            continue;
        }
        $identity[$key] = $val;
    }

    return $identity;
}

/**
 * enter() NavStack v2 desde un formulario hijo de dossier.
 */
public static function enterDossierChildNav(Posicion $oPosicion): void
{
    $state = self::buildDossierReturnParametros();
    foreach (['mod', 'id_activ', 'depende', 'refresh', 'actualizar', 'id_nom', 'id_asignatura', 'id_cargo', 'id_nivel', 'opcional', 'primary_key_s'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $state[$key] = $raw;
        }
    }

    $bloqueRaw = filter_input(INPUT_POST, 'bloque');
    $bloque = is_string($bloqueRaw) && $bloqueRaw !== '' ? $bloqueRaw : '#main';
    $idDossierRaw = filter_input(INPUT_POST, 'id_dossier', FILTER_VALIDATE_INT);
    if ((!is_string($bloqueRaw) || $bloqueRaw === '') && is_int($idDossierRaw) && $idDossierRaw > 0) {
        $bloque = '#ficha' . $idDossierRaw;
    }

    $oPosicion->nav()->enter(
        (string) ($_SERVER['PHP_SELF'] ?? ''),
        $bloque,
        self::buildDossierChildNavIdentity($state),
        $state,
    );
}

/**
 * Pasos fnjs_nav_atras desde dossiers_ver (salta segmentos dossier intermedios si aplica).
 */
public static function navBackStepsFromDossiersVer(NavStack $nav): int
{
    if (self::dossiersCurrentIsAsistentesSegment()) {
        return $nav->backStepsSkippingUrls(
            static fn (string $url): bool => self::isSkippableBetweenActividadSelectAndChild($url),
        );
    }

    $currentSeg = self::dossiersSegmentKeyFromParametros(self::stackTopParametros());
    if ($currentSeg !== '') {
        $duplicateLayers = 0;
        for ($n = 0; $n < 15; $n++) {
            $entry = $nav->peek($n);
            if ($entry === null) {
                break;
            }
            $url = is_string($entry['url'] ?? null) ? $entry['url'] : '';
            if (!str_contains($url, 'dossiers_ver.php')) {
                break;
            }
            if (self::dossiersSegmentKeyFromEntry($entry) !== $currentSeg) {
                break;
            }
            $duplicateLayers++;
        }
        if ($duplicateLayers > 1) {
            return $duplicateLayers;
        }
    }

    $parent = $nav->peek(1);
    if ($parent !== null) {
        $parentUrl = is_string($parent['url'] ?? null) ? $parent['url'] : '';
        if (str_contains($parentUrl, 'dossiers_ver.php')) {
            return 1;
        }
    }

    return $nav->backStepsSkippingUrls(
        static fn (string $url): bool => self::isSkippableBetweenActividadSelectAndChild($url),
    );
}

/**
 * Sincroniza id_sel/scroll_id del POST en actividad_select padre (NavStack v2).
 */
public static function syncActividadSelectParentSelection(Posicion $oPosicion): void
{
    $patch = self::buildSelectionStatePatchFromPost();
    if ($patch === []) {
        return;
    }

    $nav = $oPosicion->nav();
    for ($n = 1; $n < 20; $n++) {
        $entry = $nav->peek($n);
        if ($entry === null) {
            break;
        }
        $parentUrl = is_string($entry['url'] ?? null) ? $entry['url'] : '';
        if (!str_contains($parentUrl, 'actividad_select.php')) {
            continue;
        }
        $nav->updateStateAt($n, $patch);

        return;
    }
}

/**
 * @return list<array<string, mixed>>
 */
private static function navStackSessionEntries(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    /** @var list<array<string, mixed>> $stack */
    $stack = is_array($_SESSION['nav']['stack'] ?? null) ? $_SESSION['nav']['stack'] : [];
    session_write_close();

    return $stack;
}
}
