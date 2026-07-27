---
id: "encargossacd.propuestas_menu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Propuestas Menu"
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

Desde el hub: regenerar tabla staging, aprobar cambios a producción, o abrir la edición /
listados de propuestas.

## Punto De Entrada

Menú: dre > Encargos > propuestas.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.propuestas_menu`
- `encargossacd.pantalla.propuestas_aprobar`
- `encargossacd.pantalla.propuestas_ajax`

## Escenarios Inferidos

### Crear tabla staging

1. Confirm JS («Elimina la tabla…»).
2. POST a FE `propuestas_ajax.php?que=crear_tabla` → API `propuestas_ajax`.
3. Alert si `success !== true`.

### Aprobar propuestas

1. Confirm JS (aviso de ~30 s e irreversibilidad).
2. Carga FE `propuestas_aprobar.php` → API `propuestas_aprobar`.
3. Muestra texto «Hecho!» en `#main`.

### Navegar a modificar / listados

1. `fnjs_update_div` hacia `propuestas_lista`, `propuestas_lista_sacd` o `propuestas_lista_enc`.

## Endpoints Del Flujo

- `/src/encargossacd/propuestas_ajax`
- `/src/encargossacd/propuestas_aprobar`

## Errores Conocidos

- `No se puede crear la tabla` (crear_tabla)
- Fallos de aprobar: sin mensaje `_()` explícito en el caso de uso (pendiente)

## Ruta de menú

- **Legacy:** dre > Encargos > propuestas
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > propuestas
