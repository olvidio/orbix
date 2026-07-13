---
id: "personas.traslado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Trasladar persona"
capacidad: "personas.traslado.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.traslado_form"]
acciones: ["ver_formulario", "crear_actualizar"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
estado_revision: "revisado"
---

# Flujo - Trasladar persona

Cambio de centro y/o delegación con registro en dossier de traslados.

## Objetivo De Usuario

Mover una persona a otro centro o delegación, documentando fechas y situación.

## Punto De Entrada

- Ficha edición: enlace traslado.
- Listado: «cambio de ctr» (`sm`) — abre formulario de centro.

## Escenarios

### Ver formulario

1. Abrir traslado con persona seleccionada (`id_pau`, `obj_pau`).
2. `traslado_form_data` precarga ctr/dl actuales y opciones.
3. No disponible para personas de paso publicadas.

### Aplicar traslado

1. Rellenar nuevo centro y/o nueva delegación con fechas.
2. Si hay cambio de dl, elegir situación válida.
3. Guardar → `traslado_update`; se abre dossier tipo 1004.

## Endpoints Del Flujo

- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Errores Conocidos

- `con las personas de paso no tiene sentido.`
- `Falta una situación válida`
- `Faltan id_pau u obj_pau`

## Ruta de menú

- sin entrada de menú en el índice.
