---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadestudios"
titulo: "Actualizar Docencia"
pantalla: "actividadestudios.pantalla.actualizar_docencia"
preguntas: ["Que se puede hacer en Actualizar Docencia?", "Que campos tiene Actualizar Docencia?", "Que acciones hay en Actualizar Docencia?"]
capacidades: ["actividadestudios.docencia_actualizar.gestionar"]
endpoints: ["/src/actividadestudios/docencia_actualizar"]
source: "docs/catalogo/actividadestudios/pantallas/actualizar_docencia.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Actualizar Docencia

## Resumen

Herramienta de mantenimiento que recalcula y graba en el dossier de actividad docente los datos derivados de los cursos anuales (CA) terminados en un periodo elegido. Sucesor de `apps/actividadestudios/controller/actualizar_docencia.php`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.continuar`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `actividadestudios.docencia_actualizar.gestionar`

## Endpoints Relacionados

- `/src/actividadestudios/docencia_actualizar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
