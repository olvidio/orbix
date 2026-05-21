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
estado_revision: "generado"
---

# Flujo - Gestionar Fases Activ Cambio

Propuesta generada automaticamente desde la capacidad `procesos.fases_activ_cambio.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona FasesActivCambio, FasesActivCambioGet, FasesActivCambioLista. Caso de uso: aplica setCompletado(t|f) a la tarea de la fase nueva para cada id_activ seleccionado, respetando permisos de oficina del responsable. Caso de uso: devuelve las fases posibles para el id_tipo_activ y la dl_propia actual, incluyendo la opcion seleccionada por id_fase_sel. Respuesta conforme al contrato de refactor.md para desplegables (payload JSON con id, opciones, selected, blanco, action). El frontend construye el <select> con el helper JS estandar. Caso de uso: devuelve los datos estructurados para la tabla de actividades candidatas a cambiar de fase, segun filtros de tipo de actividad, dl_propia, periodo y accion (marcar/desmarcar). El frontend renderiza el formulario con frontend\shared\web\Lista + web\Hash.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
