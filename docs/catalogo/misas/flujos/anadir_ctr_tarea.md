---
id: "misas.anadir_ctr_tarea.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Añadir o quitar un centro en la plantilla"
capacidad: "misas.anadir_ctr_tarea.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/misas/anadir_ctr_tarea"]
estado_revision: "revisado"
---

# Flujo - Anadir ctr tarea

## Objetivo De Usuario

Añadir o quitar, en la plantilla, un centro asociado a una tarea.

## Punto De Entrada

Menú **dre > Misas > Modificar plantilla** (Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plantilla).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Añadir o quitar un centro en la plantilla

Pasos propuestos:
1. En **Modificar plantilla**, usar el botón de añadir fila de la tarea y elegir el centro.
2. Para quitarlo, pulsar quitar en la fila.

Endpoints asociados:
- `/src/misas/anadir_ctr_tarea`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/anadir_ctr_tarea`

## Errores Conocidos

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `opción no definida en switch en %s, linea %s`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
