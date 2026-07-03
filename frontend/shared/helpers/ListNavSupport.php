<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\web\Posicion;

/**
 * Navegación con {@see Posicion} tras `FrontBootstrap::boot()`.
 *
 * Guía: `documentacion/posicion_nav_post_frontbootstrap.md`
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
    $raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($raw)) {
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
        if ($out !== []) {
            return $out;
        }
    }

    $fromIdSel = self::idSelForLista($_POST['id_sel'] ?? null);
    if (self::idSelIsEmpty($fromIdSel)) {
        return [];
    }

    return is_array($fromIdSel) ? $fromIdSel : [$fromIdSel];
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
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        return null;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = [];
    foreach ($_SESSION['position'] as $key => $entry) {
        if (!is_array($entry)) {
            return null;
        }
        $position[$key] = $entry;
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
    $postIdSel = $_POST['id_sel'] ?? null;
    if ($postIdSel !== null) {
        $normalized = self::idSelForLista($postIdSel);
        if (!self::idSelIsEmpty($normalized)) {
            return $normalized;
        }
    }

    $aSel = self::selFromPost();
    if ($aSel !== []) {
        return self::idSelForLista($aSel);
    }

    return '';
}

public static function persistSelectionToPosicion(Posicion $oPosicion, int $n = 1): void
{
    $aSel = self::selFromPost();
    $scrollId = self::scrollIdFromPost();
    if ($aSel === [] && $scrollId === '') {
        return;
    }

    $persist = [];
    if ($aSel !== []) {
        $persist['id_sel'] = count($aSel) === 1 ? $aSel[0] : $aSel;
    }
    if ($scrollId !== '') {
        $persist['scroll_id'] = $scrollId;
    }
    $oPosicion->setParametros($persist, $n);
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

    // Tras recordar(): n=0 guarda en la entrada actual; al volver con stack, en la que restauramos.
    $oPosicion->setParametros($persist, $returningViaStack ? 1 : 0);
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

    $idPauRaw = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
    $idPau = is_int($idPauRaw) ? $idPauRaw : 0;
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
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }

    return $parametros;
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
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position']) || $_SESSION['position'] === []) {
        session_write_close();

        return [];
    }
    $stack = $_SESSION['position'];
    end($stack);
    $raw = current($stack);
    session_write_close();
    if (!is_array($raw) || !isset($raw['parametros']) || !is_array($raw['parametros'])) {
        return [];
    }

    return $raw['parametros'];
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
    if ($mod !== '' && $mod !== 'refresh') {
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
    // Desde la lista de asistentes el destino es siempre actividad_select (u otro listado
    // externo), aunque en la pila quede matrículas / plan de estudios como padre inmediato.
    if (self::dossiersCurrentIsAsistentesSegment()) {
        return self::mostrarLeftSlideToListParentFromDossiers($oPosicion);
    }

    if (self::stackParentIsDossiersVer(1)) {
        return $oPosicion->mostrar_left_slide(1);
    }

    return self::mostrarLeftSlideToListParentFromDossiers($oPosicion);
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
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return false;
    }
    foreach ($_SESSION['position'] as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            continue;
        }
        $parametros = isset($entry['parametros']) && is_array($entry['parametros']) ? $entry['parametros'] : [];
        $queSel = isset($parametros['queSel']) && is_scalar($parametros['queSel']) ? (string) $parametros['queSel'] : '';
        $idDossier = isset($parametros['id_dossier']) ? (int) $parametros['id_dossier'] : 0;
        $idPau = isset($parametros['id_pau']) ? (int) $parametros['id_pau'] : 0;
        if (($queSel === 'asis' || $idDossier === 3101) && $idPau === $idActiv) {
            session_write_close();

            return true;
        }
    }
    session_write_close();

    return false;
}

public static function ensureAsistentesDossierBeforeActividadSelectChild(Posicion $oPosicion, int $idActiv): void
{
    unset($oPosicion);
    if ($idActiv <= 0 || self::stackParentIsDossiersVer(1)) {
        return;
    }
    if (self::stackHasAsistentesDossierForActiv($idActiv)) {
        return;
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $snapshot = $_SESSION['list_nav_asistentes_dossier_snapshot'] ?? null;
    if (!is_array($snapshot)) {
        session_write_close();

        return;
    }
    $snapIdPau = isset($snapshot['id_pau']) ? (int) $snapshot['id_pau'] : 0;
    if ($snapIdPau !== $idActiv) {
        session_write_close();

        return;
    }
    $parametros = $snapshot;
    foreach (array_merge(self::metaHashPostKeys(), self::stackEphemeralPostKeys()) as $key) {
        unset($parametros[$key]);
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        $_SESSION['position'] = [];
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = &$_SESSION['position'];
    end($position);
    $topKey = key($position);
    $base = ($topKey === null || $topKey === '') ? 0 : (int) $topKey;
    $newKey = $base + 1;
    $parametros['stack'] = $newKey;
    $position[$newKey] = [
        'url' => self::dossiersVerDefaultUrl(),
        'bloque' => '#main',
        'parametros' => $parametros,
        'stack' => $newKey,
    ];
    session_write_close();
}

public static function stackHasDossiersVer(): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return false;
    }
    foreach ($_SESSION['position'] as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (str_contains($url, 'dossiers_ver.php')) {
            session_write_close();

            return true;
        }
    }
    session_write_close();

    return false;
}

public static function stackTopIsDossiersVer(): bool
{
    return self::stackParentIsDossiersVer(0);
}

public static function findBestDossiersStackEntry(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return null;
    }
    $best = null;
    $bestKey = -1;
    foreach ($_SESSION['position'] as $key => $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            continue;
        }
        $intKey = is_int($key) ? $key : (int) $key;
        if ($intKey >= $bestKey) {
            $bestKey = $intKey;
            $best = $entry;
        }
    }
    session_write_close();

    return $best;
}

public static function findBestDossiersStackKey(): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return -1;
    }
    $bestKey = -1;
    foreach ($_SESSION['position'] as $key => $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            continue;
        }
        $intKey = is_int($key) ? $key : (int) $key;
        if ($intKey >= $bestKey) {
            $bestKey = $intKey;
        }
    }
    session_write_close();

    return $bestKey;
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
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        return;
    }
    /** @var list<array<string, mixed>> $position */
    $position = array_values($_SESSION['position']);
    foreach ($position as $key => $values) {
        $position[$key]['stack'] = $key;
    }
    $_SESSION['position'] = $position;
}

