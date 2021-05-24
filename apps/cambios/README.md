# cambios y avisos

(problemas con este módulo han llevado a la reconsideración de todo el tema de los procesos).

Aunque parece que los objetos a los que se hace referencia (conjunto de datos) son los mismos que en los permisos, no es así. En los permisos se hace referencia a conceptos que pueden englobar más de un objeto (o menos). Esta información se usa a nivel de programa para dejar hacer o no alguna cosa. En los avisos, los objetos se refieren directamente a la base de datos: si ha cambiado este registro. El programa de avisos informa de los cambios sufridos en la base de datos, con independencia del proceso que los haya provocado. 

Para generar el aviso, se emplea el mismo criterio que en los permisos. Se define una fase de referencia, si está marcada se avisa. Se mantiene la opción de poder definir que avise si no está marcada, que a primera vista no parece lógica, pero quizá podría usarse en algún caso. Por ejemplo si estoy preparando las actividades de sg, quiero que me avise si alguien cambia algo, pero una vez aprobadas ya no quiero que me avise (ya he hecho mi trabajo y ahora depende de otros).

Una vez determinado que hay que avisar según el criterio de la fase, se comprueba que el usuario tiene permiso para ver esos datos. Si no tiene permiso no se avisa.

Se ha añadido un concepto adicional: "Avisar si ha finalizado la actividad". Por defecto desmarcado. Si se marca, se avisa aunque el cambio tenga una fecha posterior a la de fin de la actividad.


Proceso del programa
==================== 

Anotar cambios
--------------
Cuando se hace un cambio se anota en: 'av_cambios_dl' (sincronizada en la instalacion exterior). En el caso de actividades publicadas en dl que no tengan el módulo de cambios instalado, también se anotan en la tabla 'public.av_cambios' (con id_schema = 3000 que no pertenece a nadie).

Actualmente se anotan cambios en:

- ActividadCargo, ActividadCargoSacd

- ActividadDl, ActividadEx
			// Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
			// Anoto el cambio si la actividad está publicada

- CentroEncargado

- AsistentePub, AsistenteOut
			// Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
			// Anoto el cambio si o si.

- ActividadProcesoTarea

=> Para el servidor exterior a la hora de sincronizar 'av_cambios_dl' ('av_cambios' no hace falta porque sólo se modifica desde dentro) hay un problema de simultaniedad: si se inserta una fila a la vez en el servidor1 y en el 2, cojen el mismo valor de la sequencia (que es la clave primaria), y al sincronizar sólo queda una fila. Para evitarlo se genera un id_item distinto en cada servidor, de manera que no sea posible conflicto: los 'id_item_cambio' del servidor1 empiezan por 1, los del servidor2 por 2.

Los valores default son:

servidor1: 
>id_item_cambio > (((1)::text || (nextval('"H-dlb".av_cambios_dl_id_item_cambio_seq'::regclass))::text))::integer

servidor2: 
>id_item_cambio > (((2)::text || (nextval('"H-dlb".av_cambios_dl_id_item_cambio_seq'::regclass))::text))::integer

y lógicamente no se sincroniza la sequencia (av_cambios_dl_id_item_cambio_seq): cada uno tiene la suya.


Generar Avisos
--------------
En algún momento se ejecuta el programa 'aviso_generar_tabla.php'

- desde el menu ('generar tabla' y 'ver avisos')
- avisos_generar_mails
- Cambio,cambioDl (insert) (av_cambios, av_cambios_dl)
- desde cron

Para poder llamar por cron, hay que pasar las variables de sessión en la linea de comandos:
>$command = "nohup /usr/bin/php $program $username $pwd $dirweb $doc_root $ubicacion $esquema_web $private $db_server >> $out 2>> $err < /dev/null &";
	
	$program = ConfigGlobal::$directorio.'/apps/cambios/controller/avisos_generar_tabla.php';
	$username;
	$password;
	$dir_web = orbix | pruebas;
	document_root = /home/dani/orbix_local
	$ubicacion = 'sv'|'sf'
	$esquema_web = 'H-dlbv'...
	$private = 'sf'; para el caso del servidor exterior en dlb. puerto distinto.
	$DB_SERVER = 1 o 2; para indicar el servidor dede el que se ejecuta. (ver comentario en clase: CambioAnotado)

Al no funcionar de manera interactiva, las salidas del programa se guardan en:

    $err = ConfigGlobal::$directorio.'/log/avisos.err';
    $out = ConfigGlobal::$directorio.'/log/avisos.out';

Este programa, busca los cambios que no se han anotado y después de compararlo con las preferencias de los usuarios y en su caso anotar los cambios para avisar al usuario, se marcan como anotados.

=> Las preferencias de los usuarios están cada una en su base de datos (sv o sf). El programa que se ejecuta lo hace con un usuario de una sección, y sólo tiene acceso a "sv y comun" o "sf y comun". Por tanto la marca de anotado de sv no sirve para sf. Sólo va a avisar a los de su sección (no tiene acceso a las preferencias de los usuarios de la otra sección). Por esto en el crontab hay dos lineas, una para avisar a sv (horas y 15') y otra para avisar a sf (horas y 20').

Tablas de preferencias de los cambios para cada usuario.

	av_cambios_usuario_objeto_pref
	av_cambios_usuario_propiedades_pref

En un inicio, la tabla 'av_cambios_anotados', tenia dos campos: 'anotado_sv' y 'anotado_sf'.
Al poner un servidor externo y tener la tabla sincronizada, no funciona bien pues cuando se generan avisos masivamente, tarda un tiempo en sincronizarse, y puede suceder que desde el otro servidor también se generen avisos, generando nuevos registros, que en su momento impedirán la sincronización porque la clave es la misma ($iid_schema_cambio, $iid_item_cambio).
Se ha intentado añadir un nuevo campo en la clave primaria ($server) que indique el servidor desde el que se ha generado. Pero sigue dando algún problema.

Finalmente se han creado dos tablas: 'av_cambios_anotados' y 'av_cambios_anotados_sf'. En la instalación interior está 'av_cambios_anotados'. En la instalación exterior está 'av_cambios_anotados' (sincronizada) y 'av_cambios_anotados_sf'.


El programa añade los avisos en una única tabla 'av_cambios_usuario'(comun) y se marcan los cambios como anotados en 'av_cambios_anotados' o 'av_cambios_anotados_sf' según corresponda.

Ver avisos
----------
El último paso del proceso es que el usuario vea los cambios. Esto se puede hacer mediante una lista o por mail. En ambos casos se cojen las filas de GestorCambioUsuario con avisado=FALSE.

- Para ver por lista, se ejecuta 'avisos_generar.php'. Ni se eliminan ni se marcan como avisados. Se pueden eliminar desde el listado, seleccionándolos, o por fecha.

- Para enviar mails se ejecuta 'avisos_generar_mails.php'. Una vez enviados se eliminan.

==>> Parece que el campo de 'avisado' no se utiliza. ?????

En la instalacion (apache) se añade la variable de entorno: DB_SERVER que puede ser:

	1 -> para la instalacion de sv (interna)
	2 -> para la instalación exterior (conexión con mail).