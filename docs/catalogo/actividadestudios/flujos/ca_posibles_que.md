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
estado_revision: "generado"
---

# Flujo - Gestionar Ca Posibles Que

Propuesta generada automaticamente desde la capacidad `actividadestudios.ca_posibles_que.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CaPosiblesQue. Desplegables y texto de grupo para ca_posibles_que.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.ca_posibles_que`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