public static function purgeDossierChildFormsFromStack(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return;
    }
    /** @var list<array<string, mixed>> $kept */
    $kept = [];
    foreach ($_SESSION['position'] as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if ($url !== '' && self::isDossierChildFormUrl($url)) {
            continue;
        }
        $kept[] = $entry;
    }
    $_SESSION['position'] = $kept;
    self::reindexPositionStack();
    session_write_close();
}

public static function purgeDossierNavigationFromStack(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return;
    }
    /** @var list<array<string, mixed>> $kept */
    $kept = [];
    foreach ($_SESSION['position'] as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if ($url !== '' && self::isDossierShellOrChildUrl($url)) {
            continue;
        }
        $kept[] = $entry;
    }
    $_SESSION['position'] = $kept;
    self::reindexPositionStack();
    session_write_close();
}

public static function findStackKeyByUrlContains(string $needle): int
{
    if ($needle === '') {
        return -1;
    }
    $wasActive = session_status() === PHP_SESSION_ACTIVE;
    if (!$wasActive) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        if (!$wasActive) {
            session_write_close();
        }

        return -1;
    }
    $bestKey = -1;
    foreach ($_SESSION['position'] as $key => $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if ($url !== '' && str_contains($url, $needle)) {
            $intKey = is_int($key) ? $key : (int) $key;
            if ($intKey >= $bestKey) {
                $bestKey = $intKey;
            }
        }
    }
    if (!$wasActive) {
        session_write_close();
    }

    return $bestKey;
}

public static function stackFromPost(): int
{
    $raw = filter_input(INPUT_POST, 'stack', FILTER_VALIDATE_INT);

    return is_int($raw) ? $raw : 0;
}

public static function isReturningViaStack(): bool
{
    return self::stackFromPost() > 0;
}

