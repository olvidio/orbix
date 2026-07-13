---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dbextern"
titulo: "Sincronizar con los datos de Listas"
pantalla: "dbextern.pantalla.sincro_index"
preguntas: ["Que se puede hacer en Sincronizar con los datos de Listas?", "Que campos tiene Sincronizar con los datos de Listas?", "Que acciones hay en Sincronizar con los datos de Listas?"]
capacidades: ["dbextern.refrescar_bdu.gestionar", "dbextern.sincro_index.gestionar", "dbextern.sincro_syncro.gestionar"]
endpoints: ["/src/dbextern/refrescar_bdu", "/src/dbextern/sincro_index_datos", "/src/dbextern/sincro_syncro"]
source: "docs/catalogo/dbextern/pantallas/sincro_index.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Sincronizar con los datos de Listas

## Resumen

Dashboard principal de sincronización BDU↔Aquinate: muestra fecha de actualización de `tmp_bdu`, contadores de las 9 situaciones y enlaces «ver» / «ejecutar» hacia subpantallas y mutaciones.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl_listas`
- `form.que`
- `form.region`
- `form.tipo_persona`
- `post.tipo`

## Acciones Detectadas

- `fnjs_refrescar`
- `fnjs_sincronizar`
- `fnjs_update_div`

## Capacidades Relacionadas

- `dbextern.refrescar_bdu.gestionar`
- `dbextern.sincro_index.gestionar`
- `dbextern.sincro_syncro.gestionar`

## Endpoints Relacionados

- `/src/dbextern/refrescar_bdu`
- `/src/dbextern/sincro_index_datos`
- `/src/dbextern/sincro_syncro`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
