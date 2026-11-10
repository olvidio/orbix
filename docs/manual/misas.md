---
tipo: "manual_usuario"
modulo: "misas"
flujos: 31
estado_revision: "revisado"
---

# Manual De Usuario - misas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Ver el plan de misas de un centro

### Para Que Sirve

Elegir zona, centro y periodo para ver el plan de misas de un centro.

### Donde Entrar

- Buscar Plan Ctr (frontend/misas/controller/buscar_plan_ctr.php)
- **Legacy:** dre > Misas > Plan centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan ctr

### Tareas Habituales

#### Ver el plan de misas de un centro

1. Entrar en **Plan centro**.
2. Elegir la **zona**, el **centro** y el **Periodo**.
3. Se muestra el plan: los encargos en filas y los días en columnas, con la leyenda de sacerdotes.
4. Si el usuario no tiene permiso, aparece «No tiene permiso para ver esta página».

### Errores O Avisos Frecuentes

- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.buscar_plan_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/buscar_plan_ctr.md`

## Ver el plan de un sacerdote

### Para Que Sirve

Elegir sacerdote y periodo para ver su plan de misas.

### Donde Entrar

- Buscar Plan Sacd (frontend/misas/controller/buscar_plan_sacd.php)
- **Legacy:** dre > Misas > Plan sacerdote
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan sacerdote

### Tareas Habituales

#### Ver el plan de un sacerdote

1. Entrar en **Plan sacerdote**.
2. Elegir el **Sacerdote** (la lista se limita según el rol y la zona del usuario) y el **Periodo**.
3. Se muestra la lista día a día de las misas asignadas.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.buscar_plan_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/buscar_plan_sacd.md`

## Cambiar el estado del plan

### Para Que Sirve

Cambiar el estado del plan de misas de una zona y periodo: propuesta, visible para sacerdotes o visible para centros.

### Donde Entrar

- Cambiar Status (frontend/misas/controller/cambiar_status.php)
- **Legacy:** dre > Misas > Cambiar estado
- **Pills2:** dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado

### Tareas Habituales

#### Cambiar el estado del plan

1. Entrar en **Cambiar estado**.
2. Elegir la zona en **Plan de Misas Zona**, el **Orden** y el **Periodo**.
3. En **Nuevo estado** elegir propuesta, visible sacd o visible ctr.
4. Pulsar **cambiar**: se aplica a todas las misas de la zona en ese periodo. No muestra ningún mensaje de confirmación; para comprobarlo hay que volver a abrir el plan.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.cambiar_status.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/cambiar_status.md`

## Preparar un nuevo plan de misas

### Para Que Sirve

Crear el plan de misas de un periodo nuevo a partir de la plantilla y mostrarlo en la cuadrícula.

### Donde Entrar

- Crear Nuevo Periodo (frontend/misas/controller/crear_nuevo_periodo.php)
- **Legacy:** dre > Misas >  Nuevo plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Nuevo plan

### Tareas Habituales

#### Preparar un nuevo plan de misas

1. Entrar en **Nuevo plan** (Preparar Plan de Misas).
2. Elegir la zona en **Plan de Misas Zona**, el **Tipo de plantilla** (semanal, semanal y domingos o mensual, con una o tres opciones: la plantilla propone 1 o 3 sacerdotes para cada misa, por orden de prioridad; se elige el primero y solo se pasa al segundo si el primero no está o celebra en un centro con más prioridad), el **Orden** y el **Periodo** (próxima semana, próximo mes natural u otro, con fechas).
3. Pulsar **preparar**. Aviso: «Borrará todos los datos introducidos en este periodo!!».
4. Se muestra la cuadrícula del plan, rellenada desde la plantilla, lista para modificar: al pulsar una celda se abre la ventana **Selección de sacd** (ver «Elegir sacerdote en una celda»).

### Errores O Avisos Frecuentes

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado en error_txt>`

### Referencias Internas

- Flujo: `misas.crear_nuevo_periodo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/crear_nuevo_periodo.md`

## Editar una celda del plan de misas

### Para Que Sirve

Asignar, cambiar o borrar el sacerdote de una celda del plan de misas; preparar un periodo nuevo o copiar una plantilla a otra.

### Donde Entrar

- modificar_plan_de_misas
- preparar_plan_de_misas
- modificar_plantilla
- **Legacy:** dre > Misas > Modificar plan / Preparar / Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > …

