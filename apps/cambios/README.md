# cambios

problemas con este módulo han llevado a la reconsideración de todo el tema de los procesos.

Hasta ahora el proceso era considerado como una secuencia de fases por las que debe ir pasando la actividad.
La prespectiva que se propone ahora es que la actividad tinene unas fases que se pueden marcar como completadas o no.

En las preferencias, cada usuario puede especificar en que fases quiere que se le avise de un cambio.
Por ejemplo:

	- si las preferencias tiene marcado que avise en las fases 2 y 3 
	 y una actividad tiene las fases 1,2,3,4,5
	- Si están marcadas como completadas la 2 y 4. => si avisa
	- Si están marcadas como completadas la 4 y 5. => NO avisa
	- Si está marcada como completada la 1		 => NO avisa
	- Si está marcada como completada la 3		 => si avisa


IMPORTANTE: después de esta decisión, antes de avisar, se comprueba que el usuario tiene permiso de 'ocupado' para la fase en la que se avisa. Si no tiene permiso no se avisa.