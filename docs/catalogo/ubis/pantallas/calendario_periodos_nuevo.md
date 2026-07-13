---
id: "ubis.pantalla.calendario_periodos_nuevo"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "ubis"
nombre: "Calendario Periodos Nuevo"
controller: "frontend/ubis/controller/calendario_periodos_nuevo.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/calendario_periodos_nuevo_data"]
capacidades: ["ubis.calendario_periodos_nuevo.gestionar"]
campos: ["form.f_fin", "form.f_ini", "form.sfsv", "post.id_ubi", "post.year"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Calendario Periodos Nuevo

Formulario modal de alta de periodo de calendario con fechas y sfsv sugeridos.

## Tipo

- Subtipo: `modal`


- Controller: `frontend/ubis/controller/calendario_periodos_nuevo.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/calendario_periodos_nuevo_data`

## Capacidades Relacionadas

- `ubis.calendario_periodos_nuevo.gestionar`

## Campos Detectados

- `form.f_fin`
- `form.f_ini`
- `form.sfsv`
- `post.id_ubi`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
