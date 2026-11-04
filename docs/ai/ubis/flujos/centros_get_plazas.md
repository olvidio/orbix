---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Consultar o cambiar plazas y sede"
flujo: "ubis.centros_get_plazas.gestionar.flujo"
preguntas: ["Como consultar o cambiar plazas y sede en Consultar o cambiar plazas y sede?"]
pantallas_principales: ["ubis.pantalla.centros_get_plazas"]
fragmentos: ["ubis.pantalla.centros_get_plazas"]
endpoints: ["/src/ubis/centros_get_plazas"]
source: "docs/catalogo/ubis/flujos/centros_get_plazas.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar o cambiar plazas y sede

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar o cambiar plazas y sede`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar o cambiar plazas y sede en Consultar o cambiar plazas y sede?

## Donde Entrar

- Centros Get Plazas (`ubis.pantalla.centros_get_plazas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar o cambiar plazas y sede

1. Entrar en **modificar centros** y elegir **sede, plazas**; pulsar **buscar**.
2. Se muestra la lista con plazas, habitaciones individuales y la marca de sede.
3. Pulsar un centro para editarlo.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/centros_get_plazas`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.centros_get_plazas`
- `ubis.pantalla.centros_get_plazas`

## Objetivo

Listar los centros de la dl con plazas, habitaciones individuales y si son sede, para editarlos.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
