---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Tessera Ver"
pantalla: "notas.pantalla.tessera_ver"
preguntas: ["Que se puede hacer en Tessera Ver?", "Que campos tiene Tessera Ver?", "Que acciones hay en Tessera Ver?"]
capacidades: ["notas.tessera_ver.gestionar"]
endpoints: ["/src/notas/tessera_ver_data"]
source: "docs/catalogo/notas/pantallas/tessera_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tessera Ver

## Resumen

Tessera de una persona (vista HTML): muestra por cada asignatura del bienio+cuadrienio si esta pendiente, cursada o aprobada, con nota y fecha.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.sel`

## Acciones Detectadas

- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `notas.tessera_ver.gestionar`

## Endpoints Relacionados

- `/src/notas/tessera_ver_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
