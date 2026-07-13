---
id: "notas.tessera_copiar_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Tessera Copiar Select"
capacidad: "notas.tessera_copiar_select.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.tessera_copiar_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/tessera_copiar_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Tessera Copiar Select

Propuesta generada automaticamente desde la capacidad `notas.tessera_copiar_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Elegir destino y ejecutar copia de tessera.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.tessera_copiar_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_nom_dst`
- `html.copiar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_copiar`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/notas/tessera_copiar_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.
