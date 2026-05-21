---
id: "actividadestudios.docencia_actualizar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Docencia Actualizar"
capacidad: "actividadestudios.docencia_actualizar.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.actualizar_docencia"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/docencia_actualizar"]
estado_revision: "generado"
---

# Flujo - Gestionar Docencia Actualizar

Propuesta generada automaticamente desde la capacidad `actividadestudios.docencia_actualizar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DocenciaActualizar. Ejecuta {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.actualizar_docencia`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.continuar`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/actividadestudios/docencia_actualizar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
