---
tipo: "manual_usuario"
modulo: "misas"
flujos: 32
estado_revision: "revisado_parcial"
---

# Manual De Usuario - misas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Anadir Ctr Tarea

### Para Que Sirve

- Añade o elimina una fila de plantilla (centro asociado a tarea) en el editor de plantillas.
- Rama que=anadir crea Plantilla con semana=-1; rama quitar elimina por id_item.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `opción no definida en switch en %s, linea %s`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.anadir_ctr_tarea.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/anadir_ctr_tarea.md`

## Buscar Plan Ctr

### Para Que Sirve

Inicializa el formulario de búsqueda del plan CTR: zonas, centros disponibles y selección por defecto según rol del usuario.

### Donde Entrar

- Buscar Plan Ctr (frontend/misas/controller/buscar_plan_ctr.php)
- **Legacy:** dre > Misas > Plan centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan ctr

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.buscar_plan_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/buscar_plan_ctr.md`

## Buscar Plan Sacd

### Para Que Sirve

Devuelve el desplegable de sacerdotes para el buscador del plan SACD, filtrado por rol y zona del usuario.

### Donde Entrar

- Buscar Plan Sacd (frontend/misas/controller/buscar_plan_sacd.php)
- **Legacy:** dre > Misas > Plan sacerdote
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan sacerdote

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.buscar_plan_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/buscar_plan_sacd.md`

## Cambiar Status

### Para Que Sirve

Carga los desplegables de la pantalla cambiar estado del plan de misas: zonas permitidas, criterios de orden y estados posibles.

### Donde Entrar

- Cambiar Status (frontend/misas/controller/cambiar_status.php)
- **Legacy:** dre > Misas > Cambiar estado
- **Pills2:** dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.cambiar_status.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/cambiar_status.md`

## Crear Nuevo Periodo

### Para Que Sirve

Crea asignaciones EncargoDia para un nuevo periodo de plan de misas a partir de plantilla y devuelve el payload de cuadrícula para renderizar ver_cuadricula_zona.phtml.

### Donde Entrar

- Crear Nuevo Periodo (frontend/misas/controller/crear_nuevo_periodo.php)
- **Legacy:** dre > Misas >  Nuevo plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Nuevo plan

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado en error_txt>`

### Referencias Internas

- Flujo: `misas.crear_nuevo_periodo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/crear_nuevo_periodo.md`

## Cuadricula

### Para Que Sirve

Asigna, actualiza o borra un EncargoDia en una celda de la cuadrícula y recalcula metadatos de color/texto para la fila SACD y la celda misa.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** dre > Misas > Modificar plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plan

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

### Errores O Avisos Frecuentes

- `Falta el id_item`
- `Este día tiene más de dos Misas`
- `Este día tiene dos Misas`
- `Este día no tiene ninguna Misa`
- `Tiene dos Misas a primera hora`
- `No está en la zona y tiene Misa a primera hora`
- `Está en `
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.cuadricula.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/cuadricula.md`

## Desplegable Encargos

### Para Que Sirve

Devuelve opciones de encargos 8100+ de una zona para el desplegable dinámico del modal de encargos-centro.

### Donde Entrar

