---
id: "encargossacd.horario_sacd_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Horario Sacd Ver"
capacidad: "encargossacd.horario_sacd_ver.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.horario_sacd_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_sacd_ver_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Horario Sacd Ver

Propuesta generada automaticamente desde la capacidad `encargossacd.horario_sacd_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoSacdHorarioVer. Datos del formulario horario sacd (ficha tareas).

## Punto De Entrada

Fragmento AJAX embebido; sin entrada de menú directa.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.horario_sacd_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.desc_enc`
- `html.dia`
- `html.dia_inc`
- `html.dia_num`
- `html.dia_ref`
- `html.f_fin`
- `html.f_ini`
- `html.filtro_sacd`
- `html.h_fin`
- `html.h_ini`
- `html.id_enc`
- `html.id_item`
- `html.id_nom`
- `html.mas_menos`
- `html.mod`
- `post.desc_enc`
- `post.filtro_sacd`
- `post.id_enc`
- `post.id_item`
- `post.id_nom`
- `post.mod`

Acciones JavaScript:
- `fnjs_enviar_formulario`
- `fnjs_guardar_horario`

## Endpoints Del Flujo

- `/src/encargossacd/horario_sacd_ver_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

