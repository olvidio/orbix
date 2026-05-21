---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Casa Ec Gastos"
flujo: "casas.casa_ec_gastos.gestionar.flujo"
preguntas: ["Como guardar en Casa Ec Gastos?", "Como abrir el formulario en Casa Ec Gastos?"]
pantallas_principales: []
fragmentos: ["casas.pantalla.casa_ec_gastos_lista"]
endpoints: ["/src/casas/casa_ec_gastos_form_data", "/src/casas/casa_ec_gastos_guardar"]
source: "docs/catalogo/casas/flujos/casa_ec_gastos.md"
estado_revision: "generado"
---

# Ayuda IA - Casa Ec Gastos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Casa Ec Gastos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Casa Ec Gastos?
- Como abrir el formulario en Casa Ec Gastos?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/casa_ec_gastos_form_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casa_ec_gastos_lista`

## Objetivo

Gestiona CasaEcGastos. Data builder: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa. Sucesor de la rama que=getGastos de apps/casas/controller/casa_ec_ajax.php. Use case: guardar los gastos y aportaciones (sv/sf) mensuales de una casa para un año completo. Borra los existentes y los reinserta con fecha 5 de cada mes. Sucesor de la rama que=guardarGasto de apps/casas/controller/casa_ec_ajax.php.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
