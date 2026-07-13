---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadestudios"
titulo: "Matriculas Pendientes"
pantalla: "actividadestudios.pantalla.matriculas_pendientes"
preguntas: ["Que se puede hacer en Matriculas Pendientes?", "Que campos tiene Matriculas Pendientes?", "Que acciones hay en Matriculas Pendientes?"]
capacidades: ["actividadestudios.matricula.gestionar", "actividadestudios.matriculas_pendientes.gestionar"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matriculas_pendientes_data"]
source: "docs/catalogo/actividadestudios/pantallas/matriculas_pendientes.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Matriculas Pendientes

## Resumen

Listado de matrículas con examen pendiente de acta (sin nota definitiva), con las mismas acciones de consulta y borrado que `matriculas_lista`. El HTML se genera en el controller (sin vista PHTML separada).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.mod`
- `html.pau`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Capacidades Relacionadas

- `actividadestudios.matricula.gestionar`
- `actividadestudios.matriculas_pendientes.gestionar`

## Endpoints Relacionados

- `/src/actividadestudios/matricula_eliminar`
- `/src/actividadestudios/matriculas_pendientes_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
