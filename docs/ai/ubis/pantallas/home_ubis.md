---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Home Ubis"
pantalla: "ubis.pantalla.home_ubis"
preguntas: ["Que se puede hacer en Home Ubis?", "Que campos tiene Home Ubis?", "Que acciones hay en Home Ubis?"]
capacidades: ["ubis.home_ubis.gestionar"]
endpoints: ["/src/ubis/home_ubis_data"]
source: "docs/catalogo/ubis/pantallas/home_ubis.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Home Ubis

## Resumen

Ficha resumen de un ubi con enlaces a edición, direcciones, telecomunicaciones y dossiers.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.bloque`
- `post.id_ubi`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_left_side_show`
- `fnjs_update_div`

## Capacidades Relacionadas

- `ubis.home_ubis.gestionar`

## Endpoints Relacionados

- `/src/ubis/home_ubis_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
