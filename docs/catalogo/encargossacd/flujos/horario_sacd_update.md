---
id: "encargossacd.horario_sacd_update.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Guardar un horario de sacerdote"
capacidad: "encargossacd.horario_sacd_update.gestionar"
pantallas_principales: ["encargossacd.pantalla.horario_sacd_update"]
fragmentos: ["encargossacd.pantalla.horario_sacd_update"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_sacd_update_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Horario Sacd Update

Propuesta generada automaticamente desde la capacidad `encargossacd.horario_sacd_update.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Guardar, modificar o quitar el horario de un sacerdote en un encargo.

## Punto De Entrada

Formulario de horario del sacerdote.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.horario_sacd_update`

## Escenarios Inferidos

### Guardar un horario de sacerdote

Pasos propuestos:
1. Rellenar el horario y pulsar **guardar horario**.
2. Para borrarlo, **eliminar horario** y confirmar «¿Está seguro que desea borrar este horario?».

Endpoints asociados:
- `/src/encargossacd/horario_sacd_update_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/horario_sacd_update_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

