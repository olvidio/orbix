---
id: "encargossacd.propuestas_lista_enc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Ver las propuestas por encargos"
capacidad: ""
pantallas_principales: ["encargossacd.pantalla.propuestas_lista_enc"]
fragmentos: ["encargossacd.pantalla.propuestas_lista_enc"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/propuestas_lista_enc_data"]
estado_revision: "revisado"
---

# Flujo - Propuestas Lista Enc

Consulta HTML de propuestas agrupadas por encargo/centro (solo lectura).

## Objetivo De Usuario

Ver cómo van las propuestas, por encargos, después (o durante) la edición. (Redactado desde el código; no contrastado con la pantalla.)

## Punto De Entrada

Desde el menú de propuestas, opción **listado propuestas por encargos**.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.propuestas_lista_enc`

## Escenarios Inferidos

### Ver las propuestas por encargos

Pasos propuestos:
1. Abrir **listado propuestas por encargos**.
2. Si se quiere, filtrar por centro.
3. Se muestra el listado. Si todavía no se ha creado la tabla de propuestas, avisa «Debe crear la tabla de propuestas».

Endpoints asociados:
- `/src/encargossacd/propuestas_lista_enc_data`

## Endpoints Del Flujo

- `/src/encargossacd/propuestas_lista_enc_data`

## Errores Conocidos

- `Debe crear la tabla de propuestas`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
