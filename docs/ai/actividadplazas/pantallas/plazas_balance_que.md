---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadplazas"
titulo: "Plazas Balance Que"
pantalla: "actividadplazas.pantalla.plazas_balance_que"
preguntas: ["Que se puede hacer en Plazas Balance Que?", "Que campos tiene Plazas Balance Que?", "Que acciones hay en Plazas Balance Que?"]
capacidades: ["actividadplazas.plazas_balance_que.gestionar"]
endpoints: ["/src/actividadplazas/plazas_balance_que_data"]
source: "docs/catalogo/actividadplazas/pantallas/plazas_balance_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Plazas Balance Que

## Resumen

Pantalla de filtro para el balance de plazas entre dos dl: muestra un desplegable con las dl disponibles y un `#comparativa` vacio que se rellena via AJAX con `plazas_balance_dl.php` (frontend, devuelve HTML) al cambiar el valor del select.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl`
- `form.id_tipo_activ`
- `post.id_tipo_activ`
- `post.sactividad`
- `post.sasistentes`

## Acciones Detectadas

- `fnjs_comparativa`

## Capacidades Relacionadas

- `actividadplazas.plazas_balance_que.gestionar`

## Endpoints Relacionados

- `/src/actividadplazas/plazas_balance_que_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
