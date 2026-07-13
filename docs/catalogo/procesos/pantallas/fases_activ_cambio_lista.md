---
id: "procesos.pantalla.fases_activ_cambio_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Fases Activ Cambio Lista"
controller: "frontend/procesos/controller/fases_activ_cambio_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/procesos/fases_activ_cambio_lista"]
capacidades: ["procesos.fases_activ_cambio.gestionar"]
campos: ["form.sel"]
acciones: ["fnjs_cambiar", "fnjs_selectAll", "fnjs_ver_activ"]
estado_revision: "revisado"
---

# Fases Activ Cambio Lista

Fragmento AJAX que renderiza la tabla de actividades candidatas al cambio de fase, con selección múltiple, acción de cambiar fase y enlace para ver el proceso de una actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/fases_activ_cambio_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/procesos/fases_activ_cambio_lista`

## Capacidades Relacionadas

- `procesos.fases_activ_cambio.gestionar`

## Campos Detectados

- `form.sel`

## Acciones Detectadas

- `fnjs_cambiar`
- `fnjs_selectAll`
- `fnjs_ver_activ`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
