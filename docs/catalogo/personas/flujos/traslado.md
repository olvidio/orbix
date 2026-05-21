---
id: "personas.traslado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Gestionar Traslado"
capacidad: "personas.traslado.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.traslado_form"]
acciones: ["crear_actualizar", "ver_formulario"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Traslado

Propuesta generada automaticamente desde la capacidad `personas.traslado.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Traslado. Endpoint JSON: aplica un traslado de centro y/o delegacion. Endpoint JSON: datos para el formulario traslado_form.phtml.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `personas.pantalla.traslado_form`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/personas/traslado_update`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/personas/traslado_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.f_ctr`
- `form.f_dl`
- `form.new_ctr`
- `form.new_dl`
- `form.situacion`
- `html.f_ctr`
- `html.f_dl`
- `post.cabecera`
- `post.id_pau`
- `post.obj_pau`
- `post.sel`

Acciones JavaScript:
- `fnjs_guardar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Errores Conocidos

- ``Faltan id_pau u obj_pau``
- ``No existe la clase de la persona``
- ``No se encuentra la persona``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
