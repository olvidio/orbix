---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Ver Encargos Zona"
pantalla: "misas.pantalla.ver_encargos_zona"
preguntas: ["Que se puede hacer en Ver Encargos Zona?", "Que campos tiene Ver Encargos Zona?", "Que acciones hay en Ver Encargos Zona?"]
capacidades: ["misas.eliminar_encargo_zona.gestionar", "misas.guardar_encargo_zona.gestionar", "misas.ver_encargos_zona.gestionar"]
endpoints: ["/src/misas/eliminar_encargo_zona", "/src/misas/guardar_encargo_zona", "/src/misas/ver_encargos_zona_data"]
source: "docs/catalogo/misas/pantallas/ver_encargos_zona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ver Encargos Zona

## Resumen

Fragmento SlickGrid de encargos 8100+ con modal alta/edición/borrado (`guardar_encargo_zona`, `eliminar_encargo_zona`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.descripcion_lugar`
- `form.encargo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_ubi`
- `form.id_zona`
- `form.idioma_enc`
- `form.observ`
- `form.orden`
- `form.prioridad`
- `html.nuevo`
- `post.id_zona`
- `post.orden`

## Acciones Detectadas

- `fnjs_generarNomEnc`
- `fnjs_nuevo`
- `fnjs_refresh_grid`

## Capacidades Relacionadas

- `misas.eliminar_encargo_zona.gestionar`
- `misas.guardar_encargo_zona.gestionar`
- `misas.ver_encargos_zona.gestionar`

## Endpoints Relacionados

- `/src/misas/eliminar_encargo_zona`
- `/src/misas/guardar_encargo_zona`
- `/src/misas/ver_encargos_zona_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
