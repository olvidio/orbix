---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadtarifas"
titulo: "Tarifa Ubi Form"
pantalla: "actividadtarifas.pantalla.tarifa_ubi_form"
preguntas: ["Que se puede hacer en Tarifa Ubi Form?", "Que campos tiene Tarifa Ubi Form?", "Que acciones hay en Tarifa Ubi Form?"]
capacidades: ["actividadtarifas.tarifa_ubi.gestionar"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_form_data", "/src/actividadtarifas/tarifa_ubi_update"]
source: "docs/catalogo/actividadtarifas/pantallas/tarifa_ubi_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tarifa Ubi Form

## Resumen

Controlador AJAX HTML: form modificar/nuevo de `TarifaUbi`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.cantidad`
- `html.ctx_eliminar`
- `html.ctx_update`
- `html.id_item`
- `html.id_ubi`
- `html.year`
- `post.id_item`
- `post.id_ubi`
- `post.letra`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_comprobar_dinero`
- `fnjs_guardar`

## Capacidades Relacionadas

- `actividadtarifas.tarifa_ubi.gestionar`

## Endpoints Relacionados

- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