### Tareas Habituales

#### Editar una celda del plan de misas

1. Entrar en **Modificar plan** y buscar la zona y el periodo.
2. Pulsar la celda del encargo y día; elegir el sacerdote (o dejarla vacía para borrar), con horario y observaciones si procede.
3. Para periodos nuevos usar **Nuevo plan**; para copiar plantillas, **Modificar plantilla > importar**.
4. Leyenda de la fila de cada sacerdote (por día): **SI** = el sacerdote está en la zona ese día; **NO** = no está en la zona ese día; **--** = ese día está en otro sitio (al pasar el ratón sale «Está en …»). El fondo rojo avisa de un problema: «Tiene dos Misas a primera hora» o «No está en la zona y tiene Misa a primera hora».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `misas.cuadricula.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/cuadricula.md`

## Elegir encargo de la zona

### Para Que Sirve

Ofrecer los encargos de la zona elegida en la ventana de encargo-centro.

### Donde Entrar

- Ver Encargos Centros (frontend/misas/controller/ver_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Elegir encargo de la zona

1. En la ventana de encargo visible para un centro, elegir la zona.
2. El desplegable **Encargo** se rellena con los encargos de esa zona.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.desplegable_encargos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/desplegable_encargos.md`

## Elegir sacerdote en una celda

### Para Que Sirve

Ofrecer, al pulsar una celda de la cuadrícula, los sacerdotes disponibles ese día.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir sacerdote en una celda

1. En **Modificar plan**, pulsar la celda del día y encargo.
2. En **Selección de sacd** marcar qué sacerdotes se quieren ver: **libre a 1a hora**, **zona** (por defecto), **dl** o **de paso**; después elegir el **Sacerdote** de la lista, que solo muestra los disponibles ese día.
3. Indicar **Inicio**, **Fin** y **Observaciones** si procede y pulsar **Save**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.desplegable_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/desplegable_sacd.md`

## Quitar un encargo visible para un centro

### Para Que Sirve

Quitar un encargo de la vista de un centro.

### Donde Entrar

- Ver Encargos Centros (frontend/misas/controller/ver_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Quitar un encargo visible para un centro

1. Abrir el encargo-centro y pulsar **Eliminar encargo**.
2. Confirmar.

### Errores O Avisos Frecuentes

- `Falta el identificador del encargo-centro a eliminar`
- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.eliminar_encargo_centro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/eliminar_encargo_centro.md`

## Eliminar un encargo de zona

### Para Que Sirve

Eliminar un encargo de zona.

### Donde Entrar

- Ver Encargos Zona (frontend/misas/controller/ver_encargos_zona.php)
- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos

### Tareas Habituales

#### Eliminar un encargo de zona

1. Abrir el encargo y pulsar **Eliminar encargo**.
2. Confirmar.

### Errores O Avisos Frecuentes

- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.eliminar_encargo_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/eliminar_encargo_zona.md`

## Guardar un encargo visible para un centro

### Para Que Sirve

Hacer visible un encargo de zona en un centro, o cambiarlo.

### Donde Entrar

- Ver Encargos Centros (frontend/misas/controller/ver_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Guardar un encargo visible para un centro

1. Elegir zona, encargo y centro.
2. Pulsar **Grabar**.

### Errores O Avisos Frecuentes

- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.guardar_encargo_centro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/guardar_encargo_centro.md`

## Guardar un encargo de zona

### Para Que Sirve

Crear o actualizar un encargo de zona.

### Donde Entrar

- Ver Encargos Zona (frontend/misas/controller/ver_encargos_zona.php)
- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos

### Tareas Habituales

#### Guardar un encargo de zona

1. Rellenar el formulario del encargo (el botón **generar** propone el nombre «tipo (lugar)») y pulsar **Grabar**.
2. Si el encargo ya no existe aparece «No se encuentra el encargo».

### Errores O Avisos Frecuentes

- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.guardar_encargo_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/guardar_encargo_zona.md`

## Guardar el horario de una tarea

### Para Que Sirve

Guardar la hora de inicio y fin de una tarea.

### Donde Entrar

- Horario Tarea (frontend/misas/controller/horario_tarea.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Guardar el horario de una tarea

1. Escribir **Inicio** y **Fin**.
2. Pulsar **guardar**.

### Errores O Avisos Frecuentes

- `Error: falta el id_item`
- `No se encuentra el horario %d`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.guardar_horario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/guardar_horario.md`

## Poner horario a una tarea

### Para Que Sirve

Ver y fijar la hora de inicio y fin de una tarea (encargo) en la plantilla.

### Donde Entrar

- Horario Tarea (frontend/misas/controller/horario_tarea.php)
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Poner horario a una tarea

1. En **Modificar plantilla**, abrir el horario de la tarea.
2. Escribir **Inicio** y **Fin** y pulsar **guardar**.

#### Quitar el horario de una tarea

1. Abrir el horario de la tarea y pulsar **quitar el actual**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.horario_tarea.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/horario_tarea.md`

## Importar otra plantilla

### Para Que Sirve

Copiar las asignaciones de una plantilla a otra de la misma zona.

### Donde Entrar

- Importar Plantilla (frontend/misas/controller/importar_plantilla.php)
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Importar otra plantilla

1. En **Modificar plantilla**, elegir la plantilla origen en **Importar de la plantilla**.
2. Pulsar **importar** y confirmar el aviso de que se borra lo que hay.
3. Se muestra la plantilla con los datos copiados.

### Errores O Avisos Frecuentes

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado>`

### Referencias Internas

- Flujo: `misas.importar_plantilla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/importar_plantilla.md`

## Elegir la zona de los encargos

### Para Que Sirve

Elegir la zona para crear y modificar sus encargos (tareas de misa).

### Donde Entrar

- Modificar Encargos (frontend/misas/controller/modificar_encargos.php)
- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos

### Tareas Habituales

#### Elegir la zona de los encargos

1. Entrar en **Modificar encargos**.
2. Elegir la zona en **Encargos de la zona** y el **Orden**.
3. Se abre la lista de encargos de esa zona.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`
- `orden`
- `prioridad`
- `alfabético`

### Referencias Internas

- Flujo: `misas.modificar_encargos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/modificar_encargos.md`

## Elegir la zona de los encargos visibles por centro

### Para Que Sirve

Elegir la zona para decidir qué encargos ve cada centro.

### Donde Entrar

- Modificar Encargos Centros (frontend/misas/controller/modificar_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Elegir la zona de los encargos visibles por centro

1. Entrar en **Encargos centro**.
2. Elegir la zona en **Encargos de la zona**.
3. Se abre la lista de encargos visibles por centro.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.modificar_encargos_centros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/modificar_encargos_centros.md`

## Elegir la zona de las iniciales

### Para Que Sirve

Elegir la zona cuyas iniciales de sacerdotes se quieren editar.

### Donde Entrar

- Modificar Iniciales Sacd Zona (frontend/misas/controller/modificar_iniciales_sacd_zona.php)
- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd

### Tareas Habituales

#### Elegir la zona de las iniciales

1. Entrar en **Iniciales sacd**.
2. Elegir la zona en **Iniciales sacerdotes de la zona**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.modificar_iniciales_sacd_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/modificar_iniciales_sacd_zona.md`

## Modificar la plantilla de una zona

### Para Que Sirve

Editar la plantilla de una zona: las tareas que se repiten cada semana o mes y quién las hace.

### Donde Entrar

- Modificar Plantilla (frontend/misas/controller/modificar_plantilla.php)
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Modificar la plantilla de una zona

1. Entrar en **Modificar plantilla**.
2. Elegir la zona en **Plantilla de la zona**, el **Tipo de plantilla** y el **Orden**.
3. Editar la cuadrícula que aparece.

#### Importar de otra plantilla

1. En el desplegable **Importar de la plantilla** elegir el tipo de plantilla origen.
2. Pulsar **importar**. Aviso: «Borrará todos los datos introducidos en la plantilla que ves!!».

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.modificar_plantilla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/modificar_plantilla.md`

## Aplicar un nuevo estado a un periodo

### Para Que Sirve

Aplicar el nuevo estado a todas las misas de una zona en un rango de fechas.

### Donde Entrar

- Cambiar Status (frontend/misas/controller/cambiar_status.php)
- **Legacy:** dre > Misas > Cambiar estado
- **Pills2:** dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado

### Tareas Habituales

#### Aplicar un nuevo estado a un periodo

1. En **Cambiar estado**, elegir zona, periodo y **Nuevo estado**.
2. Pulsar **cambiar**. Los sacerdotes o centros pasan a ver el plan según el estado elegido.

### Errores O Avisos Frecuentes

- `<repositorio getErrorTxt() acumulado>`

### Referencias Internas

- Flujo: `misas.nuevo_status.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/nuevo_status.md`

## Elegir zona, orden y periodo del plan

### Para Que Sirve

Cargar las zonas, el orden y los tipos de plantilla que ofrecen las pantallas de preparar, modificar y ver el plan de misas.

### Donde Entrar

- Modificar Plan De Misas (frontend/misas/controller/modificar_plan_de_misas.php)
- Preparar Plan De Misas (frontend/misas/controller/preparar_plan_de_misas.php)
- Ver Plan De Misas (frontend/misas/controller/ver_plan_de_misas.php)
- **Legacy:** dre > Misas >  Nuevo plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Nuevo plan

### Tareas Habituales

#### Elegir zona, orden y periodo del plan

1. Entrar en la pantalla del plan.
2. En **Plan de Misas Zona** elegir la zona.
3. Elegir el **Orden** de los sacerdotes (orden, prioridad o alfabético) y el **Periodo**.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.plan_de_misas_pantalla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/plan_de_misas_pantalla.md`

## Quitar el horario de una tarea

### Para Que Sirve

Quitar el horario asignado a una tarea de la plantilla.

### Donde Entrar

- Horario Tarea (frontend/misas/controller/horario_tarea.php)
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Quitar el horario de una tarea

1. Abrir el horario de la tarea.
2. Pulsar **quitar el actual**.

### Errores O Avisos Frecuentes

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.quitar_horario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/quitar_horario.md`

## Guardar iniciales y color

### Para Que Sirve

Guardar las iniciales y el color de un sacerdote.

### Donde Entrar

- Ver Iniciales Zona (frontend/misas/controller/ver_iniciales_zona.php)
- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd

### Tareas Habituales

#### Guardar iniciales y color

1. Cambiar las iniciales o el color en la fila.
2. Se guardan al salir de la celda.

### Errores O Avisos Frecuentes

- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.update_iniciales.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/update_iniciales.md`

## Ver y modificar el plan de una zona

### Para Que Sirve

Mostrar la cuadrícula del plan de misas de una zona (encargos en filas, días en columnas, sacerdotes en las celdas) para consultarla o modificarla.

### Donde Entrar

- Ver Cuadricula Zona (frontend/misas/controller/ver_cuadricula_zona.php)
- **Legacy:** dre > Misas > Modificar plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plan

### Tareas Habituales

#### Ver y modificar el plan de una zona

1. Entrar en **Modificar plan** (o **Ver plan zona** para solo consulta).
2. Elegir la zona, el **Orden** y el **Periodo** (esta semana, este mes, próxima semana, próximo mes u otro) y pulsar buscar.
3. Aparece la cuadrícula con las misas del periodo.
4. Al elegir la zona en **Nuevo plan** o **Modificar plan** ya aparece la cuadrícula del periodo (una fila por sacerdote, una columna por día; se ven SI, NO o -- según el día). En modificar, pulsar una celda para elegir sacerdote; si no hay datos aparece «No hay datos para mostrar en la cuadrícula».

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`
- `sólo debería haber uno`

### Referencias Internas

- Flujo: `misas.ver_cuadricula_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_cuadricula_zona.md`

## Hacer visible un encargo para un centro

### Para Que Sirve

Ver y modificar qué encargos de la zona aparecen en el plan de cada centro.

### Donde Entrar

- Ver Encargos Centros (frontend/misas/controller/ver_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Hacer visible un encargo para un centro

1. En **Encargos centro**, elegir la zona.
2. Pulsar **nuevo**; en **Zona del encargo**, **Encargo** y **Centro** elegir los valores y pulsar **Grabar**.
3. Para modificarlo, pulsarlo y **Grabar**; para quitarlo, **Eliminar encargo**.
4. Si falta encargo o centro aparece «Falta encargo o centro».

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_encargos_centros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_encargos_centros.md`

## los encargos de una zona

### Para Que Sirve

Ver, crear, modificar y eliminar los encargos de una zona.

### Donde Entrar

- Ver Encargos Zona (frontend/misas/controller/ver_encargos_zona.php)
- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos

### Tareas Habituales

#### Gestionar los encargos de una zona

1. En **Modificar encargos**, elegir la zona.
2. Para crear, pulsar **nuevo** y rellenar el formulario: elegir el **Tipo de encargo** (por ejemplo Misa a 1ª hora, Misa no a 1ª hora, rtm con Misa a 1ª hora, rtm sin Misa, bendición) y el **Lugar**; el botón **generar** escribe el nombre del **Encargo** como «tipo (lugar)». Completar **Descripción lugar**, **Orden**, **Prioridad**, **Observaciones** e **Idioma** y pulsar **Grabar**.
3. Para modificar, pulsar el encargo, cambiar y **Grabar**.
4. Para quitar, abrirlo y pulsar **Eliminar encargo**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_encargos_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_encargos_zona.md`

## Cambiar iniciales y color de un sacerdote

### Para Que Sirve

Ver y cambiar las iniciales y el color de cada sacerdote de la zona, que se usan en el plan.

### Donde Entrar

- Ver Iniciales Zona (frontend/misas/controller/ver_iniciales_zona.php)
- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd

### Tareas Habituales

#### Cambiar iniciales y color de un sacerdote

1. En **Iniciales sacd**, elegir la zona.
2. Editar las **Iniciales** en la fila del sacerdote.
3. Elegir el **Color** en la paleta («Más colores», «Elegir otro color» o «Sin color»).

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_iniciales_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_iniciales_zona.md`

## Consultar el plan de misas de una zona

### Para Que Sirve

Consultar las misas de una zona en un rango de fechas, sin poder modificarlas.

### Donde Entrar

- Ver Misas Zona (frontend/misas/controller/ver_misas_zona.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar el plan de misas de una zona

1. Entrar en **Ver plan zona**.
2. Elegir la zona en **Plan de Misas de la zona**, el **Orden** y el **Periodo** (o fechas concretas con «otro»).
3. Se muestra la cuadrícula de consulta; no se puede cambiar nada.
4. Leyenda de la fila de cada sacerdote (por día): **SI** = el sacerdote está en la zona ese día; **NO** = no está en la zona ese día; **--** = ese día está en otro sitio (al pasar el ratón sale «Está en …»). El fondo rojo avisa de un problema: «Tiene dos Misas a primera hora» o «No está en la zona y tiene Misa a primera hora».

### Errores O Avisos Frecuentes

- `solo deberia haber uno`

### Referencias Internas

- Flujo: `misas.ver_misas_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_misas_zona.md`

## Consultar el plan de un centro

### Para Que Sirve

Mostrar el plan de misas de un centro: encargos en filas y días en columnas, con leyenda de sacerdotes.

### Donde Entrar

- Ver Plan Ctr (frontend/misas/controller/ver_plan_ctr.php)
- **Legacy:** dre > Misas > Plan centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan ctr

### Tareas Habituales

#### Consultar el plan de un centro

1. En **Plan centro**, elegir zona, centro y periodo.
2. Leer la cuadrícula; las iniciales de la leyenda identifican a cada sacerdote.
3. Se puede imprimir desde la propia pantalla.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_plan_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_plan_ctr.md`

## Consultar el plan de un sacerdote

### Para Que Sirve

Mostrar, día a día, las misas asignadas a un sacerdote en un periodo.

### Donde Entrar

- Ver Plan Sacd (frontend/misas/controller/ver_plan_sacd.php)
- **Legacy:** dre > Misas > Plan sacerdote
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan sacerdote

### Tareas Habituales

#### Consultar el plan de un sacerdote

1. En **Plan sacerdote**, elegir sacerdote y periodo.
2. La tabla muestra **Dia**, **Encargo** y **Observaciones** de cada misa.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_plan_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_plan_sacd.md`

## Consultar la disponibilidad semanal de un sacerdote

### Para Que Sirve

Ver los días de la semana en que un sacerdote está disponible en una zona.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar la disponibilidad semanal de un sacerdote

1. Abrir la ventana de disponibilidad del sacerdote en la zona.
2. Se muestran marcados los días de la semana disponibles.

### Errores O Avisos Frecuentes

- `No existe`

### Referencias Internas

- Flujo: `misas.zona_sacd_datos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/zona_sacd_datos.md`

## Cambiar la disponibilidad semanal de un sacerdote

### Para Que Sirve

Guardar los días de la semana en que un sacerdote está disponible en una zona.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Cambiar la disponibilidad semanal de un sacerdote

1. Marcar o desmarcar los días de la semana.
2. Guardar.

### Errores O Avisos Frecuentes

- `No existe`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.zona_sacd_datos_put.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/zona_sacd_datos_put.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/misas/flujos/`.
