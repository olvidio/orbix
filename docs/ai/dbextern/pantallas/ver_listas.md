---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dbextern"
titulo: "Personas BDU no unidas"
pantalla: "dbextern.pantalla.ver_listas"
preguntas: ["Que se puede hacer en Personas BDU no unidas?", "Que campos tiene Personas BDU no unidas?", "Que acciones hay en Personas BDU no unidas?"]
capacidades: ["dbextern.sincro.gestionar", "dbextern.sincro_crear_todos.gestionar", "dbextern.sincro_unir.gestionar", "dbextern.ver_listas.gestionar"]
endpoints: ["/src/dbextern/sincro_crear", "/src/dbextern/sincro_crear_todos", "/src/dbextern/sincro_unir", "/src/dbextern/ver_listas_datos"]
source: "docs/catalogo/dbextern/pantallas/ver_listas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Personas BDU no unidas

## Resumen

Subpantalla del punto 4: recorre personas de la BDU sin `id_match`, muestra candidatos Orbix para unir o permite crear ficha nueva / crear todas.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl`
- `form.id`
- `form.id_nom_listas`
- `form.id_orbix`
- `form.region`
- `form.tipo_persona`
- `html.mov`
- `post.dl`
- `post.id`
- `post.mov`
- `post.region`
- `post.tipo_persona`

## Acciones Detectadas

- `fnjs_crear`
- `fnjs_crear_todos`
- `fnjs_submit`
- `fnjs_unir`

## Capacidades Relacionadas

- `dbextern.sincro.gestionar`
- `dbextern.sincro_crear_todos.gestionar`
- `dbextern.sincro_unir.gestionar`
- `dbextern.ver_listas.gestionar`

## Endpoints Relacionados

- `/src/dbextern/sincro_crear`
- `/src/dbextern/sincro_crear_todos`
- `/src/dbextern/sincro_unir`
- `/src/dbextern/ver_listas_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
