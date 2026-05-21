---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Grupo"
pantalla: "casas.pantalla.grupo"
preguntas: ["Que se puede hacer en Grupo?", "Que campos tiene Grupo?", "Que acciones hay en Grupo?"]
capacidades: ["casas.grupo.gestionar"]
endpoints: ["/src/casas/grupo_eliminar", "/src/casas/grupo_update"]
source: "docs/catalogo/casas/pantallas/grupo.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Grupo

## Resumen

Pantalla principal del módulo `casas` - grupos de casas (padre ↔ hijo).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_item`
- `form.id_ubi_hijo`
- `form.id_ubi_padre`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_eliminar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Capacidades Relacionadas

- `casas.grupo.gestionar`

## Endpoints Relacionados

- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
