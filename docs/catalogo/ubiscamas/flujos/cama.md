---
id: "ubiscamas.cama.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Gestionar Cama"
capacidad: "ubiscamas.cama.gestionar"
pantallas_principales: []
fragmentos: ["ubiscamas.pantalla.cama_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/ubiscamas/cama_delete", "/src/ubiscamas/cama_form_data", "/src/ubiscamas/cama_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Cama

Propuesta generada automaticamente desde la capacidad `ubiscamas.cama.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Cama. Datos para frontend/ubiscamas/controller/cama_form.php. La composición de HashFront ocurre en {. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubiscamas.pantalla.cama_form`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/ubiscamas/cama_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/ubiscamas/cama_delete`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/ubiscamas/cama_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.descripcion`
- `html.larga`
- `html.vip`

Acciones JavaScript:
- `fnjs_cancelar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubiscamas/cama_delete`
- `/src/ubiscamas/cama_form_data`
- `/src/ubiscamas/cama_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
