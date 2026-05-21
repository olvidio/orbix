---
id: "personas.stgr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Gestionar Stgr"
capacidad: "personas.stgr.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.stgr_cambio"]
acciones: ["crear_actualizar"]
endpoints: ["/src/personas/stgr_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Stgr

Propuesta generada automaticamente desde la capacidad `personas.stgr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Stgr. Endpoint JSON: actualiza el nivel_stgr de una persona.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `personas.pantalla.stgr_cambio`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/personas/stgr_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.nivel_stgr`
- `html.guardar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`

Acciones JavaScript:
- `fnjs_guardar_stgr`

## Endpoints Del Flujo

- `/src/personas/stgr_update`

## Errores Conocidos

- ``No existe la clase de la persona``
- ``No se encuentra la persona``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
