---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cambios"
titulo: "Lista de cambios"
pantalla: "cambios.pantalla.avisos_generar"
preguntas: ["Que se puede hacer en Lista de cambios?", "Que campos tiene Lista de cambios?", "Que acciones hay en Lista de cambios?"]
capacidades: ["cambios.avisos_generar.gestionar"]
endpoints: ["/src/cambios/avisos_generar_lista_data", "/src/cambios/cambio_usuario_eliminar", "/src/cambios/cambio_usuario_eliminar_hasta_fecha"]
source: "docs/catalogo/cambios/pantallas/avisos_generar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Lista de cambios

## Resumen

Pantalla de consulta y mantenimiento de cambios anotados pendientes de avisar (`CambioUsuario` con `avisado=false`). Los administradores pueden filtrar por usuario y tipo de aviso; el resto ve solo sus propios cambios.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.aviso_tipo`
- `form.id_usuario`
- `html.f_fin`
- `html.refresh`
- `post.Gstack`
- `post.aviso_tipo`
- `post.id_usuario`
- `post.refresh`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_borrar_hasta_fecha`
- `fnjs_enviar_formulario`
- `fnjs_selectAll`

## Capacidades Relacionadas

- `cambios.avisos_generar.gestionar`

## Endpoints Relacionados

- `/src/cambios/avisos_generar_lista_data`
- `/src/cambios/cambio_usuario_eliminar`
- `/src/cambios/cambio_usuario_eliminar_hasta_fecha`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
