# Procesos

Este módulo está pensado para facilitar el uso de los permisos y avisos, posibilitando limitar estos en función de las fases del proceso en que se encuentra una actividad.

Se define un proceso para cada tipo de actividad.
Un proceso consta de una serie de fases a las que se ha asignado un orden. Además se pueden establecer unas dependencias entre las fases. Estas dependencias impiden que se pueda marcar una fase si no está mardcada la anterior (de la que depende).

Hasta ahora para definir los permisos o avisos, se hacia referencia a las fases como parte de un proceso secuencial y por tanto bastaba dar una fase de inicio y otra de final.
Para los avisos, esto ha acabado dando situaciones difíciles de resolver.

Para los avisos se propone una nueva prespectiva en que se condidera a las fases de manera independiente. El hecho de que una fase esté marcada es suficiente para generar un aviso con independencia del resto de fases.

Esto no es compatible con los permisos. Por ejemplo: los usuarios de oficina, deben tener permiso de crear y modificar los datos de las actividades en las primeras fases, y en las porteriores solo para ver. Otros usuarios (sacd) no deben poder ver en las primeras fases y si en las posteriores.  

No parece que los criterios empleados para los permisos y para los avisos sean compatibles, por tanto se van a tratar de manera distinta:

- **permisos:** Se asigna un permiso a cada fase del proceso. Para saber el permiso en un momento dado, se mira cuál es la última fase marcada según el orden del proceso. Esta se considera la fase actual de la actividad y se utilizan los permisos asignados a esta fase.

  Anteriormente se indicaba la fase inicial del permiso y la fase final. Esto se ha cambiado y ahora hay que indicar el permiso en cada fase. Esto posibilita que al definir los permisos para un tipo de actividad genérico ("sv n" por ejemplo) se pueda indicar el permiso en todas las fases posibles (unión de las de todos los procesos) aunque no existan, o tengan otro orden, en alguno de los procesos que engloba este tipo genérico.

  En el caso de asignar por fase inicial-final, hay que limtar las fases a las comunes a todos los procesos que engloba este tipo genérico. Esto obliga a definir un número mayor de condiciones (para cada tipo de proceso distinto).  

- **avisos:** Las fases se tratan como marcas independientes del proceso. Si está marcada una fase, se avisa, sea cuál sea la situación de la actividad en su proceso.  


## definición

Se pueden definir de manera independiente los siguientes conceptos:

1. tipo de proceso. Es el nombre que damos a un proceso. Debemos especificar si pertenece a sv o sf.
2. fases. El nombre que damos a una fase, y si puede pertenecer a sv o sf o ambas. Las fases no están ligadas a nada a fin de poder ser usadas en diferentes procesos.
3. tareas (fase-tareas). Las tareas si que están ligadas obligatoriamente a una fase. El propósito de las tareas es el de subdividir la fase en varios estadios intermedios. La idea es que pueda servir como checklist para dicha fase y facililtar el trabajo de la oficina. Las tareas no tienen ningún efecto sobre los permisos ni los avisos.
4. proceso. A un determinado tipo de proceso, se le añaden un serie de fases generando el proceso.
5. para cada tipo de actividad hay que especificar el tipo de proceso. Esto permite que al crear una actividad, se genere su proceso copiando el proceso tipo especificado. Como en general los procesos van a ser distintos si es una actividad de la dl o no, por cada tipo de actividad se puede especificar un tipo de proceso para las actividades de la dl y otro para las que no.

## generación de un proceso

En un proceso si que hay un orden en las fases. Al añadir una fase al proceso hay que especificar:
1. La fase. Opcionalmente también la tarea.
2. El status de la actividad (proyecto, actual, terminada) en el cual tiene lugar. 
3. La oficina responsable (texto debe estar bien escrito). Sólo pueden marcar-desmarcar esta fase la oficina responsable. Si se deja en blanco, cualquier oficina puede realizar el cambio.
4. Fase previa. Opcionalmente también una tarea previa. No se puede marcar la fase si no está marcada la fase previa. 
5. mensaje requisito. Es el mensaje de error que sale cuando se intenta marcar una fase y la fase previa no está marcada.
    
    
- Sólo la oficina de "des" tiene permiso para pasar de estado proyecto a actual.
- Excepto la primera fase del proceso, el resto debería tener una fase previa, que no tiene porque ser la anterior en el orden. Por ejemplo todas las fases que correspondan al estado "actual" de la actividad deberian tener como fase previa (si son independientes) la fase de "Aprobada" que es la que pasa la actividad de proyecto a actual y sólo puede "des".

## Permisos

Estos permisos hacen referencia a las actividades. 

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
1. Para cada fase. Aparecen todas las fases, **por orden alfabético**. En algún proceso pueden no existir.

Para saber el permiso en un momento dado, se mira cuál es la última fase marcada según el orden del proceso. Esta se considera la fase actual de la actividad y se utilizan los permisos asignados a esta fase.

Cuanto más definido está el tipo de actividad mayor preferencia tiene el permiso. Si una actividad es del tipo "sv n crt o-fl", se busca si existe un permiso definido para este tipo. Si no existe se mira para el nivel superior: "sv n crt". Si no existe nada, se mira el siguiente nivel: "sv n". etc. Cuando se encuentra el primer permiso ya no se mira en los siguientes niveles (sea para usuario o grupo).
 
Los permisos pueden asignarse a usuarios o a grupos. Los usuarios tienen preferencia sobre los grupos. La preferencia entre los grupos es aleatoria, por tanto hay que procurar dar permisos que no se pisen. Pro ejemplo al grupo "ofina_agd" se le puede asignar permiso para borrar en "sv agd" y para ver en "sv". Al grupo "oficina_n" permiso para borrar en "sv n" y para ver en "sv". De esta manera el unico tipo de actividad común a las dos oficinas (sv) tiene idéntico permiso, y los otros tipos de actividad sólo están en en un grupo.

## Avisos

Se requiere tener instalado el módulo correspondiente.