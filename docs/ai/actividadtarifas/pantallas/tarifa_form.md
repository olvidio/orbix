---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadtarifas"
titulo: "Tarifa Form"
pantalla: "actividadtarifas.pantalla.tarifa_form"
preguntas: ["Que se puede hacer en Tarifa Form?", "Que campos tiene Tarifa Form?", "Que acciones hay en Tarifa Form?"]
capacidades: ["actividadtarifas.tipo_tarifa.gestionar"]
endpoints: ["/src/actividadtarifas/tipo_tarifa_form_data", "/src/actividadtarifas/tipo_tarifa_update"]
source: "docs/catalogo/actividadtarifas/pantallas/tarifa_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tarifa Form

## Resumen

Fragmento AJAX: formulario popup modificar/nuevo de `TipoTarifa` (`tarifa_form.phtml`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.id_tarifa`
- `html.letra`
- `html.modo`
- `html.observ`
- `post.id_tarifa`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Capacidades Relacionadas

- `actividadtarifas.tipo_tarifa.gestionar`

## Endpoints Relacionados

- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
