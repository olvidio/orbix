---
id: "actividadestudios.pantalla.ca_posibles"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Ca Posibles"
controller: "frontend/actividadestudios/controller/ca_posibles.php"
vistas: ["frontend/actividadestudios/view/ca_posibles_cuadro.phtml", "frontend/actividadestudios/view/ca_posibles_lista.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/ca_posibles_data"]
capacidades: ["actividadestudios.ca_posibles.gestionar"]
campos: ["html.observ", "post.ca_estudios", "post.ca_repaso", "post.ca_todos", "post.empiezamax", "post.empiezamin", "post.grupo_estudios", "post.id_ctr_agd", "post.id_ctr_n", "post.idca", "post.na", "post.obj_pau", "post.periodo", "post.ref", "post.sel", "post.stack", "post.texto", "post.year"]
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Ca Posibles

Resultado del informe «posibles CA»: calcula y muestra, para un centro y periodo, los créditos
cursables de cada alumno por actividad (cuadro imprimible o listado resumido). Se invoca desde
`ca_posibles_que.php` al pulsar **ver cuadro**.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/ca_posibles.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/ca_posibles_cuadro.phtml` (modo cuadro)
- `frontend/actividadestudios/view/ca_posibles_lista.phtml` (modo lista)

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/ca_posibles_data`

## Capacidades Relacionadas

- `actividadestudios.ca_posibles.gestionar`

## Campos Detectados

- `html.observ`
- `post.ca_estudios`
- `post.ca_repaso`
- `post.ca_todos`
- `post.empiezamax`
- `post.empiezamin`
- `post.grupo_estudios`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.idca`
- `post.na`
- `post.obj_pau`
- `post.periodo`
- `post.ref`
- `post.sel`
- `post.stack`
- `post.texto`
- `post.year`

## Acciones Detectadas

- `fnjs_update_div` (enlace «ir a dossier de actividades» en modo lista)

## Manual De Usuario

Recibe por POST los filtros definidos en `ca_posibles_que` (centro N o AGD, periodo, tipo de CA,
delegación, formato de cabecera, referencia, etc.). Valida que haya centro seleccionado; si no,
termina con el mensaje «debe seleccionar un centro o grupo de centros».

Según la respuesta de `ca_posibles_data`:

- **Modo cuadro:** pinta una o más tablas (`ca_posibles_cuadro.phtml`) con alumnos en filas y
  actividades en columnas (cabecera horizontal para Excel o vertical para imprimir), agrupadas por
  bienio/cuadrienio/repaso/CE/otros.
- **Modo lista:** muestra actividades con asignaturas y créditos totales
  (`ca_posibles_lista.phtml`), con enlace para volver al dossier de actividades.

Los CA deben tener asignaturas y nivel STGR introducidos para aparecer en el cuadro.

## Ruta de menú

sin entrada de menú en el índice (destino del formulario de `ca_posibles_que`)
