<?php
/**
/* Para incluir dos funciones de javascript, que añaden desplegables, o inputs 
/* segun se vayan necesitando.
/* Función:
/*   	fnjs_mas_inputs(evt,nom,ir_a_camp);
/*   	mas_selects(evt,nom,ir_a_camp)  Además hay que tener una variable $var_options donde estén todas las opciones del desplegable
/*   los parámetros:
/*	evt: evento
/*	nom: nombre para identificar los campos y etiquetas id 
/*	ir_a_camp: siguiente campo al que tiene que ir después (tabulador)
/*
/* Desde la página hay que tener algo asi:  (nom=oficinas)
/*
/*	<tr>
/*		<td class=etiqueta><?php echo _("oficina"); ?>:</td>	
/*		<td id="col_oficinas" colspan=8><span id="oficinas_span" ></span>
/*	<?php
/*		// para que me salga una opción más en blanco
/*		echo "<select tabindex='89' id='oficinas_mas' name='oficinas_mas' class=contenido  onChange=\"fnjs_mas_inputs(event,'oficinas','g1');\" onKeyDown=\"fnjs_mas_inputs(event,'oficinas','g1');\" onkeyPress=\"stop_tab(event);\" ><option value=0 />";
/*			pdo_options($oDBSt_db_oficinas,"",0);
/*		echo "</select>";
/*		echo "</td></tr>";
/*	echo "<input type=hidden name='oficinas_num' id='oficinas_num' value=0>";
/*   </table>
/*
/*
*/
/*
   importante para que el navegador entienda que lo que sigue es javascrip, ja que 
	la extension del fichero no es ".js", sino ".js.php"
*/
//header('Content-Type: text/javascript; charset=UTF-8');

$var_options = empty($var_options)? '' : $var_options;
?>
<script>
mas_selects = function(evt,nom,ir_a_camp) {
	if(evt=="x") {
		var valor=1;
	} else {
		var id_campo=evt.currentTarget.id;
		var valor=$(id_campo).val();
		evt.preventDefault();
		evt.stopPropagation();
	}
	if (evt.keyCode==9 || evt.type=="change" || evt=="x") {
		if (valor!=0) {
			id_num=nom+"_num";
			id_mas=nom+"_mas";
			id_span=nom+"_span";
			var num=$(id_num);
			var camp=$(id_mas);
			var val_camp=camp.value;
			
			var n=num.value;
			var txt;
			var tab=8+n;
			

			txt='<select tabindex="'+tab+'" id=xcamp['+n+'] name=xcamp['+n+'] class=contenido onChange="comprobar_selects(\'xcamp['+n+']\',\'+ir_a_camp+\');"><option />';
			txt += '<?=	$var_options ?>';
			txt += '</select>';

			/* antes del desplegable de añadir */
			new Insertion.Bottom(id_span,txt);
			/* selecciono el valor del desplegable */
			var xnom='xcamp['+n+']';
			$(xnom).val(val_camp);
			num.value=++n;
			
			$(id_mas).val(0);
			//$('#mas_of').activate();
			//return false;
		} else {
			ir_a(ir_a_camp);
		}
	}
}

comprobar_selects = function (id_camp,ir_a_camp) {
	var val_camp=$(id_camp).val();
	if (!val_camp) {
		$(id_camp).hide();
	} else {
		ir_a(ir_a_camp);
	}
}

<!-- ------------------- PARA INPUTS -------------------- -->
fnjs_mas_inputs = function(e,nom,ir_a_camp,a_campos) {
	/* a_campos es un array con el número de campos que se quieren poner en cada fila
		Cada elemento es a su vez un array con los datos del campo:
			- [0] nombre (e id)
			- [1] type (text o hidden)
			- [2] size
			- [3] valor
	*/
	var code = (e.keyCode ? e.keyCode : e.which);
	if(e=="x") {
		var valor=1;
	} else {
		var id_campo='#'+e.currentTarget.id;
		var valor=$(id_campo).val();
		if(code!=9) {
			e.preventDefault();
			e.stopPropagation();
		}
	}
	if ( code==9 || e.type=="change" || e=="x") {
		if (valor!=0) {
			id_num='#'+nom+'_num';
			id_mas='#'+nom+'_mas';
			id_span='#'+nom+'_span';
			var px_scroll = $('#main').scrollTop();
			var num=$(id_num);
			var val_camp=$(id_mas).val();

			var n=num.val();
			var txt;
			var tab=8+n;
			var txt='';				

			for (var i = 0; i < a_campos.length; i++) {
				var xnom=a_campos[i][0];
				var xtype=a_campos[i][1];
				var xsize=a_campos[i][2];
				var xvalue=a_campos[i][3];
				
				if(xvalue=='x') {
					val='value="'+val_camp+'"';
				} else {
					val='';
				}
			
				txt+='<input type="'+xtype+'" size="'+xsize+'" tabindex="'+tab+'" id='+xnom+'_'+n+' name='+xnom+'['+n+']  '+val+' class=contenido onChange="fnjs_comprobar_input(\''+xnom+'_'+n+'\',\''+ir_a_camp+'\');" />';
			}

			/* antes del desplegable de añadir */
			$(id_span).append(txt);
			/* selecciono el valor y lo pongo en el nuevo input */
			var nombre='#'+nom+'_'+n;
			$(nombre).val(val_camp);
			n=++n;
			num.val(n);
			
			$(id_mas).val('');
			$(ir_a_camp).focus();
			//alert ("scroll: "+px_scroll );
			//$('#main').scrollTop(px_scroll);
		} else {
			$(ir_a_camp).focus();
		}
	}
}

fnjs_comprobar_input = function (camp,ir_a_camp) {
	var id_camp='#'+camp;
	var val_camp=$(id_camp).val();
	if (val_camp==undefined || !val_camp) {
		$(id_camp).hide();
	} else {
		ir_a(ir_a_camp);
	}
}

</script>
