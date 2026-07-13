---
id: "ubis.lista_ctrs.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Lista Ctrs"
capacidad: "ubis.lista_ctrs.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.lista_ctrs"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/lista_ctrs_data"]
estado_revision: "revisado"
---

# Flujo - Lista Ctrs

## Objetivo De Usuario

Lista centros tipo s de la delegación con el número de sacerdotes asignados en cada uno.

## Punto De Entrada

Menú Legacy: vsg > buscar > lista ctr i nº s. Pills2: vsg > buscar > lista ctr i nº s.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.lista_ctrs`

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
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/lista_ctrs_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** vsg > buscar > lista ctr i nº s
- **Pills2:** vsg > buscar > lista ctr i nº s
