---
id: "procesos.fases_activ_cambio.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Fases Activ Cambio"
capacidad: "procesos.fases_activ_cambio.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.fases_activ_cambio", "procesos.pantalla.fases_activ_cambio_lista"]
acciones: ["crear_actualizar", "listar", "obtener"]
endpoints: ["/src/procesos/fases_activ_cambio_get", "/src/procesos/fases_activ_cambio_lista", "/src/procesos/fases_activ_cambio_update"]
estado_revision: "revisado"
---

# Flujo - Cambiar de fase

## Objetivo De Usuario

Cambio masivo de fase en actividades: filtrar por tipo, periodo y fase destino; listar candidatas; marcar o desmarcar la tarea de la fase nueva en las actividades seleccionadas.

## Punto De Entrada

Menú Legacy: Calendario > actividades > cambiar de fase (también dre y variantes vest/vsm/dagd/vsg). Pills2: ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades (también ATENCIÓN SACD, dre y Calendario).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.fases_activ_cambio`
- `procesos.pantalla.fases_activ_cambio_lista`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/procesos/fases_activ_cambio_update`

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/procesos/fases_activ_cambio_lista`

### Obtener

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.accion`
- `form.dl_propia`
- `form.empiezamax`
- `form.empiezamin`
- `form.entrada`
- `form.extendida`
- `form.id_fase_nueva`
- `form.id_fase_sel`
- `form.id_tipo_activ`
- `form.modo`
- `form.periodo`
- `form.salida`
- `form.sel`
- `form.year`
- `post.dl_propia`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_fase_nueva`
- `post.id_tipo_activ`
- `post.inicio`
- `post.periodo`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.stack`
- `post.year`

Acciones JavaScript:
- `fnjs_cambiar`
- `fnjs_selectAll`
- `fnjs_ver_activ`

## Endpoints Del Flujo

- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_lista`
- `/src/procesos/fases_activ_cambio_update`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** Calendario > actividades > cambiar de fase; dre > actividades > cambiar de fase
- **Pills2:** ATENCIÓN SACD > Actividades > cambiar de fase; dre > actividades > cambiar de fase; Calendario > actividades > cambiar de fase; ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades
