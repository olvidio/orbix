---
id: "encargossacd.sacd_ausencias_get.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Sacd Ausencias Get"
capacidad: "encargossacd.sacd_ausencias_get.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ausencias_get"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ausencias_get_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Ausencias Get

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_ausencias_get.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona SacdAusenciasGet. Datos para la ficha de ausencias de un SACD (frontend/encargossacd/controller/sacd_ausencias_get.php). Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo 7/4) y las filas asociadas al SACD. Con historial=1 incluye todas las ausencias; sin historial solo muestra las que aun tienen vigencia.

## Punto De Entrada

Fragmento AJAX embebido; sin entrada de menú directa.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ausencias_get`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.fin`
- `form.id_enc`
- `form.id_item`
- `form.inicio`
- `html.ok`
- `post.filtro_sacd`
- `post.historial`
- `post.id_nom`

Acciones JavaScript:
- `fnjs_date_fin`
- `fnjs_guardar`
- `fnjs_horario`
- `fnjs_mas_enc`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/sacd_ausencias_get_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

