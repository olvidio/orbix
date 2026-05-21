---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadplazas"
titulo: "Plazas Balance Dl"
pantalla: "actividadplazas.pantalla.plazas_balance_dl"
preguntas: ["Que se puede hacer en Plazas Balance Dl?", "Que campos tiene Plazas Balance Dl?", "Que acciones hay en Plazas Balance Dl?"]
capacidades: ["actividadplazas.gestion_plazas.gestionar", "actividadplazas.plazas_balance.gestionar"]
endpoints: ["/src/actividadplazas/gestion_plazas_update", "/src/actividadplazas/plazas_balance_data"]
source: "docs/catalogo/actividadplazas/pantallas/plazas_balance_dl.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Plazas Balance Dl

## Resumen

Devuelve el HTML del grid comparativo A vs B para insertarlo en `#comparativa` de `plazas_balance_que.phtml` (AJAX HTML).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.colName`
- `form.data`
- `post.dl`
- `post.id_tipo_activ`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividadplazas.gestion_plazas.gestionar`
- `actividadplazas.plazas_balance.gestionar`

## Endpoints Relacionados

- `/src/actividadplazas/gestion_plazas_update`
- `/src/actividadplazas/plazas_balance_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
