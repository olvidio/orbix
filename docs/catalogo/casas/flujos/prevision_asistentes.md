---
id: "casas.prevision_asistentes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Prevision Asistentes"
capacidad: "casas.prevision_asistentes.gestionar"
pantallas_principales: []
fragmentos: ["casas.pantalla.prevision_asistentes"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/prevision_asistentes_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Prevision Asistentes

Propuesta generada automaticamente desde la capacidad `casas.prevision_asistentes.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PrevisionAsistentes. Datos de la pantalla prevision_asistentes.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.prevision_asistentes`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.mi_of`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mi_of`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/casas/prevision_asistentes_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
