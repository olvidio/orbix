---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "procesos"
titulo: "Procesos Ver"
pantalla: "procesos.pantalla.procesos_ver"
preguntas: ["Que se puede hacer en Procesos Ver?", "Que campos tiene Procesos Ver?", "Que acciones hay en Procesos Ver?"]
capacidades: ["procesos.procesos.gestionar", "procesos.procesos_depende.gestionar", "procesos.procesos_ver.gestionar"]
endpoints: ["/src/procesos/procesos_depende", "/src/procesos/procesos_update", "/src/procesos/procesos_ver_data"]
source: "docs/catalogo/procesos/pantallas/procesos_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Procesos Ver

## Resumen

Formulario modal de alta o edición de una tarea dentro de un tipo de proceso: fase, tarea, status, oficina responsable y dependencias de fases/tareas previas con mensaje de requisito.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.acc`
- `form.dep_num`
- `form.id_fase`
- `form.id_fase_previa`
- `form.id_of_responsable`
- `form.id_tarea`
- `form.id_tarea_previa`
- `form.mensaje_requisito`
- `form.status`
- `form.valor_depende`
- `post.id_item`
- `post.id_tipo_proceso`
- `post.mod`

## Acciones Detectadas

- `fnjs_get_depende`

## Capacidades Relacionadas

- `procesos.procesos.gestionar`
- `procesos.procesos_depende.gestionar`
- `procesos.procesos_ver.gestionar`

## Endpoints Relacionados

- `/src/procesos/procesos_depende`
- `/src/procesos/procesos_update`
- `/src/procesos/procesos_ver_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
