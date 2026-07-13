---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Buscar personas"
pantalla: "personas.pantalla.personas_que"
preguntas: ["Que se puede hacer en Buscar personas?", "Que campos tiene Buscar personas?", "Que acciones hay en Buscar personas?"]
capacidades: []
endpoints: []
source: "docs/catalogo/personas/pantallas/personas_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Buscar personas

## Resumen

Formulario de criterios de búsqueda. Al enviar, navega a `personas_select.php` con los filtros y parámetros de contexto (`tabla`, `na`, `tipo`, `es_sacd`) heredados del menú.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.apellido1`
- `form.apellido2`
- `form.centro`
- `form.cmb`
- `form.exacto`
- `form.nombre`

## Acciones Detectadas

- `fnjs_enviar_formulario`

## Capacidades Relacionadas

- No hay capacidades relacionadas.

## Endpoints Relacionados

- No hay endpoints detectados.

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
