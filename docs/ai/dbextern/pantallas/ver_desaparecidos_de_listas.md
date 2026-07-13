---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dbextern"
titulo: "Aquinate con BDU desaparecida"
pantalla: "dbextern.pantalla.ver_desaparecidos_de_listas"
preguntas: ["Que se puede hacer en Aquinate con BDU desaparecida?", "Que campos tiene Aquinate con BDU desaparecida?", "Que acciones hay en Aquinate con BDU desaparecida?"]
capacidades: ["dbextern.sincro_baja.gestionar", "dbextern.ver_desaparecidos_de_listas.gestionar"]
endpoints: ["/src/dbextern/sincro_baja", "/src/dbextern/ver_desaparecidos_de_listas_datos"]
source: "docs/catalogo/dbextern/pantallas/ver_desaparecidos_de_listas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Aquinate con BDU desaparecida

## Resumen

Subpantalla del punto 8: personas Aquinate con `id_match` pero ficha BDU vacía o inexistente.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_nom_orbix`
- `form.tipo_persona`
- `post.ids_desaparecidos_de_listas`
- `post.tipo_persona`

## Acciones Detectadas

- `fnjs_baja`

## Capacidades Relacionadas

- `dbextern.sincro_baja.gestionar`
- `dbextern.ver_desaparecidos_de_listas.gestionar`

## Endpoints Relacionados

- `/src/dbextern/sincro_baja`
- `/src/dbextern/ver_desaparecidos_de_listas_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
