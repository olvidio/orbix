# Procesos- intento desde app GitHub--- y ahora modificado desde Eclipse pero subido desde app GitHubb- y ahora intendo desde Eclipse

Este módulo sirve para describir las acciones que hay que realizar sobre una actividad con el fin de facilitar el trabajo de las oficinas al poder hacer consultas tipo: actividades que falta poner sacd, o: actividades que falta matricular a los alumnos etc.

Además este módulo está pensado para facilitar el uso de los permisos y avisos, posibilitando limitar estos en función de las fases del proceso en que se encuentra una actividad.

Se define un proceso para cada tipo de actividad.

Un proceso consta de una serie de fases. Se ha descartado la perspectiva de que tengan un orden, dado que el proceso no tiene porqué ser lineal, puede avanzar por varias líneas en paralelo. Esto implica que no existe una única fase actual que determine la posición de la actividad, sino que la actividad tiene varias fases completadas. El orden de las fases dentro del proceso viene determinado por las dependencias. Una fase depende de otra (previa), y esto obliga a que no se pueda marcar la fase hasta que lo esté la previa.

OJO: cuando se accede actividades ya existentes, hay que intentar conservar el status que tenga. Para las actividades anteriores (antes de instalar el módulo de procesos), se marcarán todas las fases del status. Para las posteriores, sólo la primera fase del status. Vamos a establecer la fecha de hoy como criterio para distinguir entre actividades anteriores y posteriores.

## definición

Se pueden definir de manera independiente los siguientes conceptos:

1. tipo de proceso. Es el nombre que damos a un proceso. Debemos especificar si pertenece a sv o sf.
2. fases. El nombre que damos a una fase, y si puede pertenecer a sv o sf o ambas. Las fases no están ligadas a nada a fin de poder ser usadas en diferentes procesos.
3. tareas (fase-tareas). Las tareas si que están ligadas obligatoriamente a una fase. El propósito de las tareas es el de subdividir la fase en varios estadios intermedios. La idea es que pueda servir como checklist para dicha fase y facililtar el trabajo de la oficina. Las tareas no tienen ningún efecto sobre los permisos ni los avisos.
4. proceso. A un determinado tipo de proceso, se le añaden un serie de fases generando el proceso.
5. para cada tipo de actividad hay que especificar el tipo de proceso. Esto permite que al crear una actividad, se genere su proceso copiando el proceso tipo especificado. Como en general los procesos van a ser distintos si es una actividad de la dl o no, por cada tipo de actividad se puede especificar un tipo de proceso para las actividades de la dl y otro para las que no.

## generación de un proceso

Al añadir una fase al proceso hay que especificar:
1. La fase. Opcionalmente también la tarea.
2. El status de la actividad (proyecto, actual, terminada) en el cual tiene lugar. 
3. La oficina responsable (texto debe estar bien escrito). Sólo pueden marcar-desmarcar esta fase la oficina responsable. Si se deja en blanco, cualquier oficina puede realizar el cambio.
4. Fase previa. Opcionalmente también una tarea previa. No se puede marcar la fase si no está marcada la fase previa. 
5. mensaje requisito. Es el mensaje de error que sale cuando se intenta marcar una fase y la fase previa no está marcada.
    
- Sólo la oficina de "des" tiene permiso para pasar de estado proyecto a actual.
- Excepto la primera fase del proceso, el resto debería tener una fase previa. Por ejemplo todas las fases que correspondan al estado "actual" de la actividad deberían tener como fase previa (si son independientes) la fase de "Aprobada" que es la que pasa la actividad de proyecto a actual y sólo puede "des".

## Permisos

Cada permiso hace referencia a un conjunto de datos (datos de la actividad, asistencias, cargos etc.). Se ha determinado que para cada uno de estos conjuntos debe existir una fase del proceso que actúa como punto de inflexión: es decir antes de este punto se tiene un permiso, después de este punto se tiene otro.

Por ejemplo para el caso del acceso a los datos de la actividad, la fase de inflexión es "aprobada". Se define un permiso para cuando la fase "aprobada" no está marcada (para oficinas = modificar, para sacd = nada) y otro para cuando la fase está marcada (oficinas = ver, sacd = ver). Parece que con esto podría ser suficiente. Otro ejemplo: para asistentes, hay que tener una fase de "ok asistentes". Sin marcar (para oficinas = modificar, para sacd = nada), marcada (oficinas = ver, sacd = ver).

De esta manera el estado del resto de las fases no influye para determinar el permiso.
En el caso de no existir dicha fase en el proceso, la casilla consta como no marcada, y por tanto se aplican los permisos dados al "sin marcar". 

A cada permiso se le debe indicar a qué tipo de actividad se aplica. Se puede indicar un tipo de actividad genérico ("sv n" por ejemplo).

Los permisos posibles por orden de menos a más (un permiso incluye todos los anteriores) son:
1. Nada.
1. Ocupado.
1. Ver.
1. Modificar.
1. Crear.
1. Borrar.

Para definir un permiso hay que especificar:
1. Si la actividad es  de la dl o no.
1. El tipo de actividad. El tipo de actividad puede ser genérico.
1. A que afecta:
    - datos.
    - dossiers económicos.
    - atención sacd (sacd que atienden la actividad).
    - ctr encargados.
    - tarifas.
    - cargos (resto de cargos de la actividad que no son sacd).
    - asistentes.
1. Fase de referencia. En Esta fase se deben definir dos permisos: Uno para la fase sin mardcar (PermisoOff) y otro para la fase marcada (PermisoOn).

- prioridades en los permisos:

  Cuanto más definido está el tipo de actividad mayor preferencia tiene el permiso. Si una actividad es del tipo "sv n crt o-fl", se busca si existe un permiso definido para este tipo. Si no existe se mira para el nivel superior: "sv n crt". Si no existe nada, se mira el siguiente nivel: "sv n". etc. Cuando se encuentra el primer permiso ya no se mira en los siguientes niveles (sea para usuario o grupo).
 
  Los permisos pueden asignarse a usuarios o a grupos. Los usuarios tienen preferencia sobre los grupos. La preferencia entre los grupos es aleatoria, por tanto hay que procurar dar permisos que no se pisen. Pro ejemplo al grupo "ofina_agd" se le puede asignar permiso para borrar en "sv agd" y para ver en "sv". Al grupo "oficina_n" permiso para borrar en "sv n" y para ver en "sv". De esta manera el unico tipo de actividad común a las dos oficinas (sv) tiene idéntico permiso, y los otros tipos de actividad sólo están en en un grupo.

## instalar el módulo.


Sólo ha servido al modificar las tablas y programas. Para eliminar todo lo referente a procesos y volver a empezar de cero.
El proceso de crear las tablas, para cuando existe la instalación en dos servidores con tablas sincronizadas por bucardo.

1. bucardo stop
1. borrar tablas anteriores.
1. en sv: crear tablas global
1. en sv: crear tablas esquema
1. en sv: llenar tablas esquema
1. en sf: crear tablas global
1. en sf: crear tablas esquema
1. en sf: llenar tablas esquema
1. buacrdo start

 
## Avisos

Se requiere tener instalado el módulo correspondiente.

