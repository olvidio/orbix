---
id: "encargossacd.propuestas_menu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Crear la tabla de propuestas"
capacidad: ""
pantallas_principales: ["encargossacd.pantalla.propuestas_menu"]
fragmentos: ["encargossacd.pantalla.propuestas_aprobar", "encargossacd.pantalla.propuestas_ajax"]
acciones: ["crear_tabla", "aprobar", "ir_modificar", "ir_lista_sacd", "ir_lista_enc"]
endpoints: ["/src/encargossacd/propuestas_ajax", "/src/encargossacd/propuestas_aprobar"]
estado_revision: "revisado"
---

# Flujo - Propuestas Menu

Punto de entrada del dominio propuestas (encargos SACD para nuevo curso).

## Objetivo De Usuario

Desde la pantalla de propuestas: volver a crear la tabla de trabajo, aprobar los cambios y pasarlos a los datos reales, o abrir la edición y los listados de propuestas. (Redactado desde el código; no contrastado con la pantalla.)

## Punto De Entrada

Menú **dre > Encargos > propuestas**.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.propuestas_menu`
- `encargossacd.pantalla.propuestas_aprobar`
- `encargossacd.pantalla.propuestas_ajax`

## Escenarios Inferidos

### Crear la tabla de propuestas

Pasos propuestos:
1. Pulsar la opción para crear la tabla de propuestas.
2. Confirmar el aviso: elimina la tabla anterior y la crea de nuevo.
3. Si no se ha podido crear, sale un aviso.

Endpoints asociados:
- `/src/encargossacd/propuestas_ajax`
- `/src/encargossacd/propuestas_aprobar`

### Aprobar las propuestas

Pasos propuestos:
1. Pulsar **aprobar**.
2. Leer y confirmar el aviso: tarda unos 30 segundos y no se puede deshacer.
3. Al terminar aparece «Hecho!».

Endpoints asociados:
- `/src/encargossacd/propuestas_ajax`
- `/src/encargossacd/propuestas_aprobar`

### Abrir la edición o los listados

Pasos propuestos:
1. Elegir **modificar propuestas**, **listado propuestas por sacd** o **listado propuestas por encargos**; se abre en la misma ventana.

Endpoints asociados:
- `/src/encargossacd/propuestas_ajax`
- `/src/encargossacd/propuestas_aprobar`

## Endpoints Del Flujo

- `/src/encargossacd/propuestas_ajax`
- `/src/encargossacd/propuestas_aprobar`

## Errores Conocidos

- `No se puede crear la tabla` (crear_tabla)
- Fallos de aprobar: sin mensaje `_()` explícito en el caso de uso (pendiente)

## Ruta de menú

- **Legacy:** dre > Encargos > propuestas
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > propuestas
