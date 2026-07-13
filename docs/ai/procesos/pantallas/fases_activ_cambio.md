---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "procesos"
titulo: "Fases Activ Cambio"
pantalla: "procesos.pantalla.fases_activ_cambio"
preguntas: ["Que se puede hacer en Fases Activ Cambio?", "Que campos tiene Fases Activ Cambio?", "Que acciones hay en Fases Activ Cambio?"]
capacidades: ["procesos.fases_activ_cambio.gestionar", "procesos.fases_activ_cambio_tipo_html.gestionar"]
endpoints: ["/src/actividades/actividad_tipo_get", "/src/procesos/fases_activ_cambio_get", "/src/procesos/fases_activ_cambio_tipo_html", "/src/procesos/fases_activ_cambio_update"]
source: "docs/catalogo/procesos/pantallas/fases_activ_cambio.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Fases Activ Cambio

## Resumen

Cambio de fase masivo en actividades: selector de tipo de actividad, filtro DL propia/otras, periodo, fase destino y acción marcar/desmarcar; muestra listado de actividades candidatas y permite aplicar el cambio.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.accion`
- `form.dl_propia`
- `form.empiezamax`
- `form.empiezamin`
- `form.entrada`
- `form.extendida`
- `form.id_fase_nueva`
- `form.id_fase_sel`
- `form.id_tipo_activ`
- `form.modo`
- `form.periodo`
- `form.salida`
- `form.year`
- `post.dl_propia`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_fase_nueva`
- `post.id_tipo_activ`
- `post.inicio`
- `post.periodo`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.stack`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `procesos.fases_activ_cambio.gestionar`
- `procesos.fases_activ_cambio_tipo_html.gestionar`

## Endpoints Relacionados

- `/src/actividades/actividad_tipo_get`
- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_tipo_html`
- `/src/procesos/fases_activ_cambio_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