public static function refreshStackEntryAtIndex(Posicion $oPosicion, int $stackIndex, string $bloque = '#main'): bool
{
    if ($stackIndex < 0) {
        return false;
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return false;
    }
    /** @var list<array<string, mixed>> $position */
    $position = array_values($_SESSION['position']);
    if (!array_key_exists($stackIndex, $position)) {
        session_write_close();

        return false;
    }
    $position = array_slice($position, 0, $stackIndex + 1);

    $parametros = self::buildReturnParametrosFromPost();
    $parametros['stack'] = $stackIndex;

    $phpSelf = $_SERVER['PHP_SELF'] ?? '';
    $urlFromPosition = $position[$stackIndex]['url'] ?? null;
    $urlFallback = is_string($urlFromPosition) ? $urlFromPosition : '';
    $url = is_string($phpSelf) && $phpSelf !== '' ? $phpSelf : $urlFallback;

    $position[$stackIndex]['url'] = $url;
    $position[$stackIndex]['bloque'] = $bloque;
    $position[$stackIndex]['parametros'] = $parametros;
    $position[$stackIndex]['stack'] = $stackIndex;
    $_SESSION['position'] = $position;
    self::reindexPositionStack();
    session_write_close();

    return $oPosicion->goStack($stackIndex);
}

public static function bootListPageAfterStackReturn(Posicion $oPosicion, int $stackFromPost): void
{
    self::purgeDossierNavigationFromStack();

    $index = $stackFromPost;
    $phpSelf = $_SERVER['PHP_SELF'] ?? '';
    $needle = is_string($phpSelf) && $phpSelf !== '' ? basename($phpSelf) : '';

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (self::sessionStackEntry($index) === null) {
        $found = $needle !== '' ? self::findStackKeyByUrlContains($needle) : -1;
        if ($found >= 0) {
            $index = $found;
        }
    }
    session_write_close();

    if ($index >= 0 && self::refreshStackEntryAtIndex($oPosicion, $index)) {
        return;
    }

    self::bootRecordar($oPosicion);
}

public static function stackTopIsDossierChildForm(): bool
{
    $url = self::stackEntryUrl(0);

    return $url !== '' && self::isDossierChildFormUrl($url);
}

public static function olvidarForwardFromDossiersSlot(int $preferredIndex): void
{
    $index = $preferredIndex;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $entry = $index > 0 ? self::sessionStackEntry($index) : null;
    if ($entry !== null) {
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            $index = -1;
        }
    } else {
        $index = -1;
    }
    if ($index < 0) {
        $index = self::findBestDossiersStackKey();
    }
    session_write_close();
    if ($index < 0) {
        return;
    }
    $oPosicion = new Posicion();
    if ($oPosicion->goStack($index)) {
        $oPosicion->olvidar($index);
    }
}

public static function backStepsToListParentFromDossiers(int $max = 15): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position']) || $_SESSION['position'] === []) {
        session_write_close();

        return 1;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = $_SESSION['position'];
    $stack = $position;
    end($stack);
    $steps = 0;
    while ($steps < $max) {
        if (prev($stack) === false) {
            break;
        }
        $steps++;
        $raw = current($stack);
        if (!is_array($raw)) {
            continue;
        }
        $url = isset($raw['url']) && is_string($raw['url']) ? $raw['url'] : '';
        if ($url !== '' && !self::isSkippableBetweenActividadSelectAndChild($url)) {
            session_write_close();

            return max(1, $steps);
        }
    }
    session_write_close();

    return 1;
}

public static function mostrarLeftSlideToListParentFromDossiers(Posicion $oPosicion): string
{
    return $oPosicion->mostrar_left_slide(self::backStepsToListParentFromDossiers());
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
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position']) || $_SESSION['position'] === []) {
        session_write_close();

        return 1;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = $_SESSION['position'];
    $stack = $position;
    end($stack);
    $steps = 0;
    while ($steps < $max) {
        if (prev($stack) === false) {
            break;
        }
        $steps++;
        $raw = current($stack);
        if (!is_array($raw)) {
            continue;
        }
        $url = isset($raw['url']) && is_string($raw['url']) ? $raw['url'] : '';
        if (str_contains($url, 'dossiers_ver.php')) {
            session_write_close();

            return $steps;
        }
    }
    session_write_close();

    return 1;
}

