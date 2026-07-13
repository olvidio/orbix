---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dbextern"
titulo: "Traslados desde otra DL"
pantalla: "dbextern.pantalla.ver_traslados"
preguntas: ["Que se puede hacer en Traslados desde otra DL?", "Que campos tiene Traslados desde otra DL?", "Que acciones hay en Traslados desde otra DL?"]
capacidades: ["dbextern.sincro_trasladar.gestionar", "dbextern.ver_traslados.gestionar"]
endpoints: ["/src/dbextern/sincro_trasladar", "/src/dbextern/ver_traslados_datos"]
source: "docs/catalogo/dbextern/pantallas/ver_traslados.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Traslados desde otra DL

## Resumen

Subpantalla del punto 2: personas unidas a BDU pero con ficha activa en otra DL Orbix.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl`
- `form.id_nom_orbix`
- `form.tipo_persona`
- `post.ids_traslados`
- `post.tipo_persona`

## Acciones Detectadas

- `fnjs_trasladar`

## Capacidades Relacionadas

- `dbextern.sincro_trasladar.gestionar`
- `dbextern.ver_traslados.gestionar`

## Endpoints Relacionados

- `/src/dbextern/sincro_trasladar`
- `/src/dbextern/ver_traslados_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
