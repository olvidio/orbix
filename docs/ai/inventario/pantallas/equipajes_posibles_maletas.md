---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Elegir maleta/grupo"
pantalla: "inventario.pantalla.equipajes_posibles_maletas"
preguntas: ["Que se puede hacer en Elegir maleta/grupo?", "Que campos tiene Elegir maleta/grupo?", "Que acciones hay en Elegir maleta/grupo?"]
capacidades: ["inventario.lista_equipajes_posibles_maletas.gestionar"]
endpoints: ["/src/inventario/lista_equipajes_posibles_maletas"]
source: "docs/catalogo/inventario/pantallas/equipajes_posibles_maletas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Elegir maleta/grupo

## Resumen

Selector maleta; puede crear grupo vía `equipajes_update_grupo`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_equipaje`

## Acciones Detectadas

- `fnjs_ver_docs`

## Capacidades Relacionadas

- `inventario.lista_equipajes_posibles_maletas.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_equipajes_posibles_maletas`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