- Ver Encargos Centros (frontend/misas/controller/ver_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.desplegable_encargos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/desplegable_encargos.md`

## Desplegable Sacd

### Para Que Sirve

Construye el desplegable dinámico de SACD en el modal de la cuadrícula, filtrando por disponibilidad según flags de selección y día.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.desplegable_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/desplegable_sacd.md`

## Eliminar Encargo Centro

### Para Que Sirve

Elimina la relación EncargoCtr (encargo visible en un centro) por uuid.

### Donde Entrar

- Ver Encargos Centros (frontend/misas/controller/ver_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Falta el identificador del encargo-centro a eliminar`
- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.eliminar_encargo_centro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/eliminar_encargo_centro.md`

## Eliminar Encargo Zona

### Para Que Sirve

Elimina un Encargo de zona (grupo ZONAS_MISAS) por id_enc.

### Donde Entrar

- Ver Encargos Zona (frontend/misas/controller/ver_encargos_zona.php)
- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.eliminar_encargo_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/eliminar_encargo_zona.md`

## Guardar Encargo Centro

### Para Que Sirve

Inserta o actualiza un EncargoCtr vinculando un encargo de zona con un centro.

### Donde Entrar

- Ver Encargos Centros (frontend/misas/controller/ver_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.guardar_encargo_centro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/guardar_encargo_centro.md`

## Guardar Encargo Zona

### Para Que Sirve

Crea o actualiza un Encargo del grupo ZONAS_MISAS (id_enc=0 → alta) y devuelve id y nombre del centro.

### Donde Entrar

- Ver Encargos Zona (frontend/misas/controller/ver_encargos_zona.php)
- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.guardar_encargo_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/guardar_encargo_zona.md`

## Guardar Horario

### Para Que Sirve

Guarda hora inicio/fin (t_start/t_end) de un EncargoHorario en el modal de horario de tarea.

### Donde Entrar

- Horario Tarea (frontend/misas/controller/horario_tarea.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Error: falta el id_item`
- `No se encuentra el horario %d`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.guardar_horario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/guardar_horario.md`

## Horario Tarea

### Para Que Sirve

Lee las horas actuales de un EncargoHorario para poblar el modal horario_tarea.

### Donde Entrar

- Horario Tarea (frontend/misas/controller/horario_tarea.php)
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.horario_tarea.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/horario_tarea.md`

## Importar Plantilla

### Para Que Sirve

Copia asignaciones de plantilla origen a destino para una zona, creando/actualizando EncargoDia en el rango correspondiente.

### Donde Entrar

- Importar Plantilla (frontend/misas/controller/importar_plantilla.php)
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado>`

### Referencias Internas

- Flujo: `misas.importar_plantilla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/importar_plantilla.md`

## Modificar Encargos

### Para Que Sirve

Devuelve zonas permitidas y criterios de orden para la pantalla modificar encargos de zona.

### Donde Entrar

- Modificar Encargos (frontend/misas/controller/modificar_encargos.php)
- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`
- `orden`
- `prioridad`
- `alfabético`

### Referencias Internas

- Flujo: `misas.modificar_encargos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/modificar_encargos.md`

## Modificar Encargos Centros

### Para Que Sirve

Devuelve el desplegable de zonas permitidas para la pantalla modificar encargos de centros.

### Donde Entrar

- Modificar Encargos Centros (frontend/misas/controller/modificar_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.modificar_encargos_centros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/modificar_encargos_centros.md`

## Modificar Iniciales Sacd Zona

### Para Que Sirve

Devuelve el desplegable de todas las zonas para la pantalla de edición de iniciales SACD.

### Donde Entrar

- Modificar Iniciales Sacd Zona (frontend/misas/controller/modificar_iniciales_sacd_zona.php)
- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.modificar_iniciales_sacd_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/modificar_iniciales_sacd_zona.md`

## Modificar Plantilla

### Para Que Sirve

Carga desplegables de zona, orden y tipos de plantilla (con preferencia ultima_plantilla) para modificar plantilla.

### Donde Entrar

- Modificar Plantilla (frontend/misas/controller/modificar_plantilla.php)
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.modificar_plantilla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/modificar_plantilla.md`

## Nuevo Status

### Para Que Sirve

Actualiza masivamente el status de todos los EncargoDia de encargos 8100+ de una zona en el rango de fechas indicado.

### Donde Entrar

- Cambiar Status (frontend/misas/controller/cambiar_status.php)
- **Legacy:** dre > Misas > Cambiar estado
- **Pills2:** dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `<repositorio getErrorTxt() acumulado>`

### Referencias Internas

- Flujo: `misas.nuevo_status.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/nuevo_status.md`

## Plan De Misas Pantalla

### Para Que Sirve

Datos comunes para pantallas preparar/modificar/ver plan de misas: zonas, orden y tipos de plantilla en preparar.

### Donde Entrar

- Modificar Plan De Misas (frontend/misas/controller/modificar_plan_de_misas.php)
- Preparar Plan De Misas (frontend/misas/controller/preparar_plan_de_misas.php)
- Ver Plan De Misas (frontend/misas/controller/ver_plan_de_misas.php)
- **Legacy:** dre > Misas >  Nuevo plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Nuevo plan

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

### Referencias Internas

- Flujo: `misas.plan_de_misas_pantalla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/plan_de_misas_pantalla.md`

## Quitar Horario

### Para Que Sirve

Anula t_start/t_end de una fila Plantilla (quita horario asignado a la tarea).

### Donde Entrar

- Horario Tarea (frontend/misas/controller/horario_tarea.php)
- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.quitar_horario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/quitar_horario.md`

## Update Iniciales

### Para Que Sirve

Inserta o actualiza iniciales y color de un sacerdote en la tabla InicialesSacd.

### Donde Entrar

- Ver Iniciales Zona (frontend/misas/controller/ver_iniciales_zona.php)
- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `<repositorio getErrorTxt()>`

### Referencias Internas

- Flujo: `misas.update_iniciales.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/update_iniciales.md`

## Ver Cuadricula Zona

### Para Que Sirve

Construye el SlickGrid de cuadrícula de zona (columnas, filas encargo/sacd, metadatos de celda) para ver/modificar plan, plantilla o cambiar estado.

### Donde Entrar

- Modificar Cuadricula Zona (frontend/misas/controller/modificar_cuadricula_zona.php)
- Ver Cuadricula Zona (frontend/misas/controller/ver_cuadricula_zona.php)
- **Legacy:** dre > Misas > Modificar plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plan

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`
- `sólo debería haber uno`

### Referencias Internas

- Flujo: `misas.ver_cuadricula_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_cuadricula_zona.md`

## Ver Encargos Centros

### Para Que Sirve

Devuelve filas del grid EncargoCtr de una zona más desplegables estáticos del modal (zonas, centros).

### Donde Entrar

- Ver Encargos Centros (frontend/misas/controller/ver_encargos_centros.php)
- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_encargos_centros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_encargos_centros.md`

## Ver Encargos Zona

### Para Que Sirve

Devuelve encargos 8100+ de una zona ordenados para SlickGrid y datos del modal de edición.

### Donde Entrar

- Ver Encargos Zona (frontend/misas/controller/ver_encargos_zona.php)
- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_encargos_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_encargos_zona.md`

## Ver Iniciales Zona

### Para Que Sirve

Lista sacds de una zona con sus iniciales y color para edición inline en SlickGrid.

### Donde Entrar

- Ver Iniciales Zona (frontend/misas/controller/ver_iniciales_zona.php)
- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_iniciales_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_iniciales_zona.md`

## Ver Misas Zona

### Para Que Sirve

Construye la cuadrícula de consulta de misas por zona y rango de fechas (solo lectura, con metadatos dia/tipo en celdas).

### Donde Entrar

- Ver Misas Zona (frontend/misas/controller/ver_misas_zona.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `solo deberia haber uno`

### Referencias Internas

- Flujo: `misas.ver_misas_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_misas_zona.md`

## Ver Plan Ctr

### Para Que Sirve

Genera la cuadrícula del plan de misas por centro: encargos en filas, días en columnas, con leyenda de sacds.

### Donde Entrar

- Imprimir Plan Ctr (frontend/misas/controller/imprimir_plan_ctr.php)
- Ver Plan Ctr (frontend/misas/controller/ver_plan_ctr.php)
- **Legacy:** dre > Misas > Plan centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan ctr

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_plan_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_plan_ctr.md`

## Ver Plan Sacd

### Para Que Sirve

Lista cronológica de misas asignadas a un sacerdote en un rango de fechas.

### Donde Entrar

- Ver Plan Sacd (frontend/misas/controller/ver_plan_sacd.php)
- **Legacy:** dre > Misas > Plan sacerdote
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan sacerdote

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `misas.ver_plan_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/ver_plan_sacd.md`

## Zona Sacd Datos

### Para Que Sirve

Lee datos de disponibilidad semanal (propia, dw1-dw7) de un SACD en una zona para el modal zona_sacd.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No existe`

### Referencias Internas

- Flujo: `misas.zona_sacd_datos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/misas/flujos/zona_sacd_datos.md`

## Zona Sacd Datos Put

### Para Que Sirve

Guarda flags de disponibilidad semanal de un SACD en una zona (ZonaSacd).

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

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
