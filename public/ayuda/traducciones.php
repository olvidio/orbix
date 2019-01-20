<p>Existe una plantilla con todas las frases a traducir en:
	orbix/languages/es_ES.pot
</p>
Para cada idioma exite una carpeta:
en_US.UTF-8/LC_MESSAGES

con dos archivos: "orbix.po" y "orbix.mo" (se debe llamar orbix)
Sólo hay que editar "orbix.po".

<ol>

<li>Cuando ha habido cambios en el programa hay que actualizar primero la plantilla (orbix/languages/es_ES.pot).<br>
Para ello, abrir la plantilla con poedit, y dar a "catalogo > actualizar desde las fuentes".
</li>  
<li>Para poder traducir los menus, hay que ejecutar:<br>
 Sistema > traducciones > menus.<br>
 Esto genera un archivo de texto con el texto de los menus. Asi al generar los archivo de tracuccion (.pot) tinene en cuenta estos textos que de hecho están en la base de datos. </li>

<li>Para crear un nuevo idioma:
a) si queremos usar el google traductor, creamos el fichero .po para el spanish, y lo subimos al traductor toolkit (que es distinto del traductor). Ver más abajo.
<br>
https://translate.google.com/toolkit/list?hl=ca#translations/active
</li>
<li>Para crear actualizarb n idioma:
a) Abrir el archivo de traduccion con poedit. (orbix/languages/ca_ES.UTF-8/LC_MESSAGES/orbix.po).<br>
b) Ir a "Catálogo > Update from POT file", y seleccionar la plantilla (orbix/languages/es_ES.pot).<br>

si queremos usar el google traductor, creamos el fichero .po para el spanish, y lo subimos al traductor toolkit (que es distinto del traductor). Ver más abajo.
<br>
https://translate.google.com/toolkit/list?hl=ca#translations/active
</li>
</ol>

<h1>Poedit</h1>

<ol>
<li>En el menú “Archivo”, seleccione “Nuevo”</li>
<li>Seleccione el idioma que usó en su tema (probablemente inglés)</li>
<li>En el menú “Catálogo”, seleccione “Propiedades”</li>
<li>Ingrese la información del proyecto en la pestaña “Propiedades de traducción”</li>
<li>Vaya a la tercera pestaña “Palabras clave de fonts”</li>
<li>Haga clic en el botón “Nuevo elemento” (segundo botón) e ingrese una palabra clave y _e para cada una de sus palabras clave ( __ , _e , esc_attr_e , etc.)</li>
<li>Haga clic en el botón “Aceptar” en la parte inferior</li>
<li>En el menú “Archivo”, seleccione “Guardar como …”</li>
<li>Guarde el archivo como “yourthemename.pot” en la carpeta “languages” en su directorio de temas (asegúrese de agregar la extensión .pot al nombre del archivo porque de manera predeterminada se guardará como .po)</li>
<li>En el menú “Catálogo”, seleccione “Propiedades” nuevamente</li>
<li>Vaya a la 2da pestaña “Rutas de fonts”</li>
<li>Establezca el valor de “Ruta base” a ../ (el archivo .pot se guarda en un subdirectorio, de esta manera establece la base para el directorio principal, es decir, su directorio de temas)</li>
<li>Al lado de “Ruta”, haga clic en el botón “Nuevo elemento” e ingrese . (Esto hará que escanee su directorio de temas y sus subdirectorios)</li>
<li>Haga clic en el botón “Aceptar” en la parte inferior</li>
<li>En la ventana del proyecto, haga clic en “Actualizar” (segundo icono en la parte superior)</li>
<li>En el menú “Archivo”, haga clic en “Guardar”</li>
</ol>

<h1>Google Translator Toolkit</h1>
a la que hay que acceder con una cuenta Google.

https://translate.google.com/toolkit/list?hl=ca#translations/active

Al parecer es una herramienta para organizar traducciones, pero yo le he dado el siguiente uso: Traducir temas de WordPress o drupal o cualquier otro que utilize archivos o  ficheros .po
<ol>
<li>Una vez iniciado sesion se pulsa upload/subir<\li>
<li>Se selecciona el fichero que se pretende traducir, se indica el nombre de theme, se elige el idioma origen (normalmente ingles) y se marcan los idiomas a los que se traducirá el tema.</li>
<li>Se pulsa next/siguiente y Google nos propondrá unas ofertas para realizar la traducción, pulsamos No gracias/No thanks y volveremos a la pantalla inicial donde ahora aparece listado el tema, que hemos subido, una vez por cada idioma que hayamos elegido. Además en cada línea indicará el número de frases que contiene el fichero (words), la última modificación y si el fichero está compartido.</li>
<li>Pulsamos sobre el idioma uno de los idiomas que queramos traducir.</li>
<li>Ya tenemos las traducciones automáticas, que podemos modificar.</li>
<li>Sólo queda guardar y descargar desde la opción Archivo/File.</li>
<li>
</ol>
</body>
</html>
