<?php
namespace core;

// INICIO Cabecera global de URL de controlador *********************************
use web\Hash;

require_once ("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$pruebas = 0;
if (ConfigGlobal::$web_path === '/pruebas' || ConfigGlobal::$web_path === '/pruebassf') {
  $fondo_claro="aquamarine";
  $pruebas = 1;
}

include_once(ConfigGlobal::$dir_estilos.'/todo_en_uno.css.php');

$aQuery = [ 'pau' => 'a' ];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$goMisas = Hash::link('apps/misas/controller/misas_index.php?' . http_build_query($aQuery));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="es">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>index</title>
	<script type="application/javascript">
	function fnjs_windowopen(url) { //para poder hacerlo por el menu
        let parametros = '';
        window.open(url+'?'+parametros);
	}
	</script>
  </head>
  <body class="otro">
  <?php if ($pruebas === 1) { ?>
      <h1>Entorno de pruebas</h1>
  <?php } ?>
  <p>OCTUBRE 2022: se ha añadido una dl fantasma ('Otra dl de aquinate') para poder introducir actividades que organiza
      otra dl que si está en aquinate, pero que no las va a introducir en su dl
      (por ejemplo actividades de sg, cuando no se usa para sg)</p>
<?php
  include_once('./regionesEnOrbix.html');
?>
  <br>
    <h1>Tutoriales</h1>
    <br>
    <h2><a href="#uso_general">1. Uso en general</a></h2>
    <h2><a href="#gestion_de_ca">2. Gestión de ca</a></h2>
    <h2><a href="#gestion_de_ca">3. Gestión de personas</a></h2>
    <h2><a href="#gestion_de_ctr">4. Gestión de centros</a></h2>
    <h2><a href="traducciones.php">x. Traducciones</a></h2>
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
    Ctr + Clic. Si se mantiene apretada la tecla 'Control' se puede hacer clic en más de una fila.<br>
    Mays + Clic. Si se mantiene apretada la tecla 'Mays' al hacer clic se selecciona todas las filas entre el primer y segundo click.<br>
    ATENCIÓN:<br>
    * para exportar (y quizá alguna otra cosa),&nbsp; primero hay que
    visualizar todas las filas. Explicación: si la lista es larga, el
    navegador no tiene todas las filas, las va pidiendo conforme las
    necesita visualizar. Una vez se ha visualizado todas, ya están en la memoria del navegador y
    se pueden exportar.<br>
    * Al ordenar tiene algún problema con los acentos.<br>
    <br>
    <h4> 1.3. Desplegables</h4>
    Recordar que apretando una tecla (o escribiendo más de una, pero sin
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
    En el caso de los procesos es más rígido. En las actividades, quien
    tiene el control es dre. Sólo entrando como dre se pueden borrar
    actividades.<br>
    <h4> 1.8. Salir</h4>
    Es importante usar el botón de "Salir" para cerrar la sesión. El
    servidor provoca un error cuando se excede un número determinado de
    sesiones abiertas y apagando el navegador no se cierra la session.<br>
    
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
        <h4>asistentes dl a ca <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/03-asistentesDl.mp4')">(video)</span></h4>
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
      <li>
        <h4>exportar tessera a archivo <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/13-tessera-curriculum.mp4')">(video)</span></h4>
      </li>
    </ol>
	 <br><br><br> 
    <h2><a name="gestion_de_personas"></a>3. Gestión de personas<br>
    </h2>
	<ol>
      <li>
        <h4>Nueva persona <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/31-nuevaPersona.mp4')">(video)</span></h4>
      </li>
      <li>
        <h4>crear / Modificar nota <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/32-modificarNota.mp4')">(video)</span></h4>
      </li>
	</ol>
	 <br><br><br> 
    <h2><a name="gestion_de_ctr"></a>4. Gestión de ctr<br>
    </h2>
	<ol>
      <li>
        <h4>Crear/modificar un nuevo centro <span class="link" onClick="fnjs_windowopen('<?= ConfigGlobal::getWeb_public() ?>/ayuda/41-modificarCtr.mp4')">(video)</span></h4>
      </li>
	</ol>
  </body>
</html>
