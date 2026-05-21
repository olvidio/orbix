---
id: "inventario.pantalla.equipajes_movimientos_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Movimientos Que"
controller: "frontend/inventario/controller/equipajes_movimientos_que.php"
vistas: ["frontend/inventario/view/equipajes_movimientos_que.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/equipajes_movimientos.php"]
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
capacidades: ["inventario.lista_equipajes_desde_fecha.gestionar"]
campos: ["form.sel"]
acciones: ["fnjs_lista_docs", "fnjs_ver_movimientos"]
estado_revision: "generado"
---

# Equipajes Movimientos Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_movimientos_que.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_movimientos_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/equipajes_movimientos.php`

## Endpoints Usados

- `/src/inventario/lista_equipajes_desde_fecha`

## Capacidades Relacionadas

- `inventario.lista_equipajes_desde_fecha.gestionar`

## Campos Detectados

- `form.sel`

## Acciones Detectadas

- `fnjs_lista_docs`
- `fnjs_ver_movimientos`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
