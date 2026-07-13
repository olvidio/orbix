---
id: "ubis.delegacion_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Delegacion Que"
capacidad: "ubis.delegacion_que.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.delegacion_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/delegacion_que_data"]
estado_revision: "revisado"
---

# Flujo - Delegacion Que

## Objetivo De Usuario

Devuelve delegaciones destino disponibles para el traslado de ubis.

## Punto De Entrada

Menú Legacy: scdl > direcciones > listados. Pills2: Calendario > centros y casas > listados.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.delegacion_que`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_cmb_id_dl`
- `fnjs_trasladar`

## Endpoints Del Flujo

- `/src/ubis/delegacion_que_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados
