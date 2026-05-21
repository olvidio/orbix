---
id: "actividades.tipo_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Tipo Activ"
capacidad: "actividades.tipo_activ.gestionar"
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
acciones: ["crear", "crear_actualizar", "eliminar", "listar"]
endpoints: ["/src/actividades/tipo_activ_eliminar", "/src/actividades/tipo_activ_lista", "/src/actividades/tipo_activ_nuevo", "/src/actividades/tipo_activ_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Tipo Activ

Propuesta generada automaticamente desde la capacidad `actividades.tipo_activ.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TipoActiv, TipoActivLista. Actualiza el nombre de un tipo de actividad. Portado del case update del dispatcher legacy. Crea un nuevo tipo de actividad. Portado del case nuevo del dispatcher legacy. Devuelve cadena vacia si todo va bien o un texto de error/aviso. Devuelve la tabla HTML con los tipos de actividad existentes. Portado desde el case lista del dispatcher legacy frontend/actividades/controller/tipo_activ_ajax.php. Elimina un tipo de actividad. Portado del case eliminar del dispatcher legacy.

## Punto De Entrada

- `actividades.pantalla.tipo_activ`

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Crear

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/actividades/tipo_activ_eliminar`

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/actividades/tipo_activ_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_tipo_activ`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

## Errores Conocidos

- ``hay un error, no se ha eliminado``
- ``hay un error, no se ha guardado``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
