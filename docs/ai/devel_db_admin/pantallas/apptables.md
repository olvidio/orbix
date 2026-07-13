---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "devel_db_admin"
titulo: "Apptables"
pantalla: "devel_db_admin.pantalla.apptables"
preguntas: ["Que se puede hacer en Apptables?", "Que campos tiene Apptables?", "Que acciones hay en Apptables?"]
capacidades: ["devel_db_admin.apptables_apps.gestionar", "devel_db_admin.db_propiedades.gestionar"]
endpoints: ["/src/devel_db_admin/apptables_apps_data", "/src/devel_db_admin/db_propiedades_data"]
source: "docs/catalogo/devel_db_admin/pantallas/apptables.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Apptables

## Resumen

Gestión de tablas globales y por esquema de cada aplicación instalada (crear/eliminar/rellenar).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.esquema`
- `form.id_app`
- `html.bce`
- `html.bcg`
- `html.bee`
- `html.beg`

## Acciones Detectadas

- `fnjs_db`
- `fnjs_enviar_formulario`

## Capacidades Relacionadas

- `devel_db_admin.apptables_apps.gestionar`
- `devel_db_admin.db_propiedades.gestionar`

## Endpoints Relacionados

- `/src/devel_db_admin/apptables_apps_data`
- `/src/devel_db_admin/db_propiedades_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
