<?php
/*
   importante para que el navegador entienda que lo que sigue es javascrip, ya que 
	la extension del fichero no es ".js", sino ".js.php"
*/
header('Content-Type: text/javascript; charset=UTF-8');
?>

function fnjs_sin_acentos(text) {
	var chars = {'á':'a','é':'e','í':'i','ó':'o','ú':'u','ç':'cz','à':'a','è':'e','ò':'o','ä':'a','ë':'e','ï':'i','ö':'o','ü':'u','â':'a','ê':'e','î':'i','ô':'o','û':'u','Á':'A','É':'E','Í':'I','Ó':'O','Ú':'U','Ç':'CZ','À':'A','È':'E','Ò':'O','Ä':'A','Ë':'E','Ï':'I','Ö':'O','Ü':'U','Â':'A','Ê':'E','Î':'I','Ô':'O','Û':'U','ñ':'nz','Ñ':'NZ'} 
	rta = text.replace(/[áéíóúçàèòäëïöüâêîôûÁÉÍÓÚÇÀÈÒÄËÏÖÜÂÊÎÔÛñÑ]/g, m => chars[m]);
	return rta;
}

function fnjs_selectAll(formulario,Name,val,aviso){
	aviso = typeof aviso !== 'undefined' ? aviso : 1;
	if (aviso == 1) {
		alert ("<?php printf (_("Sólo se seleccionan los ítems que se ha visualizado.")); ?>");
	}
	var form=$(formulario).attr('id');
	/* selecciono los elementos input del id=formulario */
	var selector=$('#'+form+' input');
	if(val==null){var val='toggle';}
	$(selector).each(function(i,item) {
			if($(item).attr('name') == Name) {
				switch (val) {
					case 'all':
						$(item).prop('checked',true);
						break;
					case 'none':
						$(item).prop('checked',false);
						break;
					case 'toggle':
						$(item).trigger("click");
						break;
				}
			}
		}
	);
	var $form = $(formulario);
	if (!$form.length && form) {
		$form = $('#' + form);
	}
	/* En SlickGrid la selección real vive en getSelectedRows(); los checkboxes visibles no bastan. */
	$form.find('[id^="grid_"]').each(function () {
		var tabla = this.id.substring(5);
		var grid = window['grid_' + tabla];
		var dataView = window['dataView_' + tabla];
		if (!grid || !dataView) {
			return;
		}
		if (val === 'all') {
			var rows = [];
			for (var i = 0; i < dataView.getLength(); i++) {
				rows.push(i);
			}
			grid.setSelectedRows(rows);
		} else if (val === 'none') {
			grid.setSelectedRows([]);
		}
	});
}

function fnjs_collect_sel_ids(formulario) {
	fnjs_sync_grid_sel_checkboxes(formulario);
	var ids = [];
	$(formulario).find('input.sel:checked').each(function () {
		var v = $(this).val();
		if (v) {
			ids.push(v);
		}
	});
	return ids;
}

function fnjs_set_sSeleccionados(formulario, ids) {
	var $form = $(formulario);
	var hid = $form.find('input[name="sSeleccionados"]');
	if (!hid.length) {
		hid = $('<input>', {type: 'hidden', name: 'sSeleccionados', id: 'sSeleccionados'});
		hid.appendTo($form);
	}
	hid.val(ids.join(','));
}

