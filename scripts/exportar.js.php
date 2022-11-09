<?php
/**
/* Para incluir dos funciones de javascript, que añaden desplegables, o inputs 
/* segun se vayan necesitando.
/* Función:
/*   	fnjs_mas_inputs(evt,nom,ir_a_camp);
/*   	fnjs_mas_selects(evt,nom,ir_a_camp)  Además hay que tener una variable $var_options donde estén todas las opciones del desplegable
*/

// Formato de fecha:
$idioma = $_SESSION['session_auth']['idioma'];
# Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
if (!isset($idioma)){ $idioma = $_SESSION['oConfig']->getIdioma_default(); }
$a_idioma = explode('.',$idioma);
$code_lng = $a_idioma[0];
//$code_char = $a_idioma[1];
switch ($code_lng) {
	case 'en_US':
		// formato = mes/dia/año;
		$fecha_local= '      fecha_iso=any+"-"+a_val[0]+"-"+a_val[1];';
		$fecha_hora_local = '    fecha_iso=any+"-"+a_fecha[0]+"-"+a_fecha[1]+"T"+a_hora[0]+":"+a_hora[1]+":"+a_hora[2];';
		break;
	default:
		// formato = dia/mes/año;
		$fecha_local= '      fecha_iso=any+"-"+a_val[1]+"-"+a_val[0];';
		$fecha_hora_local = '    fecha_iso=any+"-"+a_fecha[1]+"-"+a_fecha[0]+"T"+a_hora[0]+":"+a_hora[1]+":"+a_hora[2];';
}

