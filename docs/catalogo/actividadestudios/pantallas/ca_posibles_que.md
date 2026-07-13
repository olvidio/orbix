---
id: "actividadestudios.pantalla.ca_posibles_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Ca Posibles Que"
controller: "frontend/actividadestudios/controller/ca_posibles_que.php"
vistas: ["frontend/actividadestudios/view/ca_posibles_que.phtml"]
fragmentos_frontend: ["frontend/actividadestudios/controller/ca_posibles.php"]
endpoints: ["/src/actividadestudios/ca_posibles_que_data"]
capacidades: ["actividadestudios.ca_posibles_que.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.id_ctr_agd", "form.id_ctr_n", "form.periodo", "form.ref", "form.texto", "form.year", "html.btn1", "html.ca_estudios", "html.ca_repaso", "html.ca_todos", "html.grupo_estudios", "html.na", "html.ref", "html.texto", "post.actividad_val", "post.ca_estudios", "post.ca_repaso", "post.ca_todos", "post.empiezamax", "post.empiezamin", "post.grupo_estudios", "post.iasistentes_val", "post.id_ctr_agd", "post.id_ctr_n", "post.na", "post.periodo", "post.ref", "post.stack", "post.year"]
acciones: ["fnjs_buscar", "fnjs_comprobar_fecha", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_n_a"]
estado_revision: "revisado"
---

# Ca Posibles Que

Formulario de filtros para el informe de créditos cursables por alumno y CA. Al enviarlo abre
`ca_posibles.php` con el cuadro o listado resultante.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/ca_posibles_que.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/ca_posibles_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadestudios/controller/ca_posibles.php`

## Endpoints Usados

- `/src/actividadestudios/ca_posibles_que_data` (desplegables de centros y grupo de delegación)

## Capacidades Relacionadas

- `actividadestudios.ca_posibles_que.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_ctr_agd`
- `form.id_ctr_n`
- `form.periodo`
- `form.ref`
- `form.texto`
- `form.year`
- `html.btn1`
- `html.ca_estudios`
- `html.ca_repaso`
- `html.ca_todos`
- `html.grupo_estudios`
- `html.na`
- `html.ref`
- `html.texto`
- `post.actividad_val`
- `post.ca_estudios`
- `post.ca_repaso`
- `post.ca_todos`
- `post.empiezamax`
- `post.empiezamin`
- `post.grupo_estudios`
- `post.iasistentes_val`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.na`
- `post.periodo`
- `post.ref`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_n_a`

## Manual De Usuario

Pantalla de criterios previos al informe:

1. Marcar tipos de CA (estudios, repaso, todos).
2. Elegir centro numerario (`id_ctr_n`) o agregado (`id_ctr_agd`); `fnjs_n_a` limpia el otro
   desplegable.
3. Definir periodo de actividades (`PeriodoQue`: verano, curso, trimestres, otro con fechas
   validadas por `fnjs_comprobar_fecha`).
4. Elegir delegación (mi grupo / todos) y formato del cuadro (referencia, cabecera horizontal Excel
   vs vertical imprimir).
5. Pulsar **ver cuadro** (`fnjs_buscar`): envía el formulario a `ca_posibles.php`.

Por defecto, si no hay estado previo, `ca_todos` queda marcado.

## Ruta de menú

- **Legacy:** vest > posibles ca > posibles ca
- **Pills2:** vest > posibles ca > posibles ca
