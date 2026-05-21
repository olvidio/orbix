---
id: "actividades.lista_actividades_sg.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Lista Actividades Sg"
capacidad: "actividades.lista_actividades_sg.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.lista_actividades_sg"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_actividades_sg_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Actividades Sg

Propuesta generada automaticamente desde la capacidad `actividades.lista_actividades_sg.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaActividadesSgListado. JSON del listado para lista_actividades_sg: POST → {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.lista_actividades_sg`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.mod`
- `form.queSel`
- `form.sel`
- `html.b_buscar`
- `html.mod`
- `post.Gstack`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.filtro_lugar`
- `post.id_ubi`
- `post.periodo`
- `post.que`
- `post.scroll_id`
- `post.sel`
- `post.stack`
- `post.status`
- `post.tipo_activ_sg`
- `post.year`

Acciones JavaScript:
- `button:. _(`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/actividades/lista_actividades_sg_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
