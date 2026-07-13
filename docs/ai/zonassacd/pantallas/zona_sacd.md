---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "zonassacd"
titulo: "Zona Sacd"
pantalla: "zonassacd.pantalla.zona_sacd"
preguntas: ["Que se puede hacer en Zona Sacd?", "Que campos tiene Zona Sacd?", "Que acciones hay en Zona Sacd?"]
capacidades: ["zonassacd.zona_sacd.gestionar"]
endpoints: ["/src/misas/zona_sacd_datos_get", "/src/misas/zona_sacd_datos_put", "/src/zonassacd/zona_sacd"]
source: "docs/catalogo/zonassacd/pantallas/zona_sacd.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Zona Sacd

## Resumen

Pantalla Zonas-sacd: listar sacd por zona, reasignar zonas (propia/iglesia) y modal de días L–D (vía misas/zona_sacd_datos_*). Requiere perm_des para mutaciones.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dw1`
- `form.dw2`
- `form.dw3`
- `form.dw4`
- `form.dw5`
- `form.dw6`
- `form.dw7`
- `form.id_sacd`
- `form.id_zona`
- `form.id_zona_new`
- `html.dw1`
- `html.dw2`
- `html.dw3`
- `html.dw4`
- `html.dw5`
- `html.dw6`
- `html.dw7`
- `html.id_zona`
- `html.id_zona_new`
- `html.ok`
- `html.ok2`

## Acciones Detectadas

- `fnjs_busca_sacds`
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_modal_zona_sacd_ver`
- `fnjs_modificar`
- `fnjs_solo_uno`

## Capacidades Relacionadas

- `zonassacd.zona_sacd.gestionar`

## Endpoints Relacionados

- `/src/misas/zona_sacd_datos_get`
- `/src/misas/zona_sacd_datos_put`
- `/src/zonassacd/zona_sacd`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
