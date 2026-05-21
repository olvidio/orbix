---
id: "notas.actividades_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Actividades Buscar"
capacidad: "notas.actividades_buscar.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.actividad_buscar_form"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/actividades_buscar_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividades Buscar

Propuesta generada automaticamente desde la capacidad `notas.actividades_buscar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadesBuscar. Datos (delegaciones + actividades) para el dialogo "buscar actividad" que abre frontend/notas/controller/actividad_buscar_form.php desde form_notas_de_una_persona.phtml al modificar una nota asociada a una actividad.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.actividad_buscar_form`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.observ`
- `form.pres_mail`
- `form.pres_nom`
- `form.pres_telf`
- `form.zona`
- `post.dl_org`
- `post.f_acta_iso`
- `post.id_activ`

Acciones JavaScript:
- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_update_activ`

## Endpoints Del Flujo

- `/src/notas/actividades_buscar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
