---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "profesores"
titulo: "Claustro por departamentos"
pantalla: "profesores.pantalla.lista_por_departamentos"
preguntas: ["Que se puede hacer en Claustro por departamentos?", "Que campos tiene Claustro por departamentos?", "Que acciones hay en Claustro por departamentos?"]
capacidades: ["profesores.lista_por_departamentos.gestionar"]
endpoints: ["/src/profesores/lista_por_departamentos"]
source: "docs/catalogo/profesores/pantallas/lista_por_departamentos.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Claustro por departamentos

## Resumen

Listado del claustro STGR agrupado por departamento: subsección **director** y cada **tipo de profesor**, con nombre y centro (y delegación en RSTGR). En ámbito regional muestra primero un filtro de delegaciones.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl`
- `post.dl`
- `post.filtro`

## Acciones Detectadas

- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `profesores.lista_por_departamentos.gestionar`

## Endpoints Relacionados

- `/src/profesores/lista_por_departamentos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
