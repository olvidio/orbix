---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Consultar los sacerdotes por centro"
flujo: "ubis.lista_ctrs.gestionar.flujo"
preguntas: ["Como consultar los sacerdotes por centro en Consultar los sacerdotes por centro?"]
pantallas_principales: ["ubis.pantalla.lista_ctrs"]
fragmentos: ["ubis.pantalla.lista_ctrs"]
endpoints: ["/src/ubis/lista_ctrs_data"]
source: "docs/catalogo/ubis/flujos/lista_ctrs.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los sacerdotes por centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los sacerdotes por centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los sacerdotes por centro en Consultar los sacerdotes por centro?

## Donde Entrar

- Lista Ctrs (`ubis.pantalla.lista_ctrs`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los sacerdotes por centro

1. Entrar en **lista ctr i nº s**.
2. Se muestra la tabla con cada centro y el número de sacerdotes que tiene.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/lista_ctrs_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.lista_ctrs`
- `ubis.pantalla.lista_ctrs`

## Objetivo

Ver los centros de tipo s de la delegación con el número de sacerdotes asignados a cada uno.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
