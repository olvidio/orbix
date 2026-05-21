---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cambios"
titulo: "Avisos Generar"
pantalla: "cambios.pantalla.avisos_generar"
preguntas: ["Que se puede hacer en Avisos Generar?", "Que campos tiene Avisos Generar?", "Que acciones hay en Avisos Generar?"]
capacidades: ["cambios.avisos_generar.gestionar"]
endpoints: ["/src/cambios/avisos_generar_lista_data"]
source: "docs/catalogo/cambios/pantallas/avisos_generar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Avisos Generar

## Resumen

Pantalla: listado de avisos (cambios anotados) del usuario conectado o, para admins, del usuario seleccionado en el formulario superior.

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

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
