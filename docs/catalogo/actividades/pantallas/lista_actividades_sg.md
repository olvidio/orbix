---
id: "actividades.pantalla.lista_actividades_sg"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Listado actividades San Gabriel (crt/cv)"
controller: "frontend/actividades/controller/lista_actividades_sg.php"
vistas: ["frontend/actividades/view/lista_actividades_sg.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/lista_actividades_sg.php"]
endpoints: ["/src/actividades/lista_actividades_sg_datos"]
capacidades: ["actividades.lista_actividades_sg.gestionar"]
campos: ["form.mod", "form.queSel", "form.sel", "html.b_buscar", "html.mod", "post.Gstack", "post.continuar", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.filtro_lugar", "post.id_ubi", "post.periodo", "post.que", "post.scroll_id", "post.sel", "post.stack", "post.status", "post.tipo_activ_sg", "post.year"]
acciones: ["fnjs_borrar", "fnjs_buscar", "fnjs_enviar_formulario", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "revisado"
---

# Listado actividades San Gabriel (crt/cv)

Listado de actividades **SG** (`tipo_activ_sg=crt|cv`) con filtros de periodo,
estado, lugar y delegación. Incluye barra de periodo (`PeriodoQue`), formulario de
selección múltiple y tabla vía `lista_actividades_sg_datos`. Periodo por defecto:
`curso_crt` o `curso_ca` según el tipo.

Si hay más de 200 resultados, pide confirmación (`continuar`) antes de listar
(mismo patrón que `actividad_select`).

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/actividades/controller/lista_actividades_sg.php`
- Vista: `frontend/actividades/view/lista_actividades_sg.phtml`

## Endpoints Usados

- `/src/actividades/lista_actividades_sg_datos`

## Manual De Usuario

Listado *de la r/dl* para crt o cv SG: filtrar periodo/estado, buscar y actuar sobre
filas (abrir ficha, selección según `que`/`mod` del POST).

## Ruta de menú

- **Legacy:** vsg > crt > de la r/dl; vsg > cv > de la r/dl.
- **Pills2:** sin entrada dedicada en el índice (mismas rutas vsg).
