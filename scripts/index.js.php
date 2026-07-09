<?php
// JavaScript functions for index.php
// This file contains all the JavaScript functions that were previously embedded in index.php

// This file requires the $h variable to be defined before inclusion
use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use src\shared\config\ConfigGlobal;
if (!isset($h)) {
    $h = '';
}

$navAtrasUrl = 'frontend/shared/controller/nav_atras.php';
$oHashNavAtras = new HashFront();
$oHashNavAtras->setUrl($navAtrasUrl);
$oHashNavAtras->setCamposNo('n');
$navAtrasSignedParams = $oHashNavAtras->linkSinValParams();

$navStateUrl = 'frontend/shared/controller/nav_state.php';
$oHashNavState = new HashFront();
$oHashNavState->setUrl($navStateUrl);
$oHashNavState->setCamposNo('nav_patch');
$navStateSignedParams = $oHashNavState->linkSinValParams();
?>
<?php // language=JavaScript ?>
<script>
    $(document).ready(function () {
        const $cargando = $('#cargando'); // Guardem el selector una sola vegada
        $('#cargando').hide();  // hide it initially

        $(document).ajaxStart(function () {
            $('#cargando').show();
        });

        $(document).ajaxStop(function () {
            $('#cargando').hide();
        });
    })

    // Normaliza una URL para usarla como clave consistente.
    function fnjs_normalizar_url(url) {
        if (!url) return url;
        try {
            var parser = new URL(url, window.location.origin);
            var params = new URLSearchParams(parser.search);
            params.sort();
            var normalized_url = parser.pathname + (params.toString() ? '?' + params.toString() : '');
            return normalized_url;
        } catch (e) {
            // Fallback manual si falla (quitar protocolo/host)
            var normalized = url.replace(/^https?:\/\/[^\/]+/, '');
            // Intentar ordenar parámetros manualmente si hay '?'
            if (normalized.indexOf('?') !== -1) {
                var parts = normalized.split('?');
                var p = parts[1].split('&').sort().join('&');
                normalized = parts[0] + (p ? '?' + p : '');
            }
            return normalized;
        }
    }

    function fnjs_collect_sel_from_checked_inputs(container) {
        var sel = [];
        container.find('input.sel:checked').each(function() {
            var v = $(this).val();
            if (v) {
                sel.push(v);
            }
        });
        return sel;
    }

    function fnjs_parse_slick_sel_value(raw) {
        if (raw === undefined || raw === null) {
            return '';
        }
        var val = raw.toString();
        if (val.indexOf('checked#') === 0) {
            return val.split('#').slice(1).join('#');
        }
        var pos = val.indexOf('#');
        if (pos !== -1) {
            return val.substring(pos + 1);
        }
        return val;
    }

    // Guarda el estado de la UI scroll y seleccion en sessionStorage.
    function fnjs_guardar_estado() {
        var base = $('#main').attr('refe');
        if (!base) {
            return;
        }
        base = fnjs_normalizar_url(base);

        $('input.scroll_id').each(function() {
            var tabla = $(this).attr('data-tabla');
            if (!tabla) return;
            var scroll_id = $(this).val();
            var sel = [];
            var key = 'state_' + base + '_' + tabla;
            var existing = null;
            try {
                var prev = sessionStorage.getItem(key);
                if (prev) {
                    existing = JSON.parse(prev);
                }
            } catch (e) {
                existing = null;
            }

            if (typeof window['grid_' + tabla] !== 'undefined' && typeof window['dataView_' + tabla] !== 'undefined') {
                var grid = window['grid_' + tabla];
                var dataView = window['dataView_' + tabla];
                var selectedIndices = grid.getSelectedRows();
                selectedIndices.forEach(function(idx) {
                    var item = dataView.getItem(idx);
                    var id = fnjs_parse_slick_sel_value(item ? item.sel : '');
                    if (id) {
                        sel.push(id);
                    }
                });
                if (sel.length === 0) {
                    var gridRoot = $('#grid_' + tabla).closest('form');
                    if (!gridRoot.length) {
                        gridRoot = $('#grid_' + tabla).parent();
                    }
                    sel = fnjs_collect_sel_from_checked_inputs(gridRoot);
                }
            } else {
                var tableEl = $('#tabla_' + tabla);
                if (!tableEl.length) tableEl = $('#' + tabla);
                sel = fnjs_collect_sel_from_checked_inputs(tableEl);
            }

            if (sel.length === 0) {
                var $gridForm = $('#grid_' + tabla).closest('form');
                if ($gridForm.length) {
                    var hidSel = $gridForm.find('input[name="id_sel"]').val() || '';
                    if (hidSel) {
                        sel = [hidSel];
                    }
                }
            }

            if (sel.length === 0 && existing && existing.sel && existing.sel.length > 0) {
                sel = existing.sel;
            }
            if ((scroll_id == 0 || scroll_id === '' || scroll_id === '0') && existing && existing.scroll_id) {
                scroll_id = existing.scroll_id;
            }

            var search_string = '';
            var searchInput = $('#txtSearch_' + tabla);
            var hasSearchInput = searchInput.length > 0;
            if (hasSearchInput) {
                search_string = searchInput.val() || '';
            }

            if (scroll_id > 0 || sel.length > 0 || search_string !== '' || hasSearchInput) {
                sessionStorage.setItem(key, JSON.stringify({
                    scroll_id: scroll_id,
                    sel: sel,
                    search_string: search_string,
                    timestamp: new Date().getTime()
                }));
            }
        });
    }

    // Recupera el estado de la UI para una URL dada.
    function fnjs_recuperar_estado(url, tabla) {
        url = fnjs_normalizar_url(url);
        var key = 'state_' + url + (tabla ? '_' + tabla : '');
        var sState = sessionStorage.getItem(key);
        if (!sState) {
            return null;
        }
        var state = JSON.parse(sState);
        // Limpiar si es muy antiguo (p.ej. > 1 hora)
        if (new Date().getTime() - state.timestamp > 3600000) {
            sessionStorage.removeItem(key);
            return null;
        }

        // SOLO recuperar si existe la marca temporal de 'is_back_navigation'
        var isBack = sessionStorage.getItem('is_back_navigation');
        if (isBack !== 'true') {
            return null;
        } else {
            return state;
        }
    }

    function fnjs_slick_col_visible() {
        // columnas visibles
        var colsVisible = {};
        var ci = 0;
        var v = "true";
        $(".slick-header-columns .slick-column-name").each(function (i) {
            ci++;
            // para saber el nombre
            var name = $(this).text();
            // quito posibles espacios en el índice
            var name_idx = name.replace(/ /g, '');
            colsVisible[name_idx] = v;
        });
        if (ci === 0) {
            colsVisible = 'noCambia';
        }
        return colsVisible;
    }

    function fnjs_slick_search_panel(tabla) {
        // panel de búsqueda
        if ($("#inlineFilterPanel_" + tabla).is(":visible")) {
            return "si";
        } else {
            return "no";
        }
    }

    function fnjs_slick_cols_width(tabla) {
        // anchura de las columnas
        var colsWidth = {};
        $("#grid_" + tabla + " .slick-header-column").each(function (i) {
            var wid = $(this).css('width');
            var regExp = /(\d*)(px)*/;
            var match = regExp.exec(wid);
            var w = 0;
            if (match != null) {
                w = match[1];
                if (w === undefined) {
                    w = 0;
                }
            }
            // para saber el nombre
            var name = $(this).children(".slick-column-name").text();
            // quito posibles espacios en el índice
            var name_idx = name.replace(/ /g, '');
            colsWidth[name_idx] = w;
        });
        return colsWidth;
    }

    function fnjs_slick_grid_width(tabla) {
        // anchura de toda la grid
        var widthGrid = '';
        var styl = $('#grid_' + tabla).attr('style');
        var match = /(^|\s)width:\s*(\d*)(\.)?(.*)px;/i.exec(styl);
        if (match != null) {
            var w = match[2];
            if (w !== undefined) {
                widthGrid = w;
            }
        }
        return widthGrid;
    }

    function fnjs_slick_grid_height(tabla) {
        // altura de toda la grid
        var heightGrid = '';
        var styl = $('#grid_' + tabla).attr('style');
        var match = /(^|\s)height:\s*(\d*)(\.)?(.*)px;/i.exec(styl);
        if (match != null) {
            var h = match[2];
            if (h !== undefined) {
                heightGrid = h;
            }
        }
        return heightGrid;
    }

    function fnjs_slick_col_order(tabla) {
        // orden actual de las columnas visibles
        var colOrder = [];
        $("#grid_" + tabla + " .slick-header-column").each(function () {
            var name = $(this).children(".slick-column-name").text();
            var name_idx = name.replace(/ /g, '');
            if (name_idx !== '') {
                colOrder.push(name_idx);
            }
        });
        return colOrder;
    }

    function fnjs_def_tabla(tabla) {
        // si es la tabla por defecto, no puedo guardar las preferencias.
        if (tabla === 'uno') {
            alert(<?= json_encode(_("no puedo grabar las preferencias de la tabla. No puede tener el nombre por defecto")) ?> + ': ' + tabla);
            return;
        }

        var panelVis = fnjs_slick_search_panel(tabla);
        var colsVisible = fnjs_slick_col_visible();
        var colsWidth = fnjs_slick_cols_width(tabla);
        var colsOrder = fnjs_slick_col_order(tabla);
        var widthGrid = fnjs_slick_grid_width(tabla);
        var heightGrid = fnjs_slick_grid_height(tabla);

        var oPrefs = {
            "panelVis": panelVis,
            "colVisible": colsVisible,
            "colWidths": colsWidth,
            "colOrder": colsOrder,
            "widthGrid": widthGrid,
            "heightGrid": heightGrid
        };
        var sPrefs = JSON.stringify(oPrefs);
        // Misma URL base que {@see index.php} al construir HashFront para preferencias_guardar (FastRoute).
        var url = "<?= AppUrlConfig::getApiBaseUrl() ?>/src/usuarios/preferencias_guardar";
        var parametros = 'que=slickGrid&tabla=' + tabla + '&sPrefs=' + encodeURIComponent(sPrefs) + '<?= $h ?>';
        $.ajax({
            url: url,
            type: 'post',
            data: parametros,
            complete: function (rta) {
                var rta_txt = rta.responseText;
                if (!rta_txt || rta_txt === '\n') {
                    return;
                }
                try {
                    var j = JSON.parse(rta_txt);
                    if (j.success === false && j.mensaje) {
                        alert(j.mensaje);
                    }
                } catch (e) {
                    if (/<!DOCTYPE/i.test(rta_txt) || /<html[\s>]/i.test(rta_txt)) {
                        alert(<?= json_encode(_("No se han podido guardar las preferencias: respuesta inválida del servidor.")) ?>);
                        return;
                    }
                    alert(rta_txt);
                }
            }
        });
    }

    function fnjs_logout() {
        var parametros = 'logout=si&PHPSESSID=<?= session_id(); ?>';
        var path = window.location.pathname;

        if (path.endsWith('/index.php')) {
            top.location.href = path + '?' + parametros;
            return;
        }

        if (!path.endsWith('/')) {
            path += '/';
        }

        top.location.href = path + 'index.php?' + parametros;
    }

    var _orbixAuthRedirectPending = false;

    function fnjs_redirect_a_login() {
        if (_orbixAuthRedirectPending) {
            return;
        }
        _orbixAuthRedirectPending = true;

        var path = window.location.pathname;
        var target;

        if (path.endsWith('/index.php')) {
            target = path;
        } else {
            if (!path.endsWith('/')) {
                path += '/';
            }
            target = path + 'index.php';
        }

        try {
            document.documentElement.innerHTML = '';
        } catch (e) {
        }

        window.top.location.replace(target);
    }

    function fnjs_es_resposta_login(html) {
        if (!html) {
            return false;
        }
        return /id=["']frm_login["']/.test(html) || /form-signin/.test(html);
    }

    function fnjs_es_json_auth_required(html) {
        if (!html || html.charAt(0) !== '{') {
            return false;
        }
        try {
            var j = JSON.parse(html);
            if (!j || j.success !== false) {
                return false;
            }
            var data = j.data;
            if (typeof data === 'string' && data !== '') {
                try {
                    data = JSON.parse(data);
                } catch (e2) {
                }
            }
            return !!(data && data.code === 'auth_required');
        } catch (e) {
            return false;
        }
    }

    function fnjs_resposta_requiere_login(respuesta, html) {
        if (typeof respuesta === 'object' && respuesta && typeof respuesta.getResponseHeader === 'function') {
            if (respuesta.getResponseHeader('X-Orbix-Auth-Required')) {
                return true;
            }
        }
        if (fnjs_es_json_auth_required(html)) {
            return true;
        }
        return fnjs_es_resposta_login(html);
    }

    function fnjs_comprobar_respuesta_ajax_login(xhr, data) {
        if (_orbixAuthRedirectPending) {
            return true;
        }
        var html = typeof data === 'string' ? data : (xhr && xhr.responseText ? xhr.responseText : '');
        if (fnjs_resposta_requiere_login(xhr, html)) {
            fnjs_redirect_a_login();
            return true;
        }
        return false;
    }

    (function fnjs_instalar_interceptor_ajax_login() {
        if (typeof $ === 'undefined' || !$.ajaxPrefilter) {
            return;
        }

        $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
            jqXHR.done(function (data, textStatus, xhr) {
                fnjs_comprobar_respuesta_ajax_login(xhr || jqXHR, data);
            });

            var originalComplete = options.complete;
            options.complete = function (xhr, status) {
                if (fnjs_comprobar_respuesta_ajax_login(xhr, xhr.responseText)) {
                    return;
                }
                if (originalComplete) {
                    originalComplete.apply(this, arguments);
                }
            };

            var originalSuccess = options.success;
            if (originalSuccess) {
                options.success = function (data, textStatus, xhr) {
                    if (fnjs_comprobar_respuesta_ajax_login(xhr, data)) {
                        return;
                    }
                    originalSuccess.apply(this, arguments);
                };
            }
        });
    })();

    function fnjs_windowopen(url) { //para poder hacerlo por el menu
        var parametros = '';
        window.open(url + '?' + parametros);
    }

    function fnjs_link_menu(id_grupmenu) {
        var parametros = 'id_grupmenu=' + id_grupmenu + '&PHPSESSID=<?= session_id() ?>';

        if (id_grupmenu === 'web_externa') {
            top.location.href = 'http://www/exterior/cl/index.html';
        } else {
            top.location.href = 'index.php?' + parametros;
        }
    }

    function fnjs_link_submenu(url, parametros) {
        if (parametros) {
            parametros = parametros + '&nav=reset&PHPSESSID=<?= session_id() ?>';
        } else {
            parametros = 'nav=reset&PHPSESSID=<?= session_id() ?>';
        }
        if (!url) return false;
        // para el caso de editar webs
        if (url === "<?= ConfigGlobal::getWeb() ?>/programas/pag_html_editar.php") {
            window.open(url + '?' + parametros);
        } else {
            fnjs_guardar_estado();
            $('#main').attr('refe', url);
            $.ajax({
                url: url,
                type: 'post',
                data: parametros,
                complete: function (respuesta) {
                    fnjs_mostra_resposta(respuesta, '#main');
                },
                error: fnjs_procesarError
            });
        }
    }

    function fnjs_procesarError() {
        alert(<?= json_encode(_("Error de página devuelta")) ?>);
    }

    /** Oculta la capa modal (#overlay) que bloquea clics en toda la ventana (p. ej. panel izquierdo). */
    function fnjs_overlay_ocultar() {
        var el = document.getElementById('overlay');
        if (el) {
            el.style.display = 'none';
        }
        $('#overlay').hide();
    }

    /** Cierra ventana popup (#div_modificar) y su capa oscura. */
    function fnjs_cerrar_ventana_modal() {
        $('#div_modificar').html('');
        $('#div_modificar').css({width: '0', height: '0'});
        $('#div_modificar').removeClass('ventana');
        fnjs_overlay_ocultar();
        fnjs_actualizar_left_slide_segun_nav('#main');
    }

    function fnjs_normalizar_bloque(bloque) {
        if (!bloque) {
            return '#main';
        }
        bloque = String(bloque).trim();
        while (bloque.charAt(0) === '#') {
            bloque = bloque.substring(1);
        }
        return bloque === '' ? '#main' : '#' + bloque;
    }

    function fnjs_nav_atras_markers_en(bloque) {
        bloque = fnjs_normalizar_bloque(bloque);
        var $bloque = $(bloque);
        if (!$bloque.length) {
            return $();
        }
        return $bloque.find('[data-nav-atras]').add($bloque.filter('[data-nav-atras]'));
    }

    function fnjs_nav_atras_steps_from_context(bloque) {
        bloque = fnjs_normalizar_bloque(bloque || '#main');
        var $markers = fnjs_nav_atras_markers_en(bloque);
        if (!$markers.length && bloque === '#main') {
            $markers = $('#main [data-nav-atras]');
        }
        if (!$markers.length) {
            return 0;
        }
        var n = parseInt($markers.last().attr('data-nav-atras'), 10);
        return (n >= 1) ? n : 1;
    }

    function fnjs_bloque_tiene_nav_atras(bloque) {
        return fnjs_nav_atras_steps_from_context(bloque) > 0;
    }

    function fnjs_actualizar_left_slide_segun_nav(bloque) {
        if (fnjs_bloque_tiene_nav_atras(bloque)) {
            fnjs_left_side_show();
        } else {
            fnjs_left_side_hide();
        }
    }

    /** @deprecated NavStack v2 — no-op; compatibilidad con vistas que aún lo invocan. */
    function fnjs_borrar_posibles_atras() {
    }

    function fnjs_left_slide_atras() {
        fnjs_cerrar_ventana_modal();
        var n = fnjs_nav_atras_steps_from_context('#main');
        if (n < 1) {
            return false;
        }
        return fnjs_nav_atras(n);
    }

    function fnjs_nav_atras(n) {
        n = (typeof n === 'number' && n >= 1) ? n : 1;
        fnjs_guardar_estado();
        sessionStorage.setItem('is_back_navigation', 'true');
        fnjs_cerrar_ventana_modal();
        fnjs_left_side_hide();

        var parametros = '<?= $navAtrasSignedParams ?>&n=' + n + '&PHPSESSID=<?= session_id() ?>';
        $.ajax({
            url: '<?= $navAtrasUrl ?>',
            type: 'post',
            data: parametros,
            dataType: 'json',
            complete: function (respuesta) {
                var payload = respuesta.responseJSON;
                if (!payload || payload.url === null || payload.url === '') {
                    return false;
                }
                var bloque = fnjs_normalizar_bloque(payload.bloque || '#main');
                $(bloque).attr('refe', payload.url);
                $.ajax({
                    url: payload.url,
                    type: 'post',
                    data: payload.parametros,
                    complete: function (resposta) {
                        fnjs_mostra_resposta(resposta, bloque);
                    },
                    error: fnjs_procesarError
                });
            },
            error: fnjs_procesarError
        });
        return false;
    }

    var fnjs_nav_state_timer = null;
    var fnjs_nav_state_pending = null;

    function fnjs_nav_state_send(patch) {
        if (!patch || typeof patch !== 'object') {
            return;
        }
        var parametros = '<?= $navStateSignedParams ?>&nav_patch='
            + encodeURIComponent(JSON.stringify(patch))
            + '&PHPSESSID=<?= session_id() ?>';
        $.ajax({
            url: '<?= $navStateUrl ?>',
            type: 'post',
            data: parametros,
            dataType: 'json',
            async: false,
            error: fnjs_procesarError
        });
    }

    function fnjs_nav_state_patch(patch) {
        if (!patch || typeof patch !== 'object') {
            return;
        }
        fnjs_nav_state_pending = patch;
        clearTimeout(fnjs_nav_state_timer);
        fnjs_nav_state_timer = setTimeout(function () {
            fnjs_nav_state_send(fnjs_nav_state_pending);
            fnjs_nav_state_pending = null;
        }, 500);
    }

    function fnjs_nav_state_flush() {
        clearTimeout(fnjs_nav_state_timer);
        fnjs_nav_state_timer = null;
        if (!fnjs_nav_state_pending) {
            return;
        }
        var patch = fnjs_nav_state_pending;
        fnjs_nav_state_pending = null;
        fnjs_nav_state_send(patch);
    }

    function fnjs_nav_state_patch_form_selection(formSelector, scrollInputSelector, gridGlobalName) {
        if (typeof fnjs_nav_state_patch !== 'function') {
            return;
        }
        var $form = $(formSelector);
        if (!$form.length) {
            return;
        }
        if (typeof fnjs_sync_grid_sel_checkboxes === 'function') {
            fnjs_sync_grid_sel_checkboxes(formSelector);
        }
        var idSel = $form.find('input.sel:checked').first().val() || '';
        var scrollId = scrollInputSelector ? ($(scrollInputSelector).val() || '0') : '0';
        if (!idSel && gridGlobalName) {
            var grid = window[gridGlobalName];
            var dataView = window['dataView_' + String(gridGlobalName).replace(/^grid_/, '')];
            if (grid && dataView) {
                var selected = grid.getSelectedRows();
                if (selected && selected.length) {
                    if (scrollInputSelector) {
                        $(scrollInputSelector).val(selected[0]);
                        scrollId = String(selected[0]);
                    }
                    var item = dataView.getItem(selected[0]);
                    if (typeof fnjs_parse_slick_sel_value === 'function') {
                        idSel = fnjs_parse_slick_sel_value(item ? item.sel : '');
                    }
                }
            }
        }
        if (!idSel) {
            idSel = $form.find('input[name="id_sel"]').val() || '';
        }
        if (idSel !== '') {
            $form.find('input[name="id_sel"]').val(idSel);
        }
        fnjs_nav_state_patch({ id_sel: idSel, scroll_id: scrollId });
    }

    function fnjs_actividad_select_nav_state_hook() {
        if (typeof grid_actividad_select === 'undefined') {
            setTimeout(fnjs_actividad_select_nav_state_hook, 100);
            return;
        }
        var patch = function () {
            fnjs_nav_state_patch_form_selection('#seleccionados', '#scroll_id_actividad_select', 'grid_actividad_select');
        };
        grid_actividad_select.onSelectedRowsChanged.subscribe(function () {
            patch();
        });
        grid_actividad_select.onClick.subscribe(function () {
            patch();
        });
    }

    function fnjs_cambiar_link(id_div) {
        if (fnjs_bloque_tiene_nav_atras(id_div)) {
            fnjs_left_side_show();
            return true;
        }
        fnjs_left_side_hide();
        var base = $(id_div).attr('refe');
        if (base) {
            var selector = id_div + " a[href]";
            $(selector).each(function (i) {
                var aa = this.href;
                // si tiene una ref a name(#):
                if (aa !== undefined && aa.indexOf("#") !== -1) {
                    var part = aa.split("#");
                    this.href = "";
                    $(this).attr("onclick", "location.hash = '#" + part[1] + "'; return false;");
                } else {
                    var url = fnjs_ref_absoluta(base, aa);
                    var path = aa.replace(/[\?#].*$/, ''); // borro desde el "?" o el "#".
                    var extension = path.substr(-4);
                    if (extension === ".php" || extension === "html" || extension === ".htm") { // documento web
                        this.href = "";
                        $(this).attr("onclick", "fnjs_update_div('" + id_div + "','" + url + "'); return false;");
                    } else {
                        this.href = url;
                    }
                }
            });
        }
    }

    function fnjs_cambiar_base_link() {
        if ($('#main_oficina').length) {
            fnjs_cambiar_link('#main_oficina');
        }
        if ($('#main_todos').length) {
            fnjs_cambiar_link('#main_todos');
        }
        if ($('#main').length) {
            fnjs_cambiar_link('#main');
        }
    }

    function fnjs_update_div(bloque, ref, mantener_atras = 0) {
        bloque = fnjs_normalizar_bloque(bloque);
        if (bloque === '#main') {
            fnjs_guardar_estado();
        }
        var path = ref.replace(/\?.*$/, '');
        var parametros = '';
        var pattern = /\?/;
        if (pattern.test(ref)) {
            parametros = ref.replace(/^[^\?]*\?/, '');
            parametros = parametros + '&PHPSESSID=<?= session_id() ?>';
        } else {
            parametros = 'PHPSESSID=<?= session_id() ?>';
        }
        $(bloque).attr('refe', path);
        $.ajax({
            url: path,
            type: 'post',
            data: parametros,
            complete: function (respuesta) {
                fnjs_mostra_resposta(respuesta, bloque);
            }
        });
        return false;
    }

    function fnjs_ref_absoluta(base, path) {
        var url = "";
        var inicio = "";
        var secure = <?php echo !empty($_SERVER["HTTPS"]) ? 1 : 0; ?>;
        var protocol = secure ? 'https:' : 'http:';

        // El apache ya ha añadido por su cuenta protocolo+$web. Lo quito:
        var ini = protocol + '<?= ConfigGlobal::getWeb() ?>';
        if (path.indexOf(ini) !== -1) {
            path = path.replace(ini, '');
        } else { // caso especial: http://www/exterior
            ini = protocol + '//www/exterior';
            if (path.indexOf(ini) !== -1) {
                return path;
            } else {
                ini = protocol + '<?= ConfigGlobal::getWeb() ?>';
                if (path.indexOf(ini) !== -1) {
                    path = path.replace(ini, '');
                } else {
                    if (path.match(/^http/)) {
                        return path;
                    }
                }
            }
        }

        if (base.match(/^<?= addcslashes(ConfigGlobal::$directorio, "/") ?>/)) {
            base = base.replace('<?= ConfigGlobal::$directorio ?>', '');
            inicio = protocol + '<?= ConfigGlobal::getWeb() ?>';
        } else if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_fotos, "/") ?>/)) {
            base = base.replace('<?= ConfigGlobal::$dir_fotos ?>', '');
            inicio = protocol + '<?= ConfigGlobal::$web_fotos ?>';
        } else if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_oficinas, "/") ?>/)) {
            base = base.replace('<?= ConfigGlobal::$dir_oficinas ?>', '');
            inicio = protocol + '<?= ConfigGlobal::$web_oficinas ?>';
        } else if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_web, "/") ?>/)) {
            base = base.replace('<?= ConfigGlobal::$dir_web ?>', '');
            inicio = protocol + '<?= ConfigGlobal::getWeb() ?>';
        }

        if (!inicio && base.indexOf(protocol) != -1) {
            base = base.replace(protocol, '');
            inicio = protocol;
        }

        // le quito la página final (si tiene) y la barra (/)
        base = base.replace(/\/(\w+\.\w+$)|\/((\w+-)*(\w+ )*\w+\.\w+$)/, '');
        // elimino la base si ya existe en el path:
        path = path.replace(base, '');

        if (!inicio) {
            url = path;
        } else {
            url = inicio + base + path;
        }
        return url;
    }

    function fnjs_dossiers_refresh(formulario, bloque) {
        if (!bloque) {
            bloque = '#main';
        }
        var $form = $(formulario);
        $form.find('input[name="mod"]').val('');
        $form.find('input[name="refresh"]').remove();
        $form.append('<input type="hidden" name="refresh" value="1">');
        $form.attr('action', 'frontend/dossiers/controller/dossiers_ver.php');
        // Recarga in-place (refresh=1): el servidor no apunta en NavStack (legacy pararRecordar).
        fnjs_enviar_formulario(formulario, bloque);
    }

    function fnjs_enviar_formulario(id_form, bloque) {
        if (!bloque) {
            bloque = '#main';
        }
        if (bloque === '#main') {
            fnjs_guardar_estado();
        }
        $(id_form).one("submit", function () {
            var tgt_url = $(this).attr('action');
            if (tgt_url && bloque === '#main') {
                var path = tgt_url.replace(/\?.*$/, '');
                $(bloque).attr('refe', path);
            }
            $.ajax({
                data: $(this).serialize(),
                type: 'post',
                url: tgt_url,
                success: function (respuesta) {
                    fnjs_mostra_resposta(respuesta, bloque);
                }
            });
            return false;
        });
        $(id_form).trigger("submit");
        $(id_form).off();
    }

    function fnjs_enviar(evt, objeto) {
        var frm = objeto.id;
        if (evt.keyCode === 13 && evt.type === "keydown") {
            var b = $('#' + frm + ' input.btn_ok');
            if (b[0]) {
                b[0].onclick();
            }
            evt.preventDefault();
            evt.stopPropagation();
            return false;
        }
    }

    /**
     * Tras cargar vistas AJAX que reincluyen slick.core.js (p. ej. misas), window.Slick se
     * sustituye y pierde Slick.Data aunque index.php ya hubiera cargado slick.dataview.js.
     */
    function fnjs_slickListaDepsReady() {
        return !!(window.Slick
            && Slick.Data && Slick.Data.DataView
            && Slick.RowSelectionModel
            && Slick.AutoTooltips
            && Slick.Controls && Slick.Controls.Pager && Slick.Controls.ColumnPicker);
    }

    function fnjs_loadScriptFresh(src) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            s.async = false;
            s.src = src + (src.indexOf('?') >= 0 ? '&' : '?') + '_orbix=' + Date.now();
            s.onload = function () { resolve(); };
            s.onerror = function () { reject(new Error('No se pudo cargar ' + src)); };
            document.head.appendChild(s);
        });
    }

    function fnjs_ensureSlickLista(ready) {
        if (fnjs_slickListaDepsReady()) {
            ready();
            return;
        }
        var slickBase = <?= json_encode(rtrim(ConfigGlobal::getWeb_NodeScripts(), '/') . '/slickgrid/dist/browser') ?>;
        var autosizeUrl = <?= json_encode(rtrim(ConfigGlobal::getWeb_scripts(), '/') . '/slickgrid-orbix/slick-grid-autosize.js') ?>;
        var urls = [
            slickBase + '/slick.dataview.js',
            slickBase + '/plugins/slick.autotooltips.js',
            slickBase + '/plugins/slick.rowselectionmodel.js',
            slickBase + '/controls/slick.pager.js',
            slickBase + '/controls/slick.columnpicker.js',
            autosizeUrl
        ];
        var chain = Promise.resolve();
        urls.forEach(function (url) {
            chain = chain.then(function () { return fnjs_loadScriptFresh(url); });
        });
        chain.then(function () { ready(); }).catch(function (err) {
            console.error(err);
            ready();
        });
    }

    /**
     * Decodifica HTML enviado como base64 UTF-8 desde `ajax_json_html` (PHP).
     */
    function fnjs_decode_html_b64(b64) {
        if (!b64) {
            return '';
        }
        try {
            var bin = atob(String(b64));
            if (typeof TextDecoder !== 'undefined') {
                var bytes = new Uint8Array(bin.length);
                for (var i = 0; i < bin.length; i++) {
                    bytes[i] = bin.charCodeAt(i);
                }
                return new TextDecoder('utf-8').decode(bytes);
            }
            return decodeURIComponent(escape(bin));
        } catch (e) {
            return '';
        }
    }

    /**
     * Obtiene HTML de `data` del envelope (html plano o html_b64).
     */
    function fnjs_html_from_ajax_data(data) {
        if (!data || typeof data !== 'object') {
            return '';
        }
        if (data.html_b64) {
            return fnjs_decode_html_b64(data.html_b64);
        }
        if (data.html !== undefined && data.html !== null) {
            return String(data.html);
        }
        return '';
    }

    /**
     * Normaliza `data` del envelope para que `data.html` exista tras decodificar html_b64.
     */
    function fnjs_normalize_ajax_html_data(data) {
        if (!data || typeof data !== 'object') {
            return data;
        }
        if (data.html_b64 && !data.html) {
            data.html = fnjs_html_from_ajax_data(data);
        }
        return data;
    }

    /**
     * Extrae HTML (o texto) de una respuesta AJAX en envelope ContestarJson o HTML plano.
     */
    function fnjs_extract_html_from_ajax_body(text) {
        if (!text || typeof text !== 'string') {
            return '';
        }
        var trimmed = text.trim();
        if (trimmed.charAt(0) !== '{') {
            return trimmed;
        }
        try {
            var json = JSON.parse(trimmed);
            if (json && json.success === false) {
                if (json.mensaje) {
                    alert(json.mensaje);
                }
                return '';
            }
            if (!json || json.success !== true) {
                return trimmed;
            }
            var data = json.data;
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    return data;
                }
            }
            if (data && typeof data === 'object') {
                data = fnjs_normalize_ajax_html_data(data);
                if (data.html !== undefined && data.html !== null) {
                    return String(data.html);
                }
                if (data.text !== undefined && data.text !== null) {
                    return String(data.text);
                }
            }
            return trimmed;
        } catch (e) {
            return trimmed;
        }
    }

    /**
     * Parsea el envelope `{success, mensaje?, data}` de ContestarJson.
     * Semántica de `data` alineada con PostRequest::envelopeDataFieldToArray
     * (ver frontend/shared/PostRequest.php y frontend/agents.md).
     * Para mutaciones con ack `"ok"`, preferir comprobar solo `rta.success`.
     * @returns {object|null}
     */
    function fnjs_parse_rta(rta, errorPrefix) {
        if (!rta || rta.success !== true) {
            var msg = (rta && rta.mensaje) ? rta.mensaje : 'Error desconocido';
            alert((errorPrefix || '') + msg);
            return null;
        }
        try {
            var data = rta.data;
            if (typeof data === 'string') {
                var trimmed = data.trim();
                if (trimmed.charAt(0) === '{' || trimmed.charAt(0) === '[') {
                    data = JSON.parse(data);
                } else if (data === 'ok' || data === '') {
                    // ContestarJson::enviar() ack sin payload JSON anidado.
                    data = {};
                }
            }
            return fnjs_normalize_ajax_html_data(data);
        } catch (e) {
            return null;
        }
    }

    /**
     * AJAX POST estándar con respuesta JSON (ContestarJson).
     */
    function fnjs_ajax_json(options) {
        var settings = $.extend({ type: 'post', dataType: 'json' }, options || {});
        return $.ajax(settings)
            .done(function (rta) {
                if (typeof settings.onSuccess === 'function') {
                    var data = fnjs_parse_rta(rta, settings.errorPrefix || '');
                    if (data !== null) {
                        settings.onSuccess(data, rta);
                    }
                    return;
                }
                if (typeof settings.onData === 'function') {
                    var parsed = fnjs_parse_rta(rta, settings.errorPrefix || '');
                    if (parsed !== null) {
                        settings.onData(parsed, rta);
                    }
                }
            })
            .fail(function (xhr, status, error) {
                if (typeof settings.onFail === 'function') {
                    settings.onFail(xhr, status, error);
                    return;
                }
                alert("<?= _("Error en la conexión con el servidor") ?>".': ' + error);
            });
    }

    /**
     * AJAX que inyecta HTML en un selector (respuesta JSON ContestarJson o HTML plano).
     * Usa `dataType: 'text'` para soportar fragmentos grandes sin límites de `atob`/doble parse.
     */
    function fnjs_ajax_json_html(options) {
        var target = options.target;
        return $.ajax({
            url: options.url,
            type: options.type || 'post',
            data: options.data,
            dataType: 'text'
        }).done(function (text) {
            var html = fnjs_extract_html_from_ajax_body(text);
            if (target) {
                $(target).html(html);
                if (html) {
                    fnjs_ventana_ajustar(target);
                }
            }
            if (typeof options.onDone === 'function') {
                options.onDone({ html: html });
            }
        }).fail(function (xhr, status, error) {
            if (typeof options.onFail === 'function') {
                options.onFail(xhr, status, error);
                return;
            }
            alert("<?= _("Error en la conexión con el servidor") ?>".': ' + error);
        });
    }

    /**
     * AJAX JSON que muestra `data.text` o `data.mensaje` en un alert.
     * Si no hay mensaje en la respuesta y `successMessage` está definido, lo muestra en éxito.
     */
    function fnjs_ajax_json_alert(options) {
        return fnjs_ajax_json({
            url: options.url,
            type: options.type || 'post',
            data: options.data,
            errorPrefix: options.errorPrefix,
            onSuccess: function (data) {
                var txt = data.text || data.mensaje || '';
                if (txt !== '') {
                    alert(txt);
                } else if (options.successMessage) {
                    alert(options.successMessage);
                }
                if (typeof options.onDone === 'function') {
                    options.onDone(data);
                }
            },
            onFail: options.onFail
        });
    }

    /*
     * Ajusta un popup con clase .ventana al contenido recién cargado:
     * tamaño automático, recentrado y arrastrable.
     * Se pasa de transform (centrado CSS inicial) a left/top explícitos
     * porque jQuery UI draggable no convive bien con translate(-50%,-50%).
     */
    function fnjs_ventana_ajustar(bloque) {
        var $ventana = $(bloque);
        if (!$ventana.hasClass('ventana')) {
            return;
        }
        $ventana.css({width: 'auto', height: 'auto'});
        $ventana.css({
            left: Math.max(($(window).width() - $ventana.outerWidth()) / 2, 0) + 'px',
            top: Math.max(($(window).height() - $ventana.outerHeight()) / 2, 0) + 'px',
            transform: 'none'
        });
        if ($.fn.draggable) {
            $ventana.draggable({containment: 'window', cursor: 'move'});
        }
    }

    function fnjs_mostra_resposta(respuesta, bloque) {
        if (_orbixAuthRedirectPending) {
            return;
        }
        var myText = '';
        switch (typeof respuesta) {
            case 'object':
                myText = respuesta.responseText;
                break;
            case 'string':
                myText = respuesta.trim();
                break;
        }
        if (fnjs_comprobar_respuesta_ajax_login(respuesta, myText)) {
            return;
        }
        myText = fnjs_extract_html_from_ajax_body(myText);
        if (myText === '') {
            return;
        }
        $(bloque).empty().append(myText);
        if (bloque === '#main') {
            fnjs_cerrar_ventana_modal();
            if (typeof legacyAfterMainNavigation === 'function') {
                legacyAfterMainNavigation();
            }
        }
        fnjs_ventana_ajustar(bloque);
        fnjs_cambiar_link(bloque);
        // Destacar filas seleccionadas inicialmente en tablas HTML
        $(bloque).find('table input.sel:checked').closest('tr').addClass('selected_row');
        if (bloque === '#main' && typeof legacyAfterMainNavigation !== 'function') {
            // Tras AJAX en #main, UDM puede quedar con um.tr/um.n incoherentes; refrescar evita errores en consola (cck/contains).
            setTimeout(function () {
                try {
                    if (typeof window.um !== 'undefined' && window.um.tr && typeof window.um.refresh === 'function' && document.getElementById('udm')) {
                        window.um.refresh(0);
                    }
                } catch (e) {
                }
            }, 0);
        }
        if (bloque === '#main') {
            setTimeout(function () {
                sessionStorage.removeItem('is_back_navigation');
            }, 1000);
        }
    }

    function XMLtoString(elem) {
        var serialized;
        try {
            var serializer = new XMLSerializer();
            serialized = serializer.serializeToString(elem);
        } catch (e) {
            serialized = elem.xml;
        }
        return serialized;
    }

    function DOMtoString(doc) {
        var serializer = new XMLSerializer();
        serializer.asDOMSerializer();
        serializer.serialize(doc);
        console.log(serializer.toString());
    }

    // Estas variables han de ser globales, y las utiliza el dhtmlxScheduler (dibujar calendarios).
    var _isFF = false;
    var _isIE = false;
    var _isOpera = false;
    var _isKHTML = false;
    var _isMacOS = false;
    var _isChrome = false;

    $('<style>tr.selected_row {background-color: #ffffcc !important;} tr[onclick] {cursor: pointer;}</style>').appendTo('head');

    function fnjs_clic_fila(row, event) {
        var rowData = $(row).data('json');
        $(row).closest('table').trigger('rowSelected', [rowData]);

        if ($(event.target).is('a, span.link, input[type=checkbox], input:button')) {
            return;
        }
        var $chk = $(row).find('input.sel');
        if ($chk.length) {
            $chk.prop('checked', !$chk.prop('checked')).trigger('change');
        } else {
            $(row).siblings().removeClass('selected_row');
            $(row).addClass('selected_row');
        }
    }

    $(document).on('change', 'table input.sel', function () {
        var $row = $(this).closest('tr');
        if ($(this).is(':checked')) {
            $row.addClass('selected_row');
        } else {
            $row.removeClass('selected_row');
        }
    });

    function fnjs_left_side_show() {
        if ($('#left_slide').length) {
            $('#left_slide').show();
        }
    }

    function fnjs_left_side_hide() {
        if ($('#left_slide').length) {
            $('#left_slide').hide();
        }
    }

    function fnjs_dani2() {
        $("#left_slide").hover(
            function () {
                $(this).animate({ height: '+=250' }, 'slow');
            },
            function () {
                $(this).animate({ height: '-=250px' }, 'slow');
            }
        );
    }

    function fnjs_restet_form() {
        $(this).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
    }

</script>
