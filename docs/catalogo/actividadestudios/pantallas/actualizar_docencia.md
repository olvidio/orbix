---
id: "actividadestudios.pantalla.actualizar_docencia"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Actualizar Docencia"
controller: "frontend/actividadestudios/controller/actualizar_docencia.php"
vistas: ["frontend/actividadestudios/view/actualizar_docencia.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/docencia_actualizar"]
capacidades: ["actividadestudios.docencia_actualizar.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.periodo", "form.year", "html.refresh", "post.continuar", "post.empiezamax", "post.empiezamin", "post.periodo", "post.year"]
acciones: ["fnjs_buscar", "fnjs_enviar_formulario", "fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Actualizar Docencia

Herramienta de mantenimiento que recalcula y graba en el dossier de actividad docente los datos
derivados de los cursos anuales (CA) terminados en un periodo elegido. Sucesor de
`apps/actividadestudios/controller/actualizar_docencia.php`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/actualizar_docencia.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/actualizar_docencia.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/docencia_actualizar` (`fnjs_buscar` con `continuar=1`)

## Capacidades Relacionadas

- `actividadestudios.docencia_actualizar.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.continuar`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Manual De Usuario

La pantalla tiene dos fases (`mod=inicio` / `mod=fin`):

1. **Selección de periodo:** muestra un formulario `PeriodoQue` (año, periodo — todo el año,
   trimestres, curso CA u otro con fechas — y botón **buscar**). El texto explica que solo se
   tendrán en cuenta los CA marcados como terminados.
2. **Ejecución:** al pulsar **buscar**, se envía el formulario con `continuar=1` y `refresh=1` al
   mismo controller, que llama a `docencia_actualizar` y muestra el mensaje de resultado
   (`txt_rta`).

La mutación es destructiva/actualizadora sobre el dossier de docencia; conviene acotar bien el
periodo antes de confirmar.

## Ruta de menú

- **Legacy:** vest > mantenimiento > actualizar docencia
- **Pills2:** ESTUDIOS > Datos e informes > Actualizar docencia; vest > mantenimiento > actualizar docencia
