---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Listado actividades San Gabriel (crt/cv)"
pantalla: "actividades.pantalla.lista_actividades_sg"
preguntas: ["Que se puede hacer en Listado actividades San Gabriel (crt/cv)?", "Que campos tiene Listado actividades San Gabriel (crt/cv)?", "Que acciones hay en Listado actividades San Gabriel (crt/cv)?"]
capacidades: ["actividades.lista_actividades_sg.gestionar"]
endpoints: ["/src/actividades/lista_actividades_sg_datos"]
source: "docs/catalogo/actividades/pantallas/lista_actividades_sg.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Listado actividades San Gabriel (crt/cv)

## Resumen

Listado de actividades **SG** (`tipo_activ_sg=crt|cv`) con filtros de periodo, estado, lugar y delegación. Incluye barra de periodo (`PeriodoQue`), formulario de selección múltiple y tabla vía `lista_actividades_sg_datos`. Periodo por defecto: `curso_crt` o `curso_ca` según el tipo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.mod`
- `form.queSel`
- `form.sel`
- `html.b_buscar`
- `html.mod`
- `post.Gstack`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.filtro_lugar`
- `post.id_ubi`
- `post.periodo`
- `post.que`
- `post.scroll_id`
- `post.sel`
- `post.stack`
- `post.status`
- `post.tipo_activ_sg`
- `post.year`

## Acciones Detectadas

- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Capacidades Relacionadas

- `actividades.lista_actividades_sg.gestionar`

## Endpoints Relacionados

- `/src/actividades/lista_actividades_sg_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
