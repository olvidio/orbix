---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Trasladar centros o casas"
flujo: "ubis.trasladar_ubis.gestionar.flujo"
preguntas: ["Como trasladar centros o casas en Trasladar centros o casas?"]
pantallas_principales: ["ubis.pantalla.trasladar_ubis"]
fragmentos: ["ubis.pantalla.trasladar_ubis"]
endpoints: ["/src/ubis/trasladar_ubis"]
source: "docs/catalogo/ubis/flujos/trasladar_ubis.md"
estado_revision: "generado"
---

# Ayuda IA - Trasladar centros o casas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Trasladar centros o casas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como trasladar centros o casas en Trasladar centros o casas?

## Donde Entrar

- Trasladar Ubis (`ubis.pantalla.trasladar_ubis`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Trasladar centros o casas

1. En **listados**, marcar uno o varios centros o casas.
2. Elegir la delegación en **Trasladar a** y pulsar **Trasladar**.
3. Comprobar el listado: los trasladados ya no pertenecen a la delegación de origen.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/trasladar_ubis`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.trasladar_ubis`
- `ubis.pantalla.trasladar_ubis`

## Objetivo

Pasar los centros o casas seleccionados a otra delegación.

## Errores Documentados

- `No se han seleccionado ubis.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