public static function mostrarLeftSlideToDossiersParent(Posicion $oPosicion): string
{
    return $oPosicion->mostrar_left_slide(self::backStepsToDossiersParent());
}

public static function jsAtrasToDossiersParent(Posicion $oPosicion): string
{
    return $oPosicion->js_atras(self::backStepsToDossiersParent());
}

public static function goAtrasToDossiersParent(Posicion $oPosicion): string
{
    return $oPosicion->go_atras(self::backStepsToDossiersParent());
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
    if (self::stackTopIsDossiersVer()) {
        return;
    }
    $existing = self::findBestDossiersStackEntry();
    if ($existing !== null) {
        $parametros = isset($existing['parametros']) && is_array($existing['parametros'])
            ? $existing['parametros']
            : [];
        foreach (array_merge(self::metaHashPostKeys(), self::stackEphemeralPostKeys()) as $key) {
            unset($parametros[$key]);
        }
        $bloque = '#main';
        $dossiersUrl = isset($existing['url']) && is_string($existing['url'])
            ? $existing['url']
            : self::dossiersVerDefaultUrl();
    } else {
        $parametros = self::buildReturnParametrosFromPost();
        $idDossierRaw = $parametros['id_dossier'] ?? null;
        $idDossier = is_string($idDossierRaw)
            ? trim($idDossierRaw)
            : (is_scalar($idDossierRaw) ? trim((string) $idDossierRaw) : '');
        if ($idDossier === '') {
            return;
        }
        $bloque = '#main';
        $dossiersUrl = self::dossiersVerDefaultUrl();
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        $_SESSION['position'] = [];
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = &$_SESSION['position'];
    end($position);
    $topKey = key($position);
    $base = ($topKey === null || $topKey === '') ? 0 : (int) $topKey;
    $newKey = $base + 1;
    $parametros['stack'] = $newKey;
    $position[$newKey] = [
        'url' => $dossiersUrl,
        'bloque' => $bloque,
        'parametros' => $parametros,
        'stack' => $newKey,
    ];
    session_write_close();
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
    self::ensureDossiersOnStackBeforeChild();
    self::bootRecordar($oPosicion, $parar);
    self::persistDossierParentSelectionIfDossier($oPosicion);
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
    $oPosicion->setParametro('stack', 0);
}

public static function bootRecordar(Posicion $oPosicion, int $parar = 0): void
{
    self::clearInheritedStackForRecordar($oPosicion);
    $oPosicion->recordar($parar);
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
    $gstack = filter_input(INPUT_POST, 'Gstack', FILTER_VALIDATE_INT);
    if (is_int($gstack) && $gstack > 0) {
        self::repersistStackEntryFromGstack($gstack);
    }
    self::bootRecordar($oPosicion, $parar);
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
    $oPosicion->setParametros($parametros, 1);
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

public static function repersistStackEntryFromGstack(?int $gstackOverride = null, array $paramKeys = []): void
{
    $gstack = $gstackOverride ?? filter_input(INPUT_POST, 'Gstack', FILTER_VALIDATE_INT);
    if (!is_int($gstack) || $gstack === 0) {
        return;
    }

    $oRestore = new Posicion();
    if (!$oRestore->goStack($gstack)) {
        return;
    }

    if ($paramKeys === []) {
        $useActividadSelectReturn = true;
        $paramKeys = [
            'modo', 'que', 'status', 'id_tipo_activ', 'filtro_lugar', 'id_ubi', 'nom_activ',
            'periodo', 'year', 'dl_org', 'empiezamin', 'empiezamax',
            'fases_on', 'fases_off', 'publicado', 'listar_asistentes',
            'ssfsv', 'sasistentes', 'sactividad', 'sactividad2', 'extendida',
            'id_sel', 'scroll_id',
        ];
    } else {
        $useActividadSelectReturn = false;
    }

    $state = [];
    foreach ($paramKeys as $key) {
        $val = $oRestore->getParametro($key);
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $state[$key] = $val;
    }

    if ($state === []) {
        return;
    }

    $parametros = $useActividadSelectReturn
        ? self::buildActividadSelectReturnParametros($state)
        : $state;

    $parametros['stack'] = $gstack;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (self::sessionStackEntry($gstack) === null) {
        session_write_close();

        return;
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = &$_SESSION['position'];
    if (!array_key_exists($gstack, $position)) {
        session_write_close();

        return;
    }
    $position[$gstack]['parametros'] = $parametros;
    if ($useActividadSelectReturn) {
        self::rewriteStackEntryUrl($gstack, self::actividadSelectControllerSuffix());
    }
    session_write_close();
}

public static function actividadSelectControllerSuffix(): string
{
    return 'frontend/actividades/controller/actividad_select.php';
}

public static function rewriteStackEntryUrl(int $gstack, string $requiredSuffix): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $entry = self::sessionStackEntry($gstack);
    if ($entry === null || !isset($entry['url']) || !is_string($entry['url'])) {
        session_write_close();

        return;
    }
    $current = $entry['url'];
    if (str_contains($current, $requiredSuffix)) {
        session_write_close();

        return;
    }
    $pos = strpos($current, 'frontend/');
    $newUrl = $pos !== false
        ? substr($current, 0, $pos) . $requiredSuffix
        : $requiredSuffix;
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = &$_SESSION['position'];
    if (!array_key_exists($gstack, $position)) {
        session_write_close();

        return;
    }
    $position[$gstack]['url'] = $newUrl;
    session_write_close();
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
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position']) || $_SESSION['position'] === []) {
        session_write_close();

        return '';
    }
    $stack = $_SESSION['position'];
    end($stack);
    for ($i = 0; $i < $n; $i++) {
        if (prev($stack) === false) {
            reset($stack);
            break;
        }
    }
    $raw = current($stack);
    session_write_close();
    if (!is_array($raw) || !isset($raw['url']) || !is_string($raw['url'])) {
        return '';
    }

    return $raw['url'];
}

public static function bootActividadSelectChildRecordar(Posicion $oPosicion, int $parar = 0): void
{
    self::bootChildFromListRecordar($oPosicion, $parar);
}

public static function persistActividadSelectChildEntry(Posicion $oPosicion, array $extra = []): void
{
    $parametros = $extra;
    $aSel = self::selFromPost();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    }
    $parametros = self::mergeSelectionIntoReturnParametros(
        $parametros,
        self::idSelFromPost(),
        self::scrollIdFromPost(),
    );
    foreach (['pau', 'obj_pau', 'queSel', 'id_dossier', 'permiso', 'Gstack', 'stack'] as $strip) {
        unset($parametros[$strip]);
    }
    self::persistRecordarEntry($oPosicion, $parametros);
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
    self::repersistStackEntryFromGstack();
    self::bootRecordar($oPosicion, $parar);
    // No reemplazar parametros: recordar() ya guardó el POST completo del formulario.
}

public static function persistCleanReturnToPosicion(Posicion $oPosicion, array $parametros, int $n = 0): void
{
    if ($parametros === []) {
        return;
    }
    $oPosicion->replaceStackParametros($parametros, $n);
}

public static function restoreSelectionFromStackPost(): array
{
    $result = ['id_sel' => '', 'scroll_id' => ''];
    $stackFromPost = isset($_POST['stack']) ? (int) filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT) : 0;
    if ($stackFromPost === 0) {
        return $result;
    }

    $oPosicionRestore = new Posicion();
    if (!$oPosicionRestore->goStack($stackFromPost)) {
        return $result;
    }

    $restoredSel = self::idSelForLista($oPosicionRestore->getParametro('id_sel'));
    if (!self::idSelIsEmpty($restoredSel)) {
        $result['id_sel'] = $restoredSel;
    }
    $restoredScroll = $oPosicionRestore->getParametro('scroll_id');
    if (is_scalar($restoredScroll) && (string) $restoredScroll !== '') {
        $result['scroll_id'] = (string) $restoredScroll;
    }
    $oPosicionRestore->olvidar($stackFromPost);

    return $result;
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
}
