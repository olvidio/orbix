---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadplazas"
titulo: "Incorporar Peticion"
pantalla: "actividadplazas.pantalla.incorporar_peticion"
preguntas: ["Que se puede hacer en Incorporar Peticion?", "Que campos tiene Incorporar Peticion?", "Que acciones hay en Incorporar Peticion?"]
capacidades: ["actividadplazas.peticiones_incorporar.gestionar"]
endpoints: ["/src/actividadplazas/peticiones_incorporar"]
source: "docs/catalogo/actividadplazas/pantallas/incorporar_peticion.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Incorporar Peticion

## Resumen

Pantalla que dispara la incorporación de las primeras peticiones de plaza como asistencia (acción contra `/src/actividadplazas/peticiones_incorporar`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sactividad`
- `form.sasistentes`
- `post.sactividad`
- `post.sasistentes`

## Acciones Detectadas

- `fnjs_incorporar_peticiones`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `actividadplazas.peticiones_incorporar.gestionar`

## Endpoints Relacionados

- `/src/actividadplazas/peticiones_incorporar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
