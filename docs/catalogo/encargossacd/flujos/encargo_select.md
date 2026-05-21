---
id: "encargossacd.encargo_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Encargo Select"
capacidad: "encargossacd.encargo_select.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/encargo_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Encargo Select

Propuesta generada automaticamente desde la capacidad `encargossacd.encargo_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoSelect. Datos para la lista de encargos (encargo_select). El frontend construye la frontend\shared\web\Lista y los enlaces; aqui devolvemos unicamente los datos planos de cada fila.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_activ`
- `form.id_nom`
- `form.que`
- `form.scroll_id`
- `form.sel`
- `html.desc_enc`
- `html.ok`
- `html.que`
- `post.desc_enc`
- `post.id_tipo_enc`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_borrar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_horario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_strip_hash_sel`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/encargo_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
