---
id: "ubis.pantalla.delegacion_que"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "ubis"
nombre: "Delegacion Que"
controller: "frontend/ubis/controller/delegacion_que.php"
vistas: ["frontend/ubis/view/delegaciones.phtml"]
fragmentos_frontend: []
endpoints: ["/src/ubis/delegacion_que_data"]
capacidades: ["ubis.delegacion_que.gestionar"]
campos: []
acciones: ["fnjs_cerrar", "fnjs_cmb_id_dl", "fnjs_trasladar"]
estado_revision: "revisado"
---

# Delegacion Que

Modal de selección de delegación destino para trasladar ubis desde list_ctr.

## Tipo

- Subtipo: `modal`


- Controller: `frontend/ubis/controller/delegacion_que.php`

## Vistas Relacionadas

- `frontend/ubis/view/delegaciones.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/delegacion_que_data`

## Capacidades Relacionadas

- `ubis.delegacion_que.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_cmb_id_dl`
- `fnjs_trasladar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
