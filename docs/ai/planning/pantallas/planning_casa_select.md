---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Selección de casas (planning)"
pantalla: "planning.pantalla.planning_casa_select"
preguntas: ["Que se puede hacer en Selección de casas (planning)?", "Que campos tiene Selección de casas (planning)?", "Que acciones hay en Selección de casas (planning)?"]
capacidades: []
endpoints: []
source: "docs/catalogo/planning/pantallas/planning_casa_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Selección de casas (planning)

## Resumen

Pantalla intermedia: lista las casas del grupo elegido y permite abrir el calendario, crear o modificar actividades de casa (`planning_casa_nueva` / `planning_casa_modificar` en módulo actividades).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_activ`
- `form.id_ubi`
- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.modelo`
- `post.periodo`
- `post.propuesta_calendario`
- `post.sin_activ`
- `post.year`

## Acciones Detectadas

- `fnjs_cambiar_activ`
- `fnjs_cerrar`
- `fnjs_nueva_activ`
- `fnjs_update_div`
- `fnjs_ver`

## Capacidades Relacionadas

- No hay capacidades relacionadas.

## Endpoints Relacionados

- No hay endpoints detectados.

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
