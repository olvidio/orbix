<?php
/*
   importante para que el navegador entienda que lo que sigue es javascrip, ja que 
	la extension del fichero no es ".js", sino ".js.php"
*/
header('Content-Type: text/javascript; charset=UTF-8');
?>
<!-- Esta funcion comprueba si un número cumple con la notación de moneda: ######.## --> 
<!-- Sólo acepta una coma (o punto) para los valores decimales, no para los miles... --> 
<!-- La función parseInt da un error si empieza por 0 (y el siguiente número no entra en el rango Octal 0-7), -->
function fnjs_comprobar_dinero(id) {
  var num = $(id).val();
  if (num=="") return false;
	if(/,/.test(num)) {
  		num=num.replace(',','.');
  		$(id).val(num);
	}
		
	if(!/^(\d+)[,.]?\d{0,2}$/.test(num) && num.length) {
		var txt1 = '<?= _('formato no válido') ?>';
		var txt2 = '<?= _('se adminte un separador para los decimales (máximo 2)') ?>';
		var txt3 = '<?= _('No se admite separador para los miles') ?>';
		var txt4 = '<?= _('ejemplo: 1254.56') ?>';
		alert(txt1+'\n'+txt2+'\n'+txt3+'\n'+txt4);
	}
}

<!-- Esta funcion comprueba si una v_fecha es correcta --> 
<!-- Esta funcion necesita que se le envie la v_fecha con formato d/m/y o dd/mm/yyyy --> 
<!-- Ahora tambien acepta: d-m-y o dd-mm-yyyy --> 
<!-- La función parseInt da un error si empieza por 0 (y el siguiente número no entra en el rango Octal 0-7), -->
<!-- porque interpreta que es un valor en Octal. Se pone ",10" para indicar que es un valor decimal. -->
function fnjs_comprobar_fecha(id) {
  calDate = new Date();
  var year  = calDate.getFullYear();
  var month  = calDate.getMonth()+1; // sumo 1 porque empieza a contar en 0 (enero)

  str_fecha=$(id).val();
  if (str_fecha=="") return false;
  name_fecha=$(id).attr('name');
  str_fecha=str_fecha.replace(/\-/g,"/");
  v_fecha=str_fecha.split("/");
  if(v_fecha.length==3)
  {
     if(parseInt(v_fecha[0],10)>31 || (parseInt(v_fecha[0],10)<1))
     {
        alert(name_fecha+': <?= _("el dia no es correcto"); ?>');
		$(id).focus();
        return false;
     }
   
     if(parseInt(v_fecha[1],10)>12 || (parseInt(v_fecha[1],10)<1))
     {
        alert(name_fecha+': <?= _("el mes no es correcto"); ?>');
		$(id).focus();
        return false;
     }
     // Con esto compruebo que esté correctamente formada y verifico años bisiestos.
     var mi_v_fecha = new Date(parseInt(v_fecha[2],10),parseInt(v_fecha[1],10)-parseInt(1,10),parseInt(v_fecha[0],10))
     if(parseInt(v_fecha[0],10)!=parseInt(mi_v_fecha.getDate()))
     {
        alert(name_fecha+': <?= _("La fecha introducida NO es correcta"); ?>');
		$(id).focus();
        return false;
     }
	 $(id).val(str_fecha); // por si he cambiado los "-" por "/".
     return true;
  } else {
  	  // pruebo de poner el mes y el año actual si sólo tengo el dia
  	 if (v_fecha.length==1 && v_fecha[0]) {
  	 	dia=parseInt(v_fecha[0],10);
  	 	if(isNaN(dia) || dia>31 || (dia<1)) {
  	 		alert(name_fecha+': <?= _("El formato debe ser dd/mm/yyyy"); ?>');
		 	$(id).focus();
		 	return false;
  	 	}
		$(id).val(v_fecha[0]+"/"+month+"/"+year);
		$(id).focus();
		return true;
  	 } else {
	     alert(name_fecha+': <?= _("El formato debe ser dd/mm/yyyy"); ?>');
		 $(id).focus();
		 return false;
	 }
  }
}
/* Igual que la anterior, pero en vez de actualizar el objeto, devuelve un string con el valor de la fecha */
function fnjs_comprobar_fecha_val(id) {
  calDate = new Date();
  var year  = calDate.getFullYear();
  var month  = calDate.getMonth()+1; // sumo 1 porque empieza a contar en 0 (enero)
  
  str_fecha=$(id).val();
  if (str_fecha=="") return false;
  name_fecha=$(id).attr('name');

  str_fecha=str_fecha.replace(/\-/g,"/");
  v_fecha=str_fecha.split("/");
  if(v_fecha.length==3)
  {
     if(parseInt(v_fecha[0],10)>31 || (parseInt(v_fecha[0],10)<1))
     {
        alert(name_fecha+': <?= _("el dia no es correcto"); ?>');
		$(id).focus();
        return false;
     }
   
     if(parseInt(v_fecha[1],10)>12 || (parseInt(v_fecha[1],10)<1))
     {
        alert(name_fecha+': <?= _("el mes no es correcto"); ?>');
		$(id).focus();
        return false;
     }
     // Con esto compruebo que esté correctamente formada y verifico años bisiestos.
     var mi_v_fecha = new Date(parseInt(v_fecha[2],10),parseInt(v_fecha[1],10)-parseInt(1,10),parseInt(v_fecha[0],10))
     if(parseInt(v_fecha[0],10)!=parseInt(mi_v_fecha.getDate()))
     {
        alert(name_fecha+': <?= _("La fecha introducida NO es correcta"); ?>');
		$(id).focus();
        return false;
     }
	 var rta=str_fecha; // por si he cambiado los "-" por "/".
     return rta;
  } else {
  	  // pruebo de poner el mes y el año actual si sólo tengo el dia
  	 if (v_fecha.length==1 && v_fecha[0]) {
  	 	dia=parseInt(v_fecha[0],10);
  	 	if(isNaN(dia) || dia>31 || (dia<1)) {
  	 		alert(name_fecha+': <?= _("El formato debe ser dd/mm/yyyy"); ?>');
		 	$(id).focus();
		 	return false;
  	 	}
		var rta=v_fecha[0]+"/"+month+"/"+year;
		return rta;
  	 } else {
	     alert(name_fecha+': <?= _("El formato debe ser dd/mm/yyyy"); ?>');
		 $(id).focus();
		 return false;
	 }
  }
}
/**
  * Función para comprobar que el campo hora està bien escrito.
  *
  */

function fnjs_comprobar_hora(id) {
  if ($(id).val()=="") return true;
  v_fecha=$(id).val().split(":");
  if(v_fecha.length==2 || v_fecha.length==3 )  {
     if(parseInt(v_fecha[0],10)>24 || (parseInt(v_fecha[0],10)<1))
     {
        alert("<?= _("la hora no es correcta"); ?>");
		$(id).focus();
        return false;
     }
   
	 var M=parseInt(v_fecha[1],10);
     if((M>60) || (M<0))
     {
        alert("<?= _("los minutos no son correctos"); ?>"+M);
		$(id).focus();
        return false;
     }
     return true;
  } else {
  	  // pruebo de poner los minutos a 0
  	 if (v_fecha.length==1 && v_fecha[0]) {
  	 	if(parseInt(v_fecha[0],10)>24 || (parseInt(v_fecha[0],10)<1)) {
  	 		alert("<?= _("El formato debe ser hh:mm"); ?>");
		 	$(id).focus();
		 	return false;
  	 	}
		var rta=v_fecha[0]+":00";
		$(id).val(rta);
		return true;
  	 } else {
	     alert("<?= _("El formato debe ser hh:mm"); ?>");
		 $(id).focus();
		 return false;
	 }
  }
}
