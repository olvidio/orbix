<?php
/*
   importante para que el navegador entienda que lo que sigue es javascrip, ja que 
	la extension del fichero no es ".js", sino ".js.php"
*/
header('Content-Type: text/javascript; charset=UTF-8');
?>
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
						$(item).attr('checked',true);
						break;
					case 'none':
						$(item).attr('checked',false);
						break;
					case 'toggle':
						$(item).click();
						break;
				}
			}
		}
	);
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
		alert ("<?php printf (_("No ha seleccionado ninguna fila. Debe hacer click en algún chekbox de la izquierda. ")); ?>");
	}
	return s;
}

function fnjs_solo_uno(formulario) {
	var s=0;
	var form=$(formulario).attr('id');
	/* selecciono los elementos con class="sel" de las tablas del id=formulario */
	var sel=$('#'+form+' input.sel:checked');
	var s = sel.length;
	if ( s > 1 ) {
		alert ("<?php printf (_("Sólo puede seleccionar un elemento. Ha selecionado %s."),'"+s+"'); ?>");
	}
	if (s==0) {
		alert ("<?php printf (_("No ha seleccionado ninguna fila. Debe hacer click en algún chekbox de la izquierda. ")); ?>");
	}
	return s;
}

function fnjs_generarNomActiv(formulario) {
	var ini=$('#f_ini').val();
	var fin=$('#f_fin').val();
	var dl_org=$('#dl_org').val();
	
	if ($('#isfsv_val').length) {
		/*
		el_1=$('#isfsv_val').selectedIndex;
		if (el_1) {
			var sf=$('#isfsv_val').val();
		} else {
			if ($('#isfsv_val').value==1) var sf='sv'
			if ($('#isfsv_val').value==2) var sf='sf'
		}
		*/
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
		/*
		el_2=$('#id_ubi').selectedIndex;
		var lloc=$('#id_ubi').options[el_2].text;
		*/
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
