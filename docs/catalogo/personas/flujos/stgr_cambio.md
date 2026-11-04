---
id: "personas.stgr_cambio.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Cambiar el nivel stgr de una persona"
capacidad: "personas.stgr_cambio.gestionar"
pantallas_principales: ["personas.pantalla.stgr_cambio"]
fragmentos: ["personas.pantalla.stgr_cambio"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/stgr_cambio_data"]
estado_revision: "revisado"
---

# Flujo - Cambiar nivel STGR (formulario)

Apertura del formulario de cambio de nivel STGR desde el listado.

## Objetivo De Usuario

Abrir el formulario para cambiar el nivel de estudios (stgr) de una persona.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**, **n de paso**, **agd r/dl**; también en vsm, vsg y scdl). Marcar una persona y pulsar **modificar stgr**.

## Escenarios Inferidos

### Cambiar el nivel stgr de una persona

Pasos propuestos:
1. Marcar exactamente una persona en la lista y pulsar **modificar stgr**.
2. Se abre «Cambiar el stgr» con su nombre y el desplegable de niveles: Bienio, Cuadrienio Año I, Cuadrienio Año II-IV, Repaso, centro estudios, Baja temporal, ap, pa, o ad, sin estudios, est. Ecles., bienio-cuadrienio.
3. Elegir el nivel y pulsar **Guardar** (sigue en el flujo «Guardar nivel stgr»).

Endpoints asociados:
- `/src/personas/stgr_cambio_data`

## Endpoints Del Flujo

- `/src/personas/stgr_cambio_data`

## Errores Conocidos

- `No existe la clase de la persona`
- `No se encuentra la persona`

## Ruta de menú

- sin entrada de menú en el índice.
