# cambios

(problemas con este módulo han llevado a la reconsideración de todo el tema de los procesos).

Aunque parece que los objetos a los que se hace referencia (conjuto de datos) son los mismos que en los permisos, no es así. En los permisos se hace referencia a conceptos que pueden englobar más de un objeto (o menos). Esta información se usa a nivel de programa para dejar hacer o no alguna cosa. En los avisos, los objetos se refieren directamente a la base de datos: si ha cambiado este registro. El programa de avisos informa de los cambios sufridos en la base de datos, con independencia del proceso que los haya provocado. 

Para generar el aviso, se emplea el mismo criterio que en los permisos. Se define una fase de referencia, si está marcada se avisa. Se mantiene la opción de poder definir que avise si no está marcada, que a primera vista no parece lógica, pero quizá podría usarse en algún caso. Por ejemplo si estoy preparando las actividades de sg, quiero que me avise si alguien cambia algo, pero una vez aprobadas ya no quiero que me avise (ya he hecho mi trabajo y ahora depende de otros).

Una vez determinado que hay que avisar según el criterio de la fase, se comprueba que el usuario tiene permiso para ver esos datos. Si no tiene permiso no se avisa.

Se ha añadido un concepto adicional: "Avisar si ha finalizado la actividad". Por defecto desmarcado. Si se marca, se avisa aunque el cambio tenga una fecha posterior a la de fin de la actividad.