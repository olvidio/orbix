---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Direcciones Tabla"
pantalla: "ubis.pantalla.direcciones_tabla"
preguntas: ["Que se puede hacer en Direcciones Tabla?", "Que campos tiene Direcciones Tabla?", "Que acciones hay en Direcciones Tabla?"]
capacidades: ["ubis.direcciones_tabla.gestionar"]
endpoints: ["/src/ubis/direcciones_tabla"]
source: "docs/catalogo/ubis/pantallas/direcciones_tabla.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Direcciones Tabla

## Resumen

Tabla AJAX de direcciones encontradas para asignar a un ubi.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.c_p`
- `post.ciudad`
- `post.id_ubi`
- `post.obj_dir`
- `post.pais`

## Acciones Detectadas

- `fnjs_update_div`

## Capacidades Relacionadas

- `ubis.direcciones_tabla.gestionar`

## Endpoints Relacionados

- `/src/ubis/direcciones_tabla`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
