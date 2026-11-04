---
id: "ubis.lista_ctrs.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Consultar los sacerdotes por centro"
capacidad: "ubis.lista_ctrs.gestionar"
pantallas_principales: ["ubis.pantalla.lista_ctrs"]
fragmentos: ["ubis.pantalla.lista_ctrs"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/lista_ctrs_data"]
estado_revision: "revisado"
---

# Flujo - Lista Ctrs

## Objetivo De Usuario

Ver los centros de tipo s de la delegación con el número de sacerdotes asignados a cada uno.

## Punto De Entrada

Menú **vsg > buscar > lista ctr i nº s**.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.lista_ctrs`

## Escenarios Inferidos

### Consultar los sacerdotes por centro

Pasos propuestos:
1. Entrar en **lista ctr i nº s**.
2. Se muestra la tabla con cada centro y el número de sacerdotes que tiene.

Endpoints asociados:
- `/src/ubis/lista_ctrs_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/lista_ctrs_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** vsg > buscar > lista ctr i nº s
- **Pills2:** vsg > buscar > lista ctr i nº s
