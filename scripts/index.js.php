<?php
// JavaScript functions for index.php
// This file contains all the JavaScript functions that were previously embedded in index.php

// This file requires the $h variable to be defined before inclusion
use core\ConfigGlobal;if (!isset($h)) {
    $h = '';
}
?>
<script type="text/javascript">
    $(document).ready(function () {
    $('#cargando').hide();  // hide it initially
})
;
    $(document).ajaxStart(function () {
    $('#cargando').show();
})
;
    $(document).ajaxStop(function () {
    $('#cargando').hide();
})
;

    /**
    * Normaliza una URL para usarla como clave consistente.
    * Quita el protocolo, host y puerto si los hay.
    */
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

    /**
    * Guarda el estado de la UI (scroll y selección) en sessionStorage
    * keyed por la URL actual de la base del bloque (usualmente #main).
    */
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
        
        if (typeof window['grid_' + tabla] !== 'undefined' && typeof window['dataView_' + tabla] !== 'undefined') {
            var grid = window['grid_' + tabla];
            var dataView = window['dataView_' + tabla];
            var selectedIndices = grid.getSelectedRows();
            selectedIndices.forEach(function(idx) {
                var item = dataView.getItem(idx);
                if (item && item.sel !== undefined && item.sel !== null) {
                    var val = item.sel.toString();
                    var pos = val.indexOf('#');
                    if (pos !== -1) {
                        val = val.substring(pos + 1);
                    }
                    sel.push(val);
                }
            });
        } else {
            // HTML table: try both #tabla_T and #T
            var tableEl = $('#tabla_' + tabla);
            if (!tableEl.length) tableEl = $('#' + tabla);
            tableEl.find('input.sel:checked').each(function() {
                sel.push($(this).val());
            });
        }

        if (scroll_id > 0 || sel.length > 0) {
            var state = {
                scroll_id: scroll_id,
                sel: sel,
                timestamp: new Date().getTime()
            };
            var key = 'state_' + base + '_' + tabla;
            //console.log('Saving state to ' + key, state);
            sessionStorage.setItem(key, JSON.stringify(state));
        } else {
            //console.log('No state to save for ' + base + '_' + tabla);
        }
    });
}

    /**
    * Recupera el estado de la UI para una URL dada.
    */
    function fnjs_recuperar_estado(url, tabla) {
    url = fnjs_normalizar_url(url);
    var key = 'state_' + url + (tabla ? '_' + tabla : '');
    var sState = sessionStorage.getItem(key);
    if (!sState) {
    return null;
}
    var state = JSON.parse(sState);
    //console.log('State found in sessionStorage for ' + key, state);
    // Opcional: limpiar si es muy antiguo (p.ej. > 1 hora)
    if (new Date().getTime() - state.timestamp > 3600000) {
        //console.log('State is too old, ignoring.');
        sessionStorage.removeItem(key);
        return null;
    }

    // SOLO recuperar si existe la marca temporal de 'is_back_navigation'
    var isBack = sessionStorage.getItem('is_back_navigation');
    //console.log('is_back_navigation flag:', isBack);
    if (isBack !== 'true') {
        // NO restaurar porque venimos de una navegación 'hacia adelante'.
        //console.log('Not a back navigation, skipping restoration.');
        // No borramos el estado aquí para evitar interferencias encadenadas. Se sobreescribirá al salir.
        return null;
    } else {
        //console.log('Restoring state from sessionStorage.');
        // Restauramos. La marca 'is_back_navigation' la borra fnjs_mostra_resposta al terminar.
        return state;
    }
}

    function fnjs_slick_col_visible() {
    // columnas visibles
    colsVisible = {};
    ci = 0;
    v = "true";
    $(".slick-header-columns .slick-column-name").each(function (i) {
    ci++;
    // para saber el nombre
    name = $(this).text();
    // quito posibles espacios en el índice
    name_idx = name.replace(/ /g, '');
    //alert ("name: "+name+" vis: "+v);
    colsVisible[name_idx] = v;
});
    if (ci === 0) {
    colsVisible = 'noCambia';
}
    //alert (ci+'  cols: '+colsVisible);
    return colsVisible;
}

    function fnjs_slick_search_panel(tabla) {
    // panel de búsqueda
    if ($("#inlineFilterPanel_" + tabla).is(":visible")) {
    panelVis = "si";
} else {
    panelVis = "no";
}
    //alert (panelVis);
    return panelVis;
}

    function fnjs_slick_cols_width(tabla) {
    // anchura de las columnas
    colsWidth = {};
    $("#grid_" + tabla + " .slick-header-column").each(function (i) {
    //styl = $(this).attr("style");
    wid = $(this).css('width');
    //alert (wid);
    // quitar los 'px'
    //match = /width:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
    regExp = /(\d*)(px)*/;
    match = regExp.exec(wid);
    w = 0;
    if (match != null) {
    w = match[1];
    if (w === undefined) {
    w = 0;
}
}
    //alert (w);
    // para saber el nombre
    let name = $(this).children(".slick-column-name").text();
    // quito posibles espacios en el índice
    let name_idx = name.replace(/ /g, '');
    colsWidth[name_idx] = w;
});
    return colsWidth;
}

    function fnjs_slick_grid_width(tabla) {
    // anchura de toda la grid
    var widthGrid = '';
    styl = $('#grid_' + tabla).attr('style');
    match = /(^|\s)width:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
    if (match != null) {
    w = match[2];
    if (w !== undefined) {
    widthGrid = w;
}
}
    return widthGrid;
}

    function fnjs_slick_grid_height(tabla) {
    // altura de toda la grid
    var heightGrid = '';
    styl = $('#grid_' + tabla).attr('style');
    match = /(^|\s)height:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
    if (match != null) {
    h = match[2];
    if (h !== undefined) {
    heightGrid = h;
}
}
    return heightGrid;
}

    function fnjs_def_tabla(tabla) {
    // si es la tabla por defecto, no puedo guardar las preferencias.
    if (tabla === 'uno') {
    alert("<?= _("no puedo grabar las preferencias de la tabla. No puede tener el nombre por defecto") ?>: " + tabla);
    return;
}

    panelVis = fnjs_slick_search_panel(tabla);
    colsVisible = fnjs_slick_col_visible();
    //alert(JSON.stringify(colsVisible));
    colsWidth = fnjs_slick_cols_width(tabla);
    //alert(JSON.stringify(colsWidth));
    widthGrid = fnjs_slick_grid_width(tabla);
    heightGrid = fnjs_slick_grid_height(tabla);

    oPrefs = {
    "panelVis": panelVis,
    "colVisible": colsVisible,
    "colWidths": colsWidth,
    "widthGrid": widthGrid,
    "heightGrid": heightGrid
};
    sPrefs = JSON.stringify(oPrefs);
    url = "<?= ConfigGlobal::getWeb() ?>/src/usuarios/preferencias_guardar";
    parametros = 'que=slickGrid&tabla=' + tabla + '&sPrefs=' + sPrefs + '<?= $h ?>';
    $.ajax({
    url: url,
    type: 'post',
    data: parametros,
    complete: function (rta) {
    rta_txt = rta.responseText;
    if (rta_txt != '' && rta_txt != '\n') {
    alert(rta_txt);
}
}
});
}

    function fnjs_logout() {
    var parametros = 'logout=si&PHPSESSID=<?= session_id(); ?>';
    top.location.href = 'index.php?' + parametros;
}

    function fnjs_windowopen(url) { //para poder hacerlo por el menu
    var parametros = '';
    window.open(url + '?' + parametros);
}

    function fnjs_link_menu(id_grupmenu) {
    var parametros = 'id_grupmenu=' + id_grupmenu + '&PHPSESSID=<?= session_id(); ?>';

    if (id_grupmenu === 'web_externa') {
    top.location.href = 'http://www/exterior/cl/index.html';
} else {
    top.location.href = 'index.php?' + parametros;
}
    //cargar_portada(oficina);
}

    function fnjs_link_submenu(url, parametros
) {
    if (parametros) {
    parametros = parametros + '&PHPSESSID=<?= session_id() ?>';
} else {
    parametros = 'PHPSESSID=<?= session_id() ?>';
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
    alert("<?= _("Error de página devuelta") ?>");
}

    function fnjs_mostrar_atras(id_div, htmlForm
) {
    fnjs_borrar_posibles_atras();
    var name_div = id_div.substring(1);

    if ($(id_div).length) {
    $(id_div).html(htmlForm);
} else {
    html = '<div id="' + name_div + '" style="display: none;">';
    html += htmlForm;
    html += '</div>';
    $('#cargando').prepend(html);

}
    fnjs_ir_a(id_div);
}

    function fnjs_borrar_posibles_atras() {
    if ($('#ir_a').length) $('#ir_a').remove();
    if ($('#ir_atras').length) $('#ir_atras').remove();
    if ($('#ir_atras2').length) $('#ir_atras2').remove();
    if ($('#js_atras').length) $('#js_atras').remove();
    if ($('#go_atras').length) $('#go_atras').remove();
}

    function fnjs_ir_a(id_div) {
    fnjs_guardar_estado();
    
    var is_back = false;
    if (id_div && typeof id_div === 'string' && id_div.indexOf('atras') !== -1) {
        is_back = true;
    }
    if ($(id_div).find('#go_atras, #js_atras, #ir_atras').length > 0 || $(id_div).is('#go_atras, #js_atras, #ir_atras')) {
        is_back = true;
    }
    if (is_back) {
        sessionStorage.setItem('is_back_navigation', 'true');
    }

    var url = $(id_div + " [name='url']").val();
    var parametros = $(id_div + " [name='parametros']").val();
    var bloque = $(id_div + " [name='id_div']").val();

    fnjs_left_side_hide();

    $(bloque).attr('refe', url);
    fnjs_borrar_posibles_atras();
    $.ajax({
    url: url,
    type: 'post',
    data: parametros,
    complete: function (resposta) {
    fnjs_mostra_resposta(resposta, bloque);
},
    error: fnjs_procesarError
});
    return false;
}

    function fnjs_cambiar_link(id_div) {
    // busco si hay un id=ir_a que es para ir a otra página
    if ($('#ir_a').length) {
    fnjs_ir_a(id_div);
    return false;
}
    if ($('#go_atras').length) {
    fnjs_ir_a(id_div);
    return false;
}
    if ($('#ir_atras').length) {
    fnjs_left_side_show();
    return true;
}
    if ($('#js_atras').length) {
    fnjs_ir_a(id_div);
    return true;
}
    var base = $(id_div).attr('refe');
    if (base) {
    var selector = id_div + " a[href]";
    $(selector).each(function (i) {
    var aa = this.href;
    if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
    //alert ("div: "+id_div+"\n base "+base+"\n selector "+selector+"\naa: "+aa );
}
    // si tiene una ref a name(#):
    if (aa !== undefined && aa.indexOf("#") !== -1) {
    part = aa.split("#");
    this.href = "";
    $(this).attr("onclick", "location.hash = '#" + part[1] + "'; return false;");
} else {
    url = fnjs_ref_absoluta(base, aa);
    var path = aa.replace(/[\?#].*$/, ''); // borro desde el '?' o el '#'
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
    // para el div oficina
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

    function fnjs_update_div(bloque, ref, mantener_atras = 0
) {
    if (bloque === '#main') {
    fnjs_guardar_estado();
}
    if (mantener_atras === 0) {
    fnjs_borrar_posibles_atras();
}
    var path = ref.replace(/\?.*$/, '');
    var pattern = /\?/;
    if (pattern.test(ref)) {
    parametros = ref.replace(/^[^\?]*\?/, '');
    parametros = parametros + '&PHPSESSID=<?= session_id(); ?>';
} else {
    parametros = 'PHPSESSID=<?= session_id(); ?>';
}
    //var web_ref=ref.gsub(/\/var\//,'http://');  // cambio el directorio físico (/var/www) por el url (http://www)
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


    function fnjs_ref_absoluta(base, path
) {
    var url = "";
    var inicio = "";
    var secure = <?php if (!empty($_SERVER["HTTPS"])) {
    echo 1;
} else {
    echo 0;
} ?> ;
    if (secure) {
    var protocol = 'https:';
} else {
    var protocol = 'http:';
}
    // El apache ya ha añadido por su cuenta protocolo+$web. Lo quito:
    ini = protocol + '<?= ConfigGlobal::getWeb() ?>';
    if (path.indexOf(ini) !== -1) {
    path = path.replace(ini, '');
} else { // caso especial: http://www/exterior
    ini = protocol + '//www/exterior';
    if (path.indexOf(ini) !== -1) {
    url = path;
    return url;
} else { // pruebo si ha subido un nivel, si ha subido más (../../../) no hay manera. El apache sube hasta nivel de servidor, no más.
    ini = protocol + '<?= ConfigGlobal::getWeb() ?>';
    if (path.indexOf(ini) !== -1) {
    path = path.replace(ini, '');
} else {
    // si el path es una ref. absoluta, no hago nada
    // si empieza por http://
    if (path.match(/^http/)) {
    url = path;
    return url;
} else {
    if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
    alert("Este link no va ha funcionar bien, porque tiene una url relativa: ../../\n" + path);
}
}
}
}
}
    // De la base. puede ser un directorio o una web:
    //   - cambio el directorio físico por su correspondiente web.
    //   - quito el documento.

    a = 0;
    if (base.match(/^<?= addcslashes(ConfigGlobal::$directorio, "/") ?>/)) {	// si es un directorio
    base = base.replace('<?= ConfigGlobal::$directorio ?>', '');
    inicio = protocol + '<?= ConfigGlobal::getWeb() ?>';
    a = 2;
} else {
    if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_fotos, "/") ?>/)) {
    base = base.replace('<?= ConfigGlobal::$dir_fotos ?>', '');
    inicio = protocol + '<?= ConfigGlobal::$web_fotos ?>';
    a = 3;
} else {
    if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_oficinas, "/") ?>/)) {
    base = base.replace('<?= ConfigGlobal::$dir_oficinas ?>', '');
    inicio = protocol + '<?= ConfigGlobal::$web_oficinas ?>';
    a = 4;
} else {
    if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_web, "/") ?>/)) {
    base = base.replace('<?= ConfigGlobal::$dir_web ?>', '');
    inicio = protocol + '<?= ConfigGlobal::getWeb() ?>';
    a = 5;
}
}
}
}
    // si es una web:
    if (!inicio) {
    if (base.indexOf(protocol) != -1) {
    base = base.replace(protocol, '');
    inicio = protocol;
    a = 6;
}
}
    // le quito la página final (si tiene) y la barra (/)
    base = base.replace(/\/(\w+\.\w+$)|\/((\w+-)*(\w+ )*\w+\.\w+$)/, '');
    //elimino la base si ya existe en el path:
    path = path.replace(base, '');
    if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
}
    // si no coincide con ninguno, dejo lo que había.
    if (!inicio) {
    url = path;
} else {
    url = inicio + base + path;
}
    //alert ('url: '+url);
    return url;
}

    function fnjs_enviar_formulario(id_form, bloque
) {
    if (!bloque || bloque === '#main') {
    fnjs_guardar_estado();
}
    fnjs_borrar_posibles_atras();
    if (!bloque) {
    bloque = '#main';
}
    $(id_form).one("submit", function () { // catch the form's submit event
    var tgt_url = $(this).attr('action');
    if (tgt_url && bloque === '#main') {
        var path = tgt_url.replace(/\?.*$/, '');
        $(bloque).attr('refe', path);
    }
    $.ajax({ // create an AJAX call...
    data: $(this).serialize(), // get the form data
    type: 'post', // GET or POST
    url: tgt_url, // the file to call
    success: function (respuesta) {
    fnjs_mostra_resposta(respuesta, bloque);
}
});
    return false; // cancel original event to prevent form submitting
});
    $(id_form).trigger("submit");
    $(id_form).off();
}


    function fnjs_enviar(evt, objeto
) {
    var frm = objeto.id;
    if (evt.keyCode === 13 && evt.type === "keydown") {
    //alert ('hola33 '+evt.keyCode+' '+evt.type);
    // buscar el botón 'ok'
    var b = $('#' + frm + ' input.btn_ok');
    if (b[0]) {
    b[0].onclick();
}
    evt.preventDefault(); // que no siga pasando el evento a submit.
    evt.stopPropagation();
    return false;
}
}

    function fnjs_mostra_resposta(respuesta, bloque
) {
    switch (typeof respuesta) {
    case 'object':
    var myText = respuesta.responseText;
    break;
    case 'string':
    var myText = respuesta.trim();
    break;
}
    $(bloque).empty();
    $(bloque).append(myText);
    fnjs_cambiar_link(bloque);
    // Destacar filas seleccionadas inicialmente en tablas HTML
    $(bloque).find('table input.sel:checked').closest('tr').addClass('selected_row');
    if (bloque === '#main') {
        setTimeout(function() {
            sessionStorage.removeItem('is_back_navigation');
        }, 1000);
    }
}

    // Funcion para comprobar que estan todos los campos necesarios antes de guardar.
    // @param object formulario
    // @param string tabla Nombre de la tabla de la base de datos.
    // @param string ficha 'si' o 'no' si viene de la presentación ficha.php
    // @param integer pau 0 | 1 si es de dossiers
    // @param string exterior 'si' o 'no' si está en la base de datos exterior o no.
    // @return string 'ok' | 'error'
    fnjs_comprobar_campos = function (formulario, obj, ccpau, tabla
) {
    if (tabla === undefined && obj === undefined) {
    return 'ok';
} // sigue.
    var s = 0;
    if (tabla == undefined) tabla = 'x';
    if (obj == undefined) {
    obj = 'x';
}
    //var parametros=$(formulario).serialize()+'&tabla='+tabla+'&ficha='+ficha+'&pau='+pau+'&exterior='+exterior+'&PHPSESSID=<?= session_id(); ?>';
    var parametros = $(formulario).serialize() + '&cc_tabla=' + tabla + '&cc_obj=' + obj + '&cc_pau=' + ccpau;

    url = 'apps/core/comprobar_campos.php';
    // pongo la opción async a 'false' para que espere, si no sigue con el código y devuelve siempre ok.
    $.ajax({
    async: false,
    url: url,
    type: 'post',
    data: parametros,
    dataType: 'html',
    success: function (rta_txt) {
    if (rta_txt.length > 3) {
    alert("<?= _("error") ?>:\n" + rta_txt);
    s = 1;
} else {
    s = 0;
}
}
});
    if (s == 1) {
    return 'error';
} else {
    return 'ok';
}
}

    function XMLtoString(elem) {

    var serialized;

    try {
    // XMLSerializer exists in current Mozilla browsers
    serializer = new XMLSerializer();
    serialized = serializer.serializeToString(elem);
} catch (e) {
    // Internet Explorer has a different approach to serializing XML
    serialized = elem.xml;
}

    return serialized;
}

    function DOMtoString(doc) {
    // Vamos a convertir el árbol DOM en un String
    // Definimos el formato de salida: encoding, indentación, separador de línea,...
    // Pasamos doc como argumento para tener un formato de partida
    //OutputFormat
    // Definimos donde vamos a escribir. Puede ser cualquier OutputStream o un Writer
    //CharArrayWriter
    // Serializamos el arbol DOM
    //XMLSerializer
    serializer = new XMLSerializer();
    serializer.asDOMSerializer();
    serializer.serialize(doc);
    // Ya tenemos el XML serializado en el objeto salidaXML
    System.out.println(serializer.toString());
}

    // Estas variables han de ser globales, y las utiliza el dhtmlxScheduler (dibujar calendarios).
    var _isFF = false;
    var _isIE = false;
    var _isOpera = false;
    var _isKHTML = false;
    var _isMacOS = false;
    var _isChrome = false;

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
        //on mouseover
        function () {
            $(this).animate({
                    height: '+=250' //adds 250px
                }, 'slow' //sets animation speed to slow
            );
        }
        //on mouseout
        , function () {
            $(this).animate({
                    height: '-=250px' //substracts 250px
                }, 'slow'
            );
        }
    );
}

    function fnjs_restet_form() {
    $(this).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
}

    function fnjs_slick_col_visible() {
    // columnas visibles
    colsVisible = {};
    ci = 0;
    v = "true";
    $(".slick-header-columns .slick-column-name").each(function (i) {
    ci++;
    // para saber el nombre
    name = $(this).text();
    // quito posibles espacios en el índice
    name_idx = name.replace(/ /g, '');
    //alert ("name: "+name+" vis: "+v);
    colsVisible[name_idx] = v;
});
    if (ci === 0) {
    colsVisible = 'noCambia';
}
    //alert (ci+'  cols: '+colsVisible);
    return colsVisible;
}

    function fnjs_slick_search_panel(tabla) {
    // panel de búsqueda
    if ($("#inlineFilterPanel_" + tabla).is(":visible")) {
    panelVis = "si";
} else {
    panelVis = "no";
}

    //alert (panelVis);
    return panelVis;
}

    function fnjs_slick_cols_width(tabla) {
    // anchura de las columnas
    colsWidth = {};
    $("#grid_" + tabla + " .slick-header-column").each(function (i) {
    //styl = $(this).attr("style");
    wid = $(this).css('width');
    //alert (wid);
    // quitar los 'px'
    //match = /width:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
    regExp = /(\d*)(px)*/;
    match = regExp.exec(wid);
    w = 0;
    if (match != null) {
    w = match[1];
    if (w === undefined) {
    w = 0;
}
}
    //alert (w);
    // para saber el nombre
    let name = $(this).children(".slick-column-name").text();
    // quito posibles espacios en el índice
    let name_idx = name.replace(/ /g, '');
    colsWidth[name_idx] = w;
});
    return colsWidth;
}

    function fnjs_slick_grid_width(tabla) {
    // anchura de toda la grid
    var widthGrid = '';
    styl = $('#grid_' + tabla).attr('style');
    match = /(^|\s)width:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
    if (match != null) {
    w = match[2];
    if (w !== undefined) {
    widthGrid = w;
}
}
    return widthGrid;
}

    function fnjs_slick_grid_height(tabla) {
    // altura de toda la grid
    var heightGrid = '';
    styl = $('#grid_' + tabla).attr('style');
    match = /(^|\s)height:\s*(\d*)(\.)?(.*)px;/i.exec(styl)
    if (match != null) {
    h = match[2];
    if (h !== undefined) {
    heightGrid = h;
}
}
    return heightGrid;
}

    function fnjs_def_tabla(tabla) {
    // si es la tabla por defecto, no puedo guardar las preferencias.
    if (tabla === 'uno') {
    alert("<?= _("no puedo grabar las preferencias de la tabla. No puede tener el nombre por defecto") ?>: " + tabla);
    return;
}

    panelVis = fnjs_slick_search_panel(tabla);
    colsVisible = fnjs_slick_col_visible();
    //alert(JSON.stringify(colsVisible));
    colsWidth = fnjs_slick_cols_width(tabla);
    //alert(JSON.stringify(colsWidth));
    widthGrid = fnjs_slick_grid_width(tabla);
    heightGrid = fnjs_slick_grid_height(tabla);

    oPrefs = {
    "panelVis": panelVis,
    "colVisible": colsVisible,
    "colWidths": colsWidth,
    "widthGrid": widthGrid,
    "heightGrid": heightGrid
};
    sPrefs = JSON.stringify(oPrefs);
    url = "<?= ConfigGlobal::getWeb() ?>/src/usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php";
    parametros = 'que=slickGrid&tabla=' + tabla + '&sPrefs=' + sPrefs + '<?= $h ?>';
    $.ajax({
    url: url,
    type: 'post',
    data: parametros,
    complete: function (rta) {
    rta_txt = rta.responseText;
    if (rta_txt != '' && rta_txt != '\n') {
    alert(rta_txt);
}
}
});
}

    function fnjs_logout() {
    var parametros = 'logout=si&PHPSESSID=<?= session_id(); ?>';
    top.location.href = 'index.php?' + parametros;
}

    function fnjs_windowopen(url) { //para poder hacerlo por el menu
    var parametros = '';
    window.open(url + '?' + parametros);
}

    function fnjs_link_menu(id_grupmenu) {
    var parametros = 'id_grupmenu=' + id_grupmenu + '&PHPSESSID=<?= session_id(); ?>';

    if (id_grupmenu === 'web_externa') {
    top.location.href = 'http://www/exterior/cl/index.html';
} else {
    top.location.href = 'index.php?' + parametros;
}
    //cargar_portada(oficina);
}

    function fnjs_link_submenu(url, parametros
) {
    if (parametros) {
    parametros = parametros + '&PHPSESSID=<?= session_id() ?>';
} else {
    parametros = 'PHPSESSID=<?= session_id() ?>';
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
    alert("<?= _("Error de página devuelta") ?>");
}

    function fnjs_mostrar_atras(id_div, htmlForm
) {
    fnjs_borrar_posibles_atras();
    var name_div = id_div.substring(1);

    if ($(id_div).length) {
    $(id_div).html(htmlForm);
} else {
    html = '<div id="' + name_div + '" style="display: none;">';
    html += htmlForm;
    html += '</div>';
    $('#cargando').prepend(html);

}
    fnjs_ir_a(id_div);
}

    function fnjs_borrar_posibles_atras() {
    if ($('#ir_a').length) $('#ir_a').remove();
    if ($('#ir_atras').length) $('#ir_atras').remove();
    if ($('#ir_atras2').length) $('#ir_atras2').remove();
    if ($('#js_atras').length) $('#js_atras').remove();
    if ($('#go_atras').length) $('#go_atras').remove();
}

    function fnjs_ir_a(id_div) {
    fnjs_guardar_estado();
    
    var is_back = false;
    if (id_div && typeof id_div === 'string' && id_div.indexOf('atras') !== -1) {
        is_back = true;
    }
    if ($(id_div).find('#go_atras, #js_atras, #ir_atras').length > 0 || $(id_div).is('#go_atras, #js_atras, #ir_atras')) {
        is_back = true;
    }
    if (is_back) {
        sessionStorage.setItem('is_back_navigation', 'true');
    }

    var url = $(id_div + " [name='url']").val();
    var parametros = $(id_div + " [name='parametros']").val();
    var bloque = $(id_div + " [name='id_div']").val();

    fnjs_left_side_hide();

    $(bloque).attr('refe', url);
    fnjs_borrar_posibles_atras();
    $.ajax({
    url: url,
    type: 'post',
    data: parametros,
    complete: function (resposta) {
    fnjs_mostra_resposta(resposta, bloque);
},
    error: fnjs_procesarError
});
    return false;
}

    function fnjs_cambiar_link(id_div) {
    // busco si hay un id=ir_a que es para ir a otra página
    if ($('#ir_a').length) {
    fnjs_ir_a(id_div);
    return false;
}
    if ($('#go_atras').length) {
    fnjs_ir_a(id_div);
    return false;
}
    if ($('#ir_atras').length) {
    fnjs_left_side_show();
    return true;
}
    if ($('#js_atras').length) {
    fnjs_ir_a(id_div);
    return true;
}
    var base = $(id_div).attr('refe');
    if (base) {
    var selector = id_div + " a[href]";
    $(selector).each(function (i) {
    var aa = this.href;
    if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
    //alert ("div: "+id_div+"\n base "+base+"\n selector "+selector+"\naa: "+aa );
}
    // si tiene una ref a name(#):
    if (aa !== undefined && aa.indexOf("#") !== -1) {
    part = aa.split("#");
    this.href = "";
    $(this).attr("onclick", "location.hash = '#" + part[1] + "'; return false;");
} else {
    url = fnjs_ref_absoluta(base, aa);
    var path = aa.replace(/[\?#].*$/, ''); // borro desde el '?' o el '#'
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
    // para el div oficina
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

    function fnjs_update_div(bloque, ref, mantener_atras = 0
) {
    if (bloque === '#main') {
    fnjs_guardar_estado();
}
    if (mantener_atras === 0) {
    fnjs_borrar_posibles_atras();
}
    var path = ref.replace(/\?.*$/, '');
    var pattern = /\?/;
    if (pattern.test(ref)) {
    parametros = ref.replace(/^[^\?]*\?/, '');
    parametros = parametros + '&PHPSESSID=<?= session_id(); ?>';
} else {
    parametros = 'PHPSESSID=<?= session_id(); ?>';
}
    //var web_ref=ref.gsub(/\/var\//,'http://');  // cambio el directorio físico (/var/www) por el url (http://www)
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


    function fnjs_ref_absoluta(base, path
) {
    var url = "";
    var inicio = "";
    var secure = <?php if (!empty($_SERVER["HTTPS"])) {
    echo 1;
} else {
    echo 0;
} ?> ;
    if (secure) {
    var protocol = 'https:';
} else {
    var protocol = 'http:';
}
    // El apache ya ha añadido por su cuenta protocolo+$web. Lo quito:
    ini = protocol + '<?= ConfigGlobal::getWeb() ?>';
    if (path.indexOf(ini) !== -1) {
    path = path.replace(ini, '');
} else { // caso especial: http://www/exterior
    ini = protocol + '//www/exterior';
    if (path.indexOf(ini) !== -1) {
    url = path;
    return url;
} else { // pruebo si ha subido un nivel, si ha subido más (../../../) no hay manera. El apache sube hasta nivel de servidor, no más.
    ini = protocol + '<?= ConfigGlobal::getWeb() ?>';
    if (path.indexOf(ini) !== -1) {
    path = path.replace(ini, '');
} else {
    // si el path es una ref. absoluta, no hago nada
    // si empieza por http://
    if (path.match(/^http/)) {
    url = path;
    return url;
} else {
    if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
    alert("Este link no va ha funcionar bien, porque tiene una url relativa: ../../\n" + path);
}
}
}
}
}
    // De la base. puede ser un directorio o una web:
    //   - cambio el directorio físico por su correspondiente web.
    //   - quito el documento.

    a = 0;
    if (base.match(/^<?= addcslashes(ConfigGlobal::$directorio, "/") ?>/)) {	// si es un directorio
    base = base.replace('<?= ConfigGlobal::$directorio ?>', '');
    inicio = protocol + '<?= ConfigGlobal::getWeb() ?>';
    a = 2;
} else {
    if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_fotos, "/") ?>/)) {
    base = base.replace('<?= ConfigGlobal::$dir_fotos ?>', '');
    inicio = protocol + '<?= ConfigGlobal::$web_fotos ?>';
    a = 3;
} else {
    if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_oficinas, "/") ?>/)) {
    base = base.replace('<?= ConfigGlobal::$dir_oficinas ?>', '');
    inicio = protocol + '<?= ConfigGlobal::$web_oficinas ?>';
    a = 4;
} else {
    if (base.match(/^<?= addcslashes(ConfigGlobal::$dir_web, "/") ?>/)) {
    base = base.replace('<?= ConfigGlobal::$dir_web ?>', '');
    inicio = protocol + '<?= ConfigGlobal::getWeb() ?>';
    a = 5;
}
}
}
}
    // si es una web:
    if (!inicio) {
    if (base.indexOf(protocol) != -1) {
    base = base.replace(protocol, '');
    inicio = protocol;
    a = 6;
}
}
    // le quito la página final (si tiene) y la barra (/)
    base = base.replace(/\/(\w+\.\w+$)|\/((\w+-)*(\w+ )*\w+\.\w+$)/, '');
    //elimino la base si ya existe en el path:
    path = path.replace(base, '');
    if ("<?= ConfigGlobal::mi_usuario() ?>" === "dani") {
}
    // si no coincide con ninguno, dejo lo que había.
    if (!inicio) {
    url = path;
} else {
    url = inicio + base + path;
}
    //alert ('url: '+url);
    return url;
}

    function fnjs_enviar_formulario(id_form, bloque
) {
    if (!bloque || bloque === '#main') {
    fnjs_guardar_estado();
}
    fnjs_borrar_posibles_atras();
    if (!bloque) {
    bloque = '#main';
}
    $(id_form).one("submit", function () { // catch the form's submit event
    var tgt_url = $(this).attr('action');
    if (tgt_url && bloque === '#main') {
        var path = tgt_url.replace(/\?.*$/, '');
        $(bloque).attr('refe', path);
    }
    $.ajax({ // create an AJAX call...
    data: $(this).serialize(), // get the form data
    type: 'post', // GET or POST
    url: tgt_url, // the file to call
    success: function (respuesta) {
    fnjs_mostra_resposta(respuesta, bloque);
}
});
    return false; // cancel original event to prevent form submitting
});
    $(id_form).trigger("submit");
    $(id_form).off();
}

    function fnjs_enviar(evt, objeto
) {
    var frm = objeto.id;
    if (evt.keyCode === 13 && evt.type === "keydown") {
    //alert ('hola33 '+evt.keyCode+' '+evt.type);
    // buscar el botón 'ok'
    var b = $('#' + frm + ' input.btn_ok');
    if (b[0]) {
    b[0].onclick();
}
    evt.preventDefault(); // que no siga pasando el evento a submit.
    evt.stopPropagation();
    return false;
}
}

    function fnjs_mostra_resposta(respuesta, bloque
) {
    switch (typeof respuesta) {
    case 'object':
    var myText = respuesta.responseText;
    break;
    case 'string':
    var myText = respuesta.trim();
    break;
}
    $(bloque).empty();
    $(bloque).append(myText);
    fnjs_cambiar_link(bloque);
    // Destacar filas seleccionadas inicialmente en tablas HTML
    $(bloque).find('table input.sel:checked').closest('tr').addClass('selected_row');
    if (bloque === '#main') {
        setTimeout(function() {
            sessionStorage.removeItem('is_back_navigation');
        }, 1000);
    }
}

    /*
    * funcion para comprobar que estan todos los campos necesarios antes de guardar.
    *@param object formulario
    *@param string tabla Nombre de la tabla de la base de datos.
    *@param string ficha 'si' o 'no' si viene de la presentación ficha.php
    *@param integer pau 0 | 1 si es de dossiers
    *@param string exterior 'si' o 'no' si está en la base de datos exterior o no.
    *@return string 'ok' | 'error'
    */
    fnjs_comprobar_campos = function (formulario, obj, ccpau, tabla
) {
    if (tabla === undefined && obj === undefined) {
    return 'ok';
} // sigue.
    var s = 0;
    if (tabla == undefined) tabla = 'x';
    if (obj == undefined) {
    obj = 'x';
}
    //var parametros=$(formulario).serialize()+'&tabla='+tabla+'&ficha='+ficha+'&pau='+pau+'&exterior='+exterior+'&PHPSESSID=<?= session_id(); ?>';
    var parametros = $(formulario).serialize() + '&cc_tabla=' + tabla + '&cc_obj=' + obj + '&cc_pau=' + ccpau;

    url = 'apps/core/comprobar_campos.php';
    // pongo la opción async a 'false' para que espere, si no sigue con el código y devuelve siempre ok.
    $.ajax({
    async: false,
    url: url,
    type: 'post',
    data: parametros,
    dataType: 'html',
    success: function (rta_txt) {
    if (rta_txt.length > 3) {
    alert("<?= _("error") ?>:\n" + rta_txt);
    s = 1;
} else {
    s = 0;
}
}
});
    if (s == 1) {
    return 'error';
} else {
    return 'ok';
}
}

    function XMLtoString(elem) {

    var serialized;

    try {
    // XMLSerializer exists in current Mozilla browsers
    serializer = new XMLSerializer();
    serialized = serializer.serializeToString(elem);
} catch (e) {
    // Internet Explorer has a different approach to serializing XML
    serialized = elem.xml;
}

    return serialized;
}

    function DOMtoString(doc) {
    // Vamos a convertir el árbol DOM en un String
    // Definimos el formato de salida: encoding, indentación, separador de línea,...
    // Pasamos doc como argumento para tener un formato de partida
    //OutputFormat
    // Definimos donde vamos a escribir. Puede ser cualquier OutputStream o un Writer
    //CharArrayWriter
    // Serializamos el arbol DOM
    //XMLSerializer
    serializer = new XMLSerializer();
    serializer.asDOMSerializer();
    serializer.serialize(doc);
    // Ya tenemos el XML serializado en el objeto salidaXML
    System.out.println(serializer.toString());
}

    /* Estas variables han de ser globales, y las utiliza el dhtmlxScheduler (dibujar calendarios). */
    var _isFF = false;
    var _isIE = false;
    var _isOpera = false;
    var _isKHTML = false;
    var _isMacOS = false;
    var _isChrome = false;

    $('<style>tr.selected_row { background-color: #ffffcc !important; } tr[onclick] { cursor: pointer; }</style>').appendTo('head');

    function fnjs_clic_fila(row, event) {
        // Se carga el json de la fila
        var rowData = $(row).data('json');
        $(row).closest('table').trigger('rowSelected', [rowData]);

        // Si el clic es en un link o en el propio checkbox, dejar que el navegador actue normal
        if ($(event.target).is('a, span.link, input[type=checkbox], input:button')) {
            return;
        }
        var $chk = $(row).find('input.sel');
        if ($chk.length) {
            $chk.prop('checked', !$chk.prop('checked')).trigger('change');
        } else {
            // No hay checkbox? Comportamiento tipo "Single Selection" (destacar la fila actual)
            $(row).siblings().removeClass('selected_row');
            $(row).addClass('selected_row');
        }
    }

    // Listener global para destacar filas seleccionadas en tablas HTML
    $(document).on('change', 'table input.sel', function() {
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
        //on mouseover
        function () {
            $(this).animate({
                    height: '+=250' //adds 250px
                }, 'slow' //sets animation speed to slow
            );
        }
        //on mouseout
        , function () {
            $(this).animate({
                    height: '-=250px' //substracts 250px
                }, 'slow'
            );
        }
    );
}

    function fnjs_restet_form() {
    $(this).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
}

</script>
