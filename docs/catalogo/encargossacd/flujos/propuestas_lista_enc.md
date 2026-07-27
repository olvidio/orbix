---
id: "encargossacd.propuestas_lista_enc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Propuestas Lista Enc"
capacidad: ""
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.propuestas_lista_enc"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/propuestas_lista_enc_data"]
estado_revision: "revisado"
---

# Flujo - Propuestas Lista Enc

Consulta HTML de propuestas agrupadas por encargo/centro (solo lectura).

## Objetivo De Usuario

Ver el estado de las propuestas por encargos tras (o durante) la edición staging.

## Punto De Entrada

Desde hub propuestas → «listado propuestas por encargos».


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.propuestas_lista_enc`

## Escenarios Inferidos

### Obtener datos

1. FE llama `propuestas_lista_enc_data` con `filtro_ctr` (opcional).
2. Si `error` → echo del mensaje; si no → echo de `html`.

## Endpoints Del Flujo

- `/src/encargossacd/propuestas_lista_enc_data`

## Errores Conocidos

- `Debe crear la tabla de propuestas`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
