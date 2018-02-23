<?php
namespace core;
use web;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(ConfigGlobal::$dir_estilos.'/todo_en_uno.css.php');
echo "<style>";
include_once(ConfigGlobal::$dir_estilos.'/menu_horizontal.css.php');
echo "</style>";

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
    <h2><a href="#uso_general">Uso en general</a></h2>
    <h2><a href="#gestion_de_ca">Gestión de ca</a></h2>
	<br> 
	<br> 
	<br> 
	<br> 
    <h2><a name="uso_general"></a>Uso en general</h2>
    <br>
    Trata aspectos aplicables en todo el entorno web<br>
    <br>
    <h4><b> 1.Preferencias</b></h4>
    <h4> 2.Tabla slickGrid <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/slickgrid.mp4')">(video)</span></h4>
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
    ATENCIÓN:<br>
    * para exportar (y quizá alguna otra cosa),&nbsp; primero hay que
    visualizar todas las filas. Explicación: si la lista es larga, el
    navegador no tiene todas las filas, las va pidiendo conforme las
    necesita visualizar. Una <br>
    vez se ha visualizado todas, ya estan en la memoria del navegador y
    se pueden exportar.<br>
    * Al ordenar tiene algún problema con los acentos.<br>
    <br>
    <h4> 3. Desplegables</h4>
    Recordar que apretando una tecla (o esribiendo más de una pero sin
    pausa) va a<br>
    <h4> 4. Buscar en la página</h4>
    Para buscar en toda la página, Ctrl+f permite buscar una cadena, con
    Ctrl+g se busca la siguiente.<br>
    * en slickgrid deben de haberse visualizado antes (mejor usar la
    búsqueda propia de la grid)<br>
    <h4> 5. Imprimir</h4>
    Hay páginas web que están pensadas para imprimir directamente desde
    el navegador. En este caso hay que decirle al navegador que no ponga
    cabecera ni pie en la página.<br>
    <h4> 6. Exportar</h4>
    <h4> 7. Permisos</h4>
    Afectan a dos cosas: menús y procesos.<br>
    En el caso de los menús se pueden adaptar a cada caso, aunque
    entiendo que lo mejor de momento es dejarlo como viene por defecto.<br>
    En el caso de los procesos es más rigido. En las actividades, quien
    tiene el control es dre. Sólo entrando como dre se pueden borrar
    actividades.<br>
    <h4> 8. Salir</h4>
    Es importante usar el boton de "Salir" para cerrar la sessión. El
    servidor provoca un error cuando se excede un número determinado de
    sessiones abiertas y apagando el navegador no se cierra la session.<br>
    <h2><a name="gestion_de_ca"></a>Gestión de ca<br>
    </h2>
    <ol>
      <li>
		  <h4>Nuevo ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/01-nuevoCa.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>Asignaturas de ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/02-asignaturas.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>asistentes dl a ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/03-asitentesDl.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>asistentes otras dl a ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/04-asistentesOtrDl.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>matriculaciones asignaturas ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/05-matriculaciones.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>actas <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/06-actas.mp4')">(video)</span></h4>
      </li>
	  <li>
        <h4>ca posibles por ctr <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/07-caPosiblesPorCentro.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>opciones ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/08-opcionesCa.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>gestión de plazas de calendario <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/09-gestionPlazasCalendario.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>importar ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/10-importarCa.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>publicar ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::$web_public ?>/ayuda/11-publicarCa.mp4')">(video)</span></h4>
      </li>
    </ol>
  </body>
</html>