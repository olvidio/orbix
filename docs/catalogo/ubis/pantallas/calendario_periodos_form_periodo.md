---
id: "ubis.pantalla.calendario_periodos_form_periodo"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "ubis"
nombre: "Calendario Periodos Form Periodo"
controller: "frontend/ubis/controller/calendario_periodos_form_periodo.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/calendario_periodos_form_periodo_data"]
capacidades: ["ubis.calendario_periodos_form_periodo.gestionar"]
campos: ["form.f_fin", "form.f_ini", "form.sfsv", "post.id_item"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Calendario Periodos Form Periodo

Formulario modal de edición o eliminación de un periodo de calendario existente.

## Tipo

- Subtipo: `modal`


- Controller: `frontend/ubis/controller/calendario_periodos_form_periodo.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/calendario_periodos_form_periodo_data`

## Capacidades Relacionadas

- `ubis.calendario_periodos_form_periodo.gestionar`

## Campos Detectados

- `form.f_fin`
- `form.f_ini`
- `form.sfsv`
- `post.id_item`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
