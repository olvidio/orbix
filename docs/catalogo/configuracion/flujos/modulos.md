---
id: "configuracion.modulos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Gestionar Modulos"
capacidad: "configuracion.modulos.gestionar"
pantallas_principales: ["configuracion.pantalla.modulos_update"]
fragmentos: ["configuracion.pantalla.modulos_form"]
acciones: ["crear_actualizar", "ver_formulario"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Modulos

Propuesta generada automaticamente desde la capacidad `configuracion.modulos.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Modulos, ModulosUpdateAction. Alta / baja / modificación de módulos (respuesta texto plano para AJAX legacy). JSON para {.

## Punto De Entrada

- `configuracion.pantalla.modulos_update`

## Fragmentos O Pantallas Auxiliares

- `configuracion.pantalla.modulos_form`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/configuracion/modulos_update`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/configuracion/modulos_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.descripcion`
- `html.nom`
- `html.refresh`
- `html.sel_apps[]`
- `html.sel_mods[]`
- `post.refresh`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_cambio`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/configuracion/modulos_form_data`
- `/src/configuracion/modulos_update`

## Errores Conocidos

- ``hay un error, no se ha eliminado``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
