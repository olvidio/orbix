---
id: "ubis.ubis_editar_normalize_dl.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Ficha de un centro o casa de la propia delegación"
capacidad: "ubis.ubis_editar_normalize_dl.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_editar_normalize_dl_data"]
estado_revision: "revisado"
---

# Flujo - Ubis Editar Normalize Dl

## Objetivo De Usuario

Ajuste interno: si la ficha pertenece a la propia delegación, se trata como centro/casa de la dl y se habilitan todos sus campos.

## Punto De Entrada

Sin entrada de menú propia: se abre desde la ficha del centro o casa.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ficha de un centro o casa de la propia delegación

Pasos propuestos:
1. Abrir la ficha de un centro o casa de la propia delegación.
2. Se muestran todos los campos editables; en los de otras delegaciones, solo consulta.

Endpoints asociados:
- `/src/ubis/ubis_editar_normalize_dl_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/ubis_editar_normalize_dl_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
