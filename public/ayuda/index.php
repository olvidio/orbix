<?php
namespace core;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(ConfigGlobal::$dir_estilos.'/todo_en_uno.css.php');
include_once(ConfigGlobal::$dir_estilos.'/menu_horizontal.css.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>index</title>
	<script>
	function fnjs_windowopen(url) { //para poder hacerlo por el menu
		var parametros='';
		window.open(url+'?'+parametros);
	}
	</script>
  </head>
  <body class="otro">
    <h1>Tutoriales</h1>
    <br>
    <h2><a href="#uso_general">1. Uso en general</a></h2>
    <h2><a href="#gestion_de_ca">2. Gestión de ca</a></h2>
    <h2><a href="#gestion_de_ctr">3. Gestión de centros</a></h2>
    <h2><a href="public/ayuda/traducciones.php">3. Traducciones</a></h2>
	<br> 
	<br> 
	<br> 
	<br> 
    <h2><a name="uso_general"></a>1. Uso en general</h2>
    <br>
    Aspectos aplicables en todo el entorno web<br>
    <br>
    <h4><b> 1.1. Preferencias</b></h4>
    <h4> 1.2. Tabla slickGrid <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/slickgrid.mp4')">(video)</span></h4>
    Es la presentación de resultados por defecto. Se puede cambiar a la
    vista de tabla normal (html) en el menu de preferencias.<br>
    Posibilidades:<br>
    - cambio de tamaño<br>
    - esconder columnas<br>
    - cambiar el ancho de las columnas<br>
    - cambiar el orden de las columnas<br>
    - ordenar por una columna<br>
    - buscar dentro de la grid<br>
    - guardar la configuración (para cada página web es distinta)<br>
    - Selección multiple: En algunos casos se puede seleccionar más de una fila para hacer 
    alguna operación.<br>
    Ctr + Click. Si se mantiene apretada la tecla 'Control' se puede hacer click en más de una fila.<br>
    Mays + Click. Si se mantiene apretada la tecla 'Mays' al hacer click se selecciona todas las filas entre el primer y segundo click.<br> 
    ATENCIÓN:<br>
    * para exportar (y quizá alguna otra cosa),&nbsp; primero hay que
    visualizar todas las filas. Explicación: si la lista es larga, el
    navegador no tiene todas las filas, las va pidiendo conforme las
    necesita visualizar. Una vez se ha visualizado todas, ya estan en la memoria del navegador y
    se pueden exportar.<br>
    * Al ordenar tiene algún problema con los acentos.<br>
    <br>
    <h4> 1.3. Desplegables</h4>
    Recordar que apretando una tecla (o esribiendo más de una pero sin
    pausa) va al item de la lista que empieza por la letra que tecleamos.<br>
    <h4> 1.4. Buscar en la página</h4>
    Para buscar en toda la página, Ctrl+f permite buscar una cadena, con
    Ctrl+g se busca la siguiente.<br>
    * en slickgrid deben de haberse visualizado antes (mejor usar la
    búsqueda propia de la grid)<br>
    <h4> 1.5. Imprimir</h4>
    Hay páginas web que están pensadas para imprimir directamente desde
    el navegador. En este caso hay que decirle al navegador que no ponga
    cabecera ni pie en la página.<br>
    <h4> 1.6. Exportar</h4>
    <h4> 1.7. Permisos</h4>
    Afectan a dos cosas: menús y procesos.<br>
    En el caso de los menús se pueden adaptar a cada caso, aunque
    entiendo que lo mejor de momento es dejarlo como viene por defecto.<br>
    En el caso de los procesos es más rigido. En las actividades, quien
    tiene el control es dre. Sólo entrando como dre se pueden borrar
    actividades.<br>
    <h4> 1.8. Salir</h4>
    Es importante usar el boton de "Salir" para cerrar la sessión. El
    servidor provoca un error cuando se excede un número determinado de
    sessiones abiertas y apagando el navegador no se cierra la session.<br>
    
   <br><br><br> 
    <h2><a name="gestion_de_ca"></a>2. Gestión de ca<br>
    </h2>
    <ol>
      <li>
		  <h4>Nuevo ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/01-nuevoCa.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>Asignaturas de ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/02-asignaturas.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>asistentes dl a ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/03-asitentesDl.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>asistentes otras dl a ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/04-asistentesOtrDl.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>matriculaciones asignaturas ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/05-matriculaciones.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>actas <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/06-actas.mp4')">(video)</span></h4>
      </li>
	  <li>
        <h4>ca posibles por ctr <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/07-caPosiblesPorCentro.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>opciones ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/08-opcionesCa.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>gestión de plazas de calendario <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/09-gestionPlazasCalendario.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>importar ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/10-importarCa.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>publicar ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/11-publicarCa.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>imprimir situación académica <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/12-verTessera.mp4')">(video)</span></h4>
      </li>
    </ol>
	 <br><br><br> 
    <h2><a name="gestion_de_ctr"></a>3. Gestión de ctr<br>
    </h2>
	<ol>
      <li>
        <h4>Crear/modificar un nuevo centro <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/13-modificarCtr.mp4')">(video)</span></h4>
	  </li>
	</ol>
  </body>
</html>
