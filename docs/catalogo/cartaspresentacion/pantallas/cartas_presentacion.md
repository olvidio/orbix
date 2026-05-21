---
id: "cartaspresentacion.pantalla.cartas_presentacion"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion.php"
vistas: ["frontend/cartaspresentacion/view/cartas_presentacion.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cartaspresentacion/cartas_presentacion_shell_data"]
capacidades: ["cartaspresentacion.cartas_presentacion_shell.gestionar"]
campos: ["html.buscar"]
acciones: ["fnjs_cerrar", "fnjs_construir_desplegable", "fnjs_eliminar_cp", "fnjs_guardar_cp", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_poblacion", "fnjs_update_div", "fnjs_ver", "fnjs_ver_ubi"]
estado_revision: "generado"
---

# Cartas Presentacion

Pantalla principal del modulo `cartaspresentacion` — shell con filtro dl/r + poblacion, listado AJAX de centros y modal de modificacion.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion.php`

## Vistas Relacionadas

- `frontend/cartaspresentacion/view/cartas_presentacion.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/cartaspresentacion/cartas_presentacion_shell_data`

## Capacidades Relacionadas

- `cartaspresentacion.cartas_presentacion_shell.gestionar`

## Campos Detectados

- `html.buscar`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_construir_desplegable`
- `fnjs_eliminar_cp`
- `fnjs_guardar_cp`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_poblacion`
- `fnjs_update_div`
- `fnjs_ver`
- `fnjs_ver_ubi`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
