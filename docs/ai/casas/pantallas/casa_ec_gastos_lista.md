---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Casa Ec Gastos Lista"
pantalla: "casas.pantalla.casa_ec_gastos_lista"
preguntas: ["Que se puede hacer en Casa Ec Gastos Lista?", "Que campos tiene Casa Ec Gastos Lista?", "Que acciones hay en Casa Ec Gastos Lista?"]
capacidades: ["casas.casa_ec_gastos.gestionar"]
endpoints: ["/src/casas/casa_ec_gastos_form_data", "/src/casas/casa_ec_gastos_guardar"]
source: "docs/catalogo/casas/pantallas/casa_ec_gastos_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Casa Ec Gastos Lista

## Resumen

Controlador AJAX HTML: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.id_ubi`
- `html.year`
- `post.id_cdc`
- `post.year`

## Acciones Detectadas

- `fnjs_comprobar_dinero`
- `fnjs_gastos_guardar`
- `fnjs_ver`

## Capacidades Relacionadas

- `casas.casa_ec_gastos.gestionar`

## Endpoints Relacionados

- `/src/casas/casa_ec_gastos_form_data`
- `/src/casas/casa_ec_gastos_guardar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
