---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Calendario Ubi Resumen"
pantalla: "casas.pantalla.calendario_ubi_resumen"
preguntas: ["Que se puede hacer en Calendario Ubi Resumen?", "Que campos tiene Calendario Ubi Resumen?", "Que acciones hay en Calendario Ubi Resumen?"]
capacidades: ["casas.calendario_ubi_resumen.gestionar"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_update_inc", "/src/casas/calendario_ubi_resumen_data", "/src/ubis/casas_opciones_data"]
source: "docs/catalogo/casas/pantallas/calendario_ubi_resumen.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Calendario Ubi Resumen

## Resumen

Pantalla `calendario_ubi_resumen`: estudio económico y de ocupación de una casa.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.G`
- `form.id_ubi`
- `form.inc_cantidad`
- `form.inc_t`
- `form.seccion`
- `form.year`
- `html.G`
- `html.inc_t`
- `html.seccion`
- `post.G`
- `post.id_ubi`
- `post.inc_t`

## Acciones Detectadas

- `button:resumen sf`
- `button:resumen sv`
- `fnjs_guardar`
- `fnjs_ver`

## Capacidades Relacionadas

- `casas.calendario_ubi_resumen.gestionar`

## Endpoints Relacionados

- `/src/actividadtarifas/tarifa_ubi_update_inc`
- `/src/casas/calendario_ubi_resumen_data`
- `/src/ubis/casas_opciones_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
