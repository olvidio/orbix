---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dbextern"
titulo: "BDU desaparecidas en Aquinate"
pantalla: "dbextern.pantalla.ver_desaparecidos_de_orbix"
preguntas: ["Que se puede hacer en BDU desaparecidas en Aquinate?", "Que campos tiene BDU desaparecidas en Aquinate?", "Que acciones hay en BDU desaparecidas en Aquinate?"]
capacidades: ["dbextern.sincro_desunir.gestionar", "dbextern.ver_desaparecidos_de_orbix.gestionar"]
endpoints: ["/src/dbextern/sincro_desunir", "/src/dbextern/ver_desaparecidos_de_orbix_datos"]
source: "docs/catalogo/dbextern/pantallas/ver_desaparecidos_de_orbix.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - BDU desaparecidas en Aquinate

## Resumen

Subpantalla del punto 3: personas en BDU con vínculo pero sin ficha activa en esta DL.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_nom_listas`
- `form.tipo_persona`
- `post.ids_desaparecidos_de_orbix`
- `post.tipo_persona`

## Acciones Detectadas

- `fnjs_desunir`

## Capacidades Relacionadas

- `dbextern.sincro_desunir.gestionar`
- `dbextern.ver_desaparecidos_de_orbix.gestionar`

## Endpoints Relacionados

- `/src/dbextern/sincro_desunir`
- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
