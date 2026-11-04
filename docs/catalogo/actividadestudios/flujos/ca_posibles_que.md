---
id: "actividadestudios.ca_posibles_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Ver el cuadro de posibles ca"
capacidad: "actividadestudios.ca_posibles_que.gestionar"
pantallas_principales: ["actividadestudios.pantalla.ca_posibles_que"]
fragmentos: ["actividadestudios.pantalla.ca_posibles_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/ca_posibles_que_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Ca Posibles Que

Pantalla de filtros para el informe de posibles CA.

## Objetivo De Usuario

Preparar el cuadro de posibles ca: qué personas podrían ir a cada ca según las asignaturas que les faltan.

## Punto De Entrada

Menú **vest > posibles ca > posibles ca**.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.ca_posibles_que`

## Escenarios Inferidos

### Ver el cuadro de posibles ca

Pasos propuestos:
1. Entrar en «Ver cuadro de posibles ca». Nota de la pantalla: para que salgan los ca en el cuadro deben tener introducidas las asignaturas y el campo de nivel de stgr.
2. Marcar qué ca se quieren: **ca de estudios**, **ca de repaso** o **todos los ca**.
3. Elegir los centros de los numerarios y de los agregados (o «todos los ctr»), el periodo (verano, curso, todo el año u otro) y el año.
4. Escoger el grupo de estudios y, si hace falta, un texto de referencia y el texto de cabecera: **horizontal (excel)** o **vertical (imprimir)**.
5. Pulsar **ver cuadro**.

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