?>
<script>
function convertToIso(fecha) {
	var dateformat = /^\d{1,2}(\-|\/|\.)\d{1,2}(\-|\/|\.)\d{2,4}$/;
	var dateTimeFormat = /^\d{1,2}(\-|\/|\.)\d{1,2}(\-|\/|\.)\d{2,4} \d{2}:\d{2}:\d{2}$/;

	if (dateTimeFormat.test(fecha)) {
		var dateTime = fecha.split(' ');
		var a_fecha = dateTime[0].split('/');
		var a_hora = dateTime[1].split(':');
		any=a_fecha[2];
		if (any < 100) any="20"+any;
		if (a_fecha[0] < 10) a_fecha[0]="0"+a_fecha[0];
		if (a_fecha[1] < 10) a_fecha[1]="0"+a_fecha[1];
		//fecha_iso=any+"-"+a_fecha[1]+"-"+a_fecha[0]+"T"+a_hora[0]+":"+a_hora[1]+":"+a_hora[2];
		<?= $fecha_hora_local ?>
		return fecha_iso;
	}
	if (dateformat.test(fecha)) {
		a_val=fecha.split('/');
		any=a_val[2];
		if (any < 100) any="20"+any;
		if (a_val[0] < 10) a_val[0]="0"+a_val[0];
		if (a_val[1] < 10) a_val[1]="0"+a_val[1];
		//fecha_iso=any+"-"+a_val[1]+"-"+a_val[0];
		<?= $fecha_local ?>
		return fecha_iso;
	} else {
		return false;
	}
}
function fnjs_slickToTable(node) {
	tagId=$(node).attr("id");
	tabla=tagId.replace("GridContainer_","");
	var dataView="dataView_"+tabla;
	var grid="grid_"+tabla;
	Columns=window[grid].getColumns();
	Items=window[dataView].getItems();
	//alert("Cols:\n"+Columns.length);
	//alert("ITEM:\n"+Items.length);
	/*
	 daniCol=JSON.stringify(Columns[0]);
	dani=JSON.stringify(Items[0]);
	alert("1:\n"+daniCol);
	alert("1:\n"+dani);
	*/
	var tab=0;
	cabecera="<tr>";
	$.each(Columns,function(){
		nameCol=this.name;
		// si es el cuadrado de seleccionar, me lo salto.
		if (nameCol=="sel") return true; // equivalent to 'continue' with a normal for loop
		amplada=(this.width)*2/3; //Ajusto para el pdf. el odt ya lo cambio en xslt
		tab+=amplada;
		cabecera+="<th web-width=\""+amplada+"\" tab-width=\""+tab+"\">"+nameCol+"</th>";
		//ColVisibles[c]=this.name;
	});
	cabecera+="</tr>";
	//alert("cab:\n"+cabecera);
	tbody="";
	f=0;
    $.each(Items,function(){
		tbody+="<tr>";
		Fila=this;
		metadata = window[dataView].getItemMetadata(f);
		d = [];
		if(metadata) d=metadata['columns'];
		f++;
		$.each(Columns,function(d){
			colspan='';
			nameId=this.id;
			nameCol=this.name;
			classCol=this.cssClass;
			if (nameCol=="sel") return true; // equivalent to 'continue' with a normal for loop
			if (Fila[nameId]) {
				if(metadata && d[nameId]){
					dd=d[nameId];
					if (dd.colspan){ colspan='colspan="'+dd.colspan+'"'; }
				}
				if (classCol=="fecha_hora") {
					fecha_iso=convertToIso(Fila[nameId]);
					tbody+="<td "+colspan+" class='fecha_hora' fecha_iso='"+fecha_iso+"'>"+Fila[nameId]+"</td>";
				} else {
					if (classCol=="fecha") {
						fecha_iso=convertToIso(Fila[nameId]);
						tbody+="<td "+colspan+" class='fecha' fecha_iso='"+fecha_iso+"'>"+Fila[nameId]+"</td>";
					} else { 
						if (isNaN(Fila[nameId])) {
							tbody+="<td "+colspan+">"+Fila[nameId]+"</td>";
						} else { // si es número y tiene separador decimal lo cambio a coma.
							num=Fila[nameId].replace(".",",");
							tbody+="<td "+colspan+">"+num+"</td>";
						}
					} 
				}
			} else {
				tbody+="<td></td>";
			}
		});		
		tbody+="</tr>";
	});
	//txt+="<table>"+cabecera+tbody+"</table>";
	//alert("grid:\n"+cabecera+tbody+"</table>");
	return "<table>"+cabecera+tbody+"</table>";
}	
rec=0;
function fnjs_convert(node){
	var tag="";
	var rta="";
	var rta1="";
	rec=rec+1;
	//var tag = $(node).get(0).tagName.toLowerCase();
	//tag=$(node).get(0).tagName;
	// utilizo nodename en vez de tagname, porque asi me coje los #commnent...
	tag=$(node).get(0).nodeName.toLowerCase();
	//dani=JSON.stringify(a_tag);
	//if (rec<30)	alert("1:\n"+tag);
	tagId=$(node).attr("id");
	if (tagId == undefined) tagId="";
	//if ($(node).hasClass("no_print")) return false;
	//alert ("num: "+node+"\ntipo: "+tag+"\nid: "+tagId);
	switch(tag){
		case "script":
			return " ";
			break;
		case "style":
		case "hr":
			break;
		case "img":
			//rta=$(node).html();
			return " ";
			break;
		case "textarea":
			value=$(node).val();
			myText=value.replaceAll('\n','\r'); 
			rta+=myText;
			break;
		case "p":
			$(node).children().filter(":visible").each(function(i){
				//alert("i: "+i+"\n"+$(node).html());
				if (i==0) {	
					rta+=$(node).text();
				}
				rta+=fnjs_convert(this);
			});
			break;
		case "input":
			//alert("tipo: "+$(node).attr("type"));
			if (tagId=="sel") return false;
			tipo=$(node).attr("type");
			value=$(node).val();
			if (tipo != undefined) {
				if (tipo.toUpperCase()==="hidden".toUpperCase() || tipo.toUpperCase()==="button".toUpperCase()) return " ";
				if (tipo.toUpperCase()==="radio".toUpperCase() ) {
					if ($(node).prop('checked')) {
						txta=$(node).parent().text();
						return txta;
					} else {
						return " ";
					} 
				}
			}
			if (value === undefined || !value) {
				rta=" ";
			} else {
				rta=value;
			}
			break;
		case "select":
			rta=$('#'+tagId+' option:selected').text();
			if (!rta) {
				return " ";
			}
			break;
		case "h1":
		case "h2":
		case "h3":
			rta="<"+tag+">"+$(node).html()+"</"+tag+">";
			break;
		case "div":
			if (tagId.indexOf("GridContainer") != -1) { 
				//txt+=fnjs_slickToTable(node); 
				rta2=fnjs_slickToTable(node); 
				rta+=fnjs_convert(rta2);
			} else {
				$(node).children().filter(":visible").each(function(i){
					rta+=fnjs_convert(this);
				});
			}
			if ($(node).attr("class")) {
				clas=" class='"+$(node).attr('class')+"'";
			} else {
				clas="";
			}
			if (rta) {
				rta="<div"+clas+">"+rta+"</div>";
			}
			break;
		case "form":
			$(node).children().filter(":visible").each(function(i){
					rta+=fnjs_convert(this);
				});
			break;
		case "table":
			rta1="";
			$(node).children().each(function(){
					rta1+=fnjs_convert(this);
				});
			if (rta1) {
				if ($(node).attr("class")) {
					clas=" class='"+$(node).attr('class')+"'";
				} else {
					clas="";
				}
				rta="<table"+clas+">"+rta1+"</table>";
			} else {
				rta="<"+tag+">"+$(node).html()+"</"+tag+">"; 
			}
			break;
	   	case "td":
			//html=$(node).html();
			rta1="";
			// buscar los textos huerfanos de tag.
			txt=$(node).html();
		    var m = /^([^<]*)(<.+>)([^>]*)$/.exec(txt);
			//alert ("m: "+m);
			$(node).children().each(function(){
				//alert ("WW"+this);
					rta1+=fnjs_convert(this);
				});
			if ($(node).attr("class")) {
				clas=" class='"+$(node).attr('class')+"'";
			} else {
				clas="";
			}
			if (rta1) {
				rr='';
				//rr=$(node).contents().not($(node).children()).text();
				//if (rec<30) alert(tag+" con hijos\n"+rta1);
				rta="<"+tag+" "+clas+">"+rr+rta1+"</"+tag+">"; 
			} else {
				//if (rec<30) alert(tag+" sin hijos");
				//rta="<"+tag+" "+clas+">"+$(node).html()+"</"+tag+">"; 
				colSpan=$(node).attr("colspan");
				if (colSpan== undefined) { Span="";} else {Span=" colspan='"+colSpan+"'"}
				fecha_iso=convertToIso($(node).html()); //compruebo si tiene forma de fecha.
				if (fecha_iso) {
					rta="<td class='fecha' fecha_iso='"+fecha_iso+"'>"+$(node).html()+"</td>";
				} else {
					num_iso=$(node).html();
					if ($.isNumeric(num_iso)) { // si es número y tiene separador decimal lo cambio a coma.
						num=num_iso.replace(".",",");
						rta="<td"+Span+" class='numero' num_iso='"+num_iso+"'>"+num+"</td>";
					} else {
						rta="<"+tag+Span+">"+num_iso+"</"+tag+">"; 
					}
				}
			}
			break;
		case "tbody":
		case "tr":
			if (m && m[1]) rta1+=m[1];
			$(node).children().each(function(){
				//alert ("WW"+this);
					rta1+=fnjs_convert(this);
				});
			if (m && m[3]) rta1+=m[3];
			//rta1+=$(node).html();
			if (rta1) {
				rta="<"+tag+">"+rta1+"</"+tag+">"; 
			} else {
				colSpan=$(node).attr("colspan");
				if (colSpan== undefined) { Span="";} else {Span=" colspan='"+colSpan+"'"}
				fecha_iso=convertToIso($(node).html()); //compruebo si tiene forma de fecha.
				if (fecha_iso) {
					rta="<td class='fecha' fecha_iso='"+fecha_iso+"'>"+$(node).html()+"</td>";
				} else {
					num_iso=$(node).html();
					if ($.isNumeric(num_iso)) { // si es número y tiene separador decimal lo cambio a coma.
						num=num_iso.replace(".",",");
						rta="<td"+Span+" class='numero' num_iso='"+num_iso+"'>"+num+"</td>";
					} else {
						rta="<"+tag+Span+">"+num_iso+"</"+tag+">"; 
					}
				}
			//if (rec<30)	alert ("rta: "+rta);
			}
			break;
		case "th":
			var tab=0;
			amplada=$(node).attr("web-width");
			if (amplada==undefined) amplada=($(node).outerWidth())*2/3;
			tab=$(node).attr("tab-width");
			if (tab==undefined) tab=amplada;
			rta="<th web-width=\""+amplada+"\" tab-width=\""+tab+"\">"+$(node).html()+"</th>";
			break;
		case "br": //dentro de un td es fatal, no sé que pasa en el resto de casos...
		case "#comment":
			return "";
   			break;
	   	default:
			//html=$(node).html();
			rta1="";
				$(node).children().each(function(i){
					//if (rec<30)	alert ("hijo n: "+i);
					rta1+=fnjs_convert(this);
				});
			if (rta1) {
				//if (rec<30) alert(tag+" con hijos\n"+rta1);
				rta="<"+tag+">"+rta1+"</"+tag+">"; 
			} else {
				//if (rec<30) alert(tag+" sin hijos");
				rta="<"+tag+">"+$(node).html()+"</"+tag+">"; 
			}
			//if (rec<30) alert ("tag: "+tag+"\nhtml: "+$(node).html());
			break;
	}
	tag="";
	return rta;
}
/**
* Esta función sirve para guardar la pagina que se ve como documento a parte, en distintos formatos.
* Si existe un bloque (<div>) con id=exportar, sólo se coge el contenido de este bloque.
* Sino, se coge el del bloque 'main'.
*
* Se puede poner el nombre del archivo en el atributo 'title' de cualquier bloque con id=span_exportar.
* ej:
*		<span id="span_exportar"  title="cabecera"></span>
*
*/
var rec=0;
function fnjs_exportar(formato){
	var txt="";
	rec=0;
	if ($('#exportar').length) { 
		bloque='#exportar';
	} else {
		bloque='#main'; 
	}
	if ($('#span_exportar').length) { 
		var titulo=$('#span_exportar').attr('title');
		var orientation=$('#span_exportar').attr('orientation');
	} else {
		var titulo=''; 
		var orientation='';
	}
	if (!formato) { bloque='excel'; }
	
	// para particularizar según la pagina
	var ref=$(bloque).attr('refe');

	if(!(export_modo=$(bloque).attr('export_modo'))) {
		export_modo="texto";
	}

	$(bloque+" > *").filter(":visible").each(function(i){
		txt+=fnjs_convert(this);
	});

	//return;
	//var myText=$(bloque).html();;
	
	switch (export_modo) {
		case "formulario":
			var myText=txt;
			/*
			//var myText=$(bloque).html();;
			//var o=new Object($(bloque));
			oldObject = $(bloque);
			//var o = jQuery.extend({}, oldObject);
			var o = oldObject.clone(true);
			//quitar los div class=no_print 
			var selector="no_print";
			var myText=o.html();
			if (formato!='html') {
				//quitar los div
				myText=myText.replace(/<div.*>/,''); 
				myText=myText.replace(/<\/div>/,''); 
			}
			//quitar los img 
			myText=myText.replace(/<img.+?>/,''); 
			//quitar los forms 
			myText=myText.replace(/<form.+?>/,''); 
			myText=myText.replace(/<\/form>/,''); 

			//coger el valor de los inputs.
			var selector=bloque+" input";
			$(selector).each(function(i,elemento) {
				var id_n=elemento.id;
				var val_n=elemento.value;
				var val_type=elemento.type;
				var val_name=elemento.name;

				if (val_type=="checkbox") {
					if (id_n) {
						re_str='<input.+?id=\"'+id_n+'.*?>';
					} else {
						re_str='<input.+?name=\"'+val_name+'.*?>';
					}
				} else {
					if (val_type=="hidden" || val_type=="button" || val_type=="submit" || val_type=="reset") {
						val_n="";
						re_str='<input.+?type=\"'+val_type+'.*?>';
					} else {
						re_str='<input.+?name=\"'+val_name+'.*?>';
					}
				}
				if (val_type=="radio" ) {
					var val_n=$('#'+id_n).prop('checked');
					re_str='<input.+?id=\"'+id_n+'.*?>(.+?)<';
					if (val_n) {
						val_n="$1<";
					} else {
						val_n='<';
					} 
				}
				re = new RegExp(re_str,'i');
				myText=myText.replace(re,val_n); 
			} );
			//coger el valor de los select.
			var selector=bloque+" select";
			$(selector).each(function(i) {
				var id_n=$(this).attr('id');
				var val_n=$('#'+id_n+' :selected').text();
				//alert ("select: "+id_n+"  "+val_n);
				re_str='<select.+?id=\"'+id_n+'.+?><option.+?>.+?</option></select>';
				re = new RegExp(re_str);
				myText=myText.replace(re,val_n); 
			} );
		
			myText='<body>'+myText+'</body>';
			console.log(myText);
			var Text=$(bloque).html();
			console.log("--------\n");
			console.log(Text);
			*/
			break;
		case "texto":
			if (formato=='html') {
				var myText=$(bloque).html();
			} else {
				var myText=txt;
			}
			break;
	}
	txt="<div>"+txt+"</div>";
	//alert ("rta:\n"+txt);

	//var parametros='formato='+formato+'&ex=operario';
	$('#frm_export_orientation').val(orientation);
	$('#frm_export_ref').val(ref);
	$('#frm_export_titulo').val(titulo);
	$('#frm_export_modo').val(export_modo);
	$('#frm_export_tipo').val(formato);
	$('#frm_export_ex').val(myText);
	$('#frm_export').trigger("submit");
}
</script>