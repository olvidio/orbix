---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Listado de actividades (tabla)"
pantalla: "actividades.pantalla.lista_activ"
preguntas: ["Que se puede hacer en Listado de actividades (tabla)?", "Que campos tiene Listado de actividades (tabla)?", "Que acciones hay en Listado de actividades (tabla)?"]
capacidades: ["actividades.lista_activ.gestionar"]
endpoints: ["/src/actividades/lista_activ_datos"]
source: "docs/catalogo/actividades/pantallas/lista_activ.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Listado de actividades (tabla)

## Resumen

Pantalla de **resultados tabulares** de actividades. Recibe por POST los filtros de `actividad_que` (`que=list_activ` / `list_activ_compl`) o de `lista_activ_que` (listados SR/SG) y llama a `lista_activ_datos` para obtener cabeceras, filas y título; monta la tabla con `Lista` y la muestra con navegación atrás.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.Gstack`
- `post.asist`
- `post.c_activ`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.periodo`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.seccion`
- `post.snom_tipo`
- `post.ssfsv`
- `post.stack`
- `post.status`
- `post.titulo`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.lista_activ.gestionar`

## Endpoints Relacionados

- `/src/actividades/lista_activ_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
