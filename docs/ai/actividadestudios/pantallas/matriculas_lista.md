---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadestudios"
titulo: "Matriculas Lista"
pantalla: "actividadestudios.pantalla.matriculas_lista"
preguntas: ["Que se puede hacer en Matriculas Lista?", "Que campos tiene Matriculas Lista?", "Que acciones hay en Matriculas Lista?"]
capacidades: ["actividadestudios.matricula.gestionar", "actividadestudios.matriculas.gestionar"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matriculas_lista_data"]
source: "docs/catalogo/actividadestudios/pantallas/matriculas_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Matriculas Lista

## Resumen

Listado de matrículas (dossier).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.mod`
- `html.pau`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mod`
- `post.periodo`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Capacidades Relacionadas

- `actividadestudios.matricula.gestionar`
- `actividadestudios.matriculas.gestionar`

## Endpoints Relacionados

- `/src/actividadestudios/matricula_eliminar`
- `/src/actividadestudios/matriculas_lista_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
