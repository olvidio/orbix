---
id: "actividades.tipo_activ_form.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Tipo Activ Form"
capacidad: "actividades.tipo_activ_form.gestionar"
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
acciones: ["crear"]
endpoints: ["/src/actividades/tipo_activ_form_nuevo"]
estado_revision: "generado"
---

# Flujo - Gestionar Tipo Activ Form

Propuesta generada automaticamente desde la capacidad `actividades.tipo_activ_form.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TipoActivForm. Devuelve el HTML del formulario para crear un nuevo tipo de actividad. Portado del case form_nuevo del dispatcher legacy.

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

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_tipo_activ`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_form_nuevo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
