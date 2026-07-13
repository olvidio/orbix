---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "devel_db_admin"
titulo: "Db Mover Que"
pantalla: "devel_db_admin.pantalla.db_mover_que"
preguntas: ["Que se puede hacer en Db Mover Que?", "Que campos tiene Db Mover Que?", "Que acciones hay en Db Mover Que?"]
capacidades: ["devel_db_admin.db_propiedades.gestionar"]
endpoints: ["/src/devel_db_admin/db_propiedades_data"]
source: "docs/catalogo/devel_db_admin/pantallas/db_mover_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Db Mover Que

## Resumen

Selección de tabla a mover de sv a sv-e por esquema.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.tabla`
- `html.bcrear`

## Acciones Detectadas

- `fnjs_db_mover_tabla`
- `fnjs_enviar_formulario`

## Capacidades Relacionadas

- `devel_db_admin.db_propiedades.gestionar`

## Endpoints Relacionados

- `/src/devel_db_admin/db_propiedades_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
