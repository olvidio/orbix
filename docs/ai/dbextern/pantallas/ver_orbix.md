---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dbextern"
titulo: "Personas Aquinate sin BDU"
pantalla: "dbextern.pantalla.ver_orbix"
preguntas: ["Que se puede hacer en Personas Aquinate sin BDU?", "Que campos tiene Personas Aquinate sin BDU?", "Que acciones hay en Personas Aquinate sin BDU?"]
capacidades: ["dbextern.sincro_unir.gestionar", "dbextern.ver_orbix.gestionar"]
endpoints: ["/src/dbextern/sincro_unir", "/src/dbextern/ver_orbix_datos"]
source: "docs/catalogo/dbextern/pantallas/ver_orbix.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Personas Aquinate sin BDU

## Resumen

Subpantalla del punto 9: personas activas en Aquinate sin correspondencia en BDU; permite unir con candidato BDU si existe.

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

- `fnjs_submit`
- `fnjs_unir_bdu`

## Capacidades Relacionadas

- `dbextern.sincro_unir.gestionar`
- `dbextern.ver_orbix.gestionar`

## Endpoints Relacionados

- `/src/dbextern/sincro_unir`
- `/src/dbextern/ver_orbix_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