function fnjs_sync_grid_sel_checkboxes(formulario) {
	var formId = $(formulario).attr('id');
	if (!formId) {
		return;
	}
	var $form = $('#' + formId);
	var idsToSync = {};

	$form.find('input.sel:checked').each(function () {
		var v = $(this).val();
		if (v) {
			idsToSync[v] = true;
		}
	});

	$form.find('[id^="grid_"]').each(function () {
		var tabla = this.id.substring(5);
		var grid = window['grid_' + tabla];
		var dataView = window['dataView_' + tabla];
		if (!grid || !dataView) {
			return;
		}
		var selectedIndices = grid.getSelectedRows();
		if (!selectedIndices || selectedIndices.length === 0) {
			return;
		}
		selectedIndices.forEach(function (idx) {
			var item = dataView.getItem(idx);
			var id = (typeof fnjs_parse_slick_sel_value === 'function')
				? fnjs_parse_slick_sel_value(item ? item.sel : '')
				: '';
			if (id) {
				idsToSync[id] = true;
			}
		});
	});

	var idList = Object.keys(idsToSync);

	// Huérfanos de una sync anterior: si la celda del grid ya está renderizada, sobran y fnjs_solo_uno cuenta doble.
	$form.find('input.sel[data-sync-orphan]').remove();

	if (idList.length === 0) {
		return;
	}

	$form.find('input.sel').prop('checked', false);
	idList.forEach(function (id) {
		var $cb = $form.find('input.sel').filter(function () {
			return $(this).val() === id;
		});
		if ($cb.length) {
			$cb.prop('checked', true);
		} else {
			$('<input>', {
				type: 'checkbox',
				'class': 'sel',
				name: 'sel[]',
				value: id,
				'data-sync-orphan': '1'
			}).prop('checked', true).css('display', 'none').appendTo($form);
		}
	});
}

function fnjs_solo_uno_grid(formulario) {
	var s=0;
	var form=$(formulario).attr('id');
	/* selecciono los elementos con class="slick-cell-checkboxsel" de las tablas del id=formulario */
	var sel=$('#'+form+' div.slick-cell-checkboxsel > input:checked');
	var s = sel.length;

	if ( s > 1 ) {
		alert ("<?php printf (_("Sólo puede seleccionar un elemento. Ha selecionado %s."),'"+s+"'); ?>");
	}
	if (s==0) {
		alert ("<?php printf (_("No ha seleccionado ninguna fila. debe hacer click en algún chekbox de la izquierda. ")); ?>");
	}
	return s;
}

function fnjs_solo_uno(formulario, multiple = false) {
	fnjs_sync_grid_sel_checkboxes(formulario);
	var s=0;
	var form=$(formulario).attr('id');
	/* selecciono los elementos con class="sel" de las tablas del id=formulario */
	var sel=$('#'+form+' input.sel:checked');
	var s = sel.length;

	if ( s > 1 && !multiple) {
		alert ("<?php printf (_("Sólo puede seleccionar un elemento. Ha selecionado %s."),'"+s+"'); ?>");
	}
	if (s==0) {
		alert ("<?php printf (_("No ha seleccionado ninguna fila. debe hacer click en algún chekbox de la izquierda. ")); ?>");
	}
	if (s > 0) {
		var idSel = sel.first().val();
		var hid = $(formulario).find('input[name="id_sel"]');
		if (hid.length === 0) {
			hid = $('<input type="hidden" name="id_sel">').appendTo(formulario);
		}
		hid.val(idSel);
	}
	return s;
}

function fnjs_generarNomActiv(formulario) {
	var ini=$('#f_ini').val();
	var fin=$('#f_fin').val();
	var dl_org=$('#dl_org').val();

	if ($('#isfsv_val').length) {
		var sf=$('#isfsv_val').val();
		if (sf==1) var sf='sv'
		if (sf==2) var sf='sf'
		var lloc=$('#span_nom_ubi').html();
		if (sf=='sf') { sf=sf+' ';} else {sf='';}
		var actividad=$('#iactividad_val :selected').text();
		var asistentes=$('#iasistentes_val :selected').text();
		var tipo=$('#inom_tipo_val :selected').text();
		if (tipo=='(sin especificar)')	{ tipo=''; } else { tipo= '' + tipo; }
		var tipus=sf+actividad + ' ' + asistentes + ' ' + tipo;
	} else {
		var sf=$('#ssfsv').val();
		var lloc=$('#span_nom_ubi').html();
		if (sf=='sf') { sf=sf+' ';} else {sf='';}
		var actividad=$('#sactividad').val();
		var asistentes=$('#sasistentes').val();
		var tipo=$('#snom_tipo').val();
		if (tipo=='(sin especificar)')	{ tipo=''; } else { tipo= '' + tipo; }
		var tipus=sf+actividad + ' ' + asistentes + ' ' + tipo;
	}

    nom = tipus + ' ' + lloc + ' (' + ini + '-' + fin +')-'+dl_org;
    $('#nom_activ').val(nom);
}

