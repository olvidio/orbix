---
id: "actividadestudios.ca_posibles_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Ca Posibles Que"
capacidad: "actividadestudios.ca_posibles_que.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.ca_posibles_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/ca_posibles_que_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Ca Posibles Que

Pantalla de filtros para el informe de posibles CA.

## Objetivo De Usuario

El usuario configura los filtros del informe de posibles CA: centro (N o AGD), periodo,
grupo de estudios y opciones de inclusión (estudios, repaso, todos). Al cargar la pantalla
obtiene los desplegables y textos iniciales.

## Punto De Entrada

Pantalla `ca_posibles_que` (`frontend/actividadestudios/controller/ca_posibles_que.php`):
al abrirse llama a `ca_posibles_que_data` para cargar desplegables de centros y grupo de
estudios. El formulario envía a `ca_posibles.php` al pulsar **buscar**.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.ca_posibles_que`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Abrir la entrada de menú **posibles ca**.
2. El sistema carga desplegables de centros N/AGD y texto de grupo vía `ca_posibles_que_data`.
3. El usuario ajusta periodo, centro y flags; al buscar pasa al flujo `ca_posibles`.

Endpoints asociados:
- `/src/actividadestudios/ca_posibles_que_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
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

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_n_a`

## Endpoints Del Flujo

- `/src/actividadestudios/ca_posibles_que_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > posibles ca > posibles ca.
- **Pills2:** vest > posibles ca > posibles ca.
