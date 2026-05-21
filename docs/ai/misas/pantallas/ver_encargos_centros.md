---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Ver Encargos Centros"
pantalla: "misas.pantalla.ver_encargos_centros"
preguntas: ["Que se puede hacer en Ver Encargos Centros?", "Que campos tiene Ver Encargos Centros?", "Que acciones hay en Ver Encargos Centros?"]
capacidades: ["misas.desplegable_encargos.gestionar", "misas.eliminar_encargo_centro.gestionar", "misas.guardar_encargo_centro.gestionar", "misas.ver_encargos_centros.gestionar"]
endpoints: ["/src/misas/desplegable_encargos", "/src/misas/eliminar_encargo_centro", "/src/misas/guardar_encargo_centro", "/src/misas/ver_encargos_centros_data"]
source: "docs/catalogo/misas/pantallas/ver_encargos_centros.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ver Encargos Centros

## Resumen

Descripcion funcional pendiente de revisar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_ctr`
- `form.id_enc`
- `form.id_item`
- `form.id_zona`
- `html.nuevo`
- `post.id_zona`

## Acciones Detectadas

- `fnjs_construir_desplegable`
- `fnjs_nuevo`
- `fnjs_prepara_select_encargo`
- `fnjs_refresh_grid`

## Capacidades Relacionadas

- `misas.desplegable_encargos.gestionar`
- `misas.eliminar_encargo_centro.gestionar`
- `misas.guardar_encargo_centro.gestionar`
- `misas.ver_encargos_centros.gestionar`

## Endpoints Relacionados

- `/src/misas/desplegable_encargos`
- `/src/misas/eliminar_encargo_centro`
- `/src/misas/guardar_encargo_centro`
- `/src/misas/ver_encargos_centros_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
