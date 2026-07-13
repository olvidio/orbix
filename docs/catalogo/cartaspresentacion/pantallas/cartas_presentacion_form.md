---
id: "cartaspresentacion.pantalla.cartas_presentacion_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion Form"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion_form.php"
vistas: ["frontend/cartaspresentacion/view/cartas_presentacion_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cartaspresentacion/carta_presentacion_form_data"]
capacidades: ["cartaspresentacion.carta_presentacion.gestionar"]
campos: ["html.observ", "html.pres_mail", "html.pres_nom", "html.pres_telf", "html.zona", "post.id_direccion", "post.id_ubi"]
acciones: ["fnjs_cerrar", "fnjs_guardar_cp"]
estado_revision: "revisado"
---

# Cartas Presentacion Form

Modal AJAX con el formulario de modificación de una carta de presentación.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_form.php`

## Vistas Relacionadas

- `frontend/cartaspresentacion/view/cartas_presentacion_form.phtml`

## Endpoints Usados

- `/src/cartaspresentacion/carta_presentacion_form_data`

## Campos Detectados

- `html.pres_nom`, `html.pres_telf`, `html.pres_mail`, `html.zona`, `html.observ`
- `post.id_ubi`, `post.id_direccion` (hidden vía `hash_update`)

## Acciones Detectadas

- `fnjs_guardar_cp` — submit hacia `carta_presentacion_update` (definido en la shell padre)
- `fnjs_cerrar` — cierra el modal

## Manual De Usuario

Se abre desde el listado de la pantalla principal al pulsar **director**. Muestra el nombre del
centro y los campos editables. Si no hay permiso (otra dl), muestra el mensaje de error. **Guardar**
persiste la carta; **cancel** cierra sin cambios.

## Ruta de menú

sin entrada de menú en el índice (fragmento modal desde `cartas_presentacion`).
