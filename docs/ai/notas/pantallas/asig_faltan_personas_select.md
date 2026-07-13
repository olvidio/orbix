---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Asig Faltan Personas Select"
pantalla: "notas.pantalla.asig_faltan_personas_select"
preguntas: ["Que se puede hacer en Asig Faltan Personas Select?", "Que campos tiene Asig Faltan Personas Select?", "Que acciones hay en Asig Faltan Personas Select?"]
capacidades: ["notas.asig_faltan_personas_select.gestionar"]
endpoints: ["/src/notas/asig_faltan_personas_select_data"]
source: "docs/catalogo/notas/pantallas/asig_faltan_personas_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Asig Faltan Personas Select

## Resumen

Resultado: alumnos a los que falta una asignatura concreta.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `post.b_c`
- `post.c1`
- `post.c2`
- `post.id_asignatura`
- `post.personas_agd`
- `post.personas_n`
- `post.stack`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_tesera`

## Capacidades Relacionadas

- `notas.asig_faltan_personas_select.gestionar`

## Endpoints Relacionados

- `/src/notas/asig_faltan_personas_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
