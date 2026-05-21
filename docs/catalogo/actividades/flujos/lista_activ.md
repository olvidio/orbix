---
id: "actividades.lista_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Lista Activ"
capacidad: "actividades.lista_activ.gestionar"
pantallas_principales: ["actividades.pantalla.lista_activ_que"]
fragmentos: ["actividades.pantalla.lista_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_activ_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Activ

Propuesta generada automaticamente desde la capacidad `actividades.lista_activ.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaActivTabla. JSON del listado lista_activ: filtros POST → {.

## Punto De Entrada

- `actividades.pantalla.lista_activ_que`

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.lista_activ`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.asist`
- `form.c_activ`
- `form.empiezamax`
- `form.empiezamin`
- `form.seccion`
- `form.status`
- `form.tit_list_grupo`
- `post.Gstack`
- `post.asist`
- `post.c_activ`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.periodo`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.seccion`
- `post.snom_tipo`
- `post.ssfsv`
- `post.stack`
- `post.status`
- `post.titulo`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/lista_activ_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
