---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Direcciones Que"
pantalla: "ubis.pantalla.direcciones_que"
preguntas: ["Que se puede hacer en Direcciones Que?", "Que campos tiene Direcciones Que?", "Que acciones hay en Direcciones Que?"]
capacidades: ["ubis.direcciones_que.gestionar"]
endpoints: ["/src/ubis/direcciones_que"]
source: "docs/catalogo/ubis/pantallas/direcciones_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Direcciones Que

## Resumen

Formulario de criterios para buscar direcciones existentes a asignar a un ubi.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.c_p`
- `form.ciudad`
- `form.id_ubi`
- `form.obj_dir`
- `form.pais`
- `html.btn_ok`
- `post.id_ubi`
- `post.obj_dir`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Capacidades Relacionadas

- `ubis.direcciones_que.gestionar`

## Endpoints Relacionados

- `/src/ubis/direcciones_que`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
