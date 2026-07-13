---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "zonassacd"
titulo: "Zona Ctr"
pantalla: "zonassacd.pantalla.zona_ctr"
preguntas: ["Que se puede hacer en Zona Ctr?", "Que campos tiene Zona Ctr?", "Que acciones hay en Zona Ctr?"]
capacidades: ["zonassacd.zona_ctr.gestionar"]
endpoints: ["/src/zonassacd/zona_ctr"]
source: "docs/catalogo/zonassacd/pantallas/zona_ctr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Zona Ctr

## Resumen

Pantalla Zonas-ctr: listar centros por zona y reasignarlos. Opción sin zona sf solo con perm_des.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_zona`
- `form.id_zona_new`
- `html.id_zona_new`
- `html.ok`

## Acciones Detectadas

- `fnjs_busca_ctrs`
- `fnjs_guardar`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `zonassacd.zona_ctr.gestionar`

## Endpoints Relacionados

- `/src/zonassacd/zona_ctr`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
