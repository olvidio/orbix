---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Elegir la delegación de destino"
flujo: "ubis.delegacion_que.gestionar.flujo"
preguntas: ["Como elegir la delegación de destino en Elegir la delegación de destino?"]
pantallas_principales: ["ubis.pantalla.delegacion_que"]
fragmentos: ["ubis.pantalla.delegacion_que"]
endpoints: ["/src/ubis/delegacion_que_data"]
source: "docs/catalogo/ubis/flujos/delegacion_que.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la delegación de destino

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la delegación de destino`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la delegación de destino en Elegir la delegación de destino?

## Donde Entrar

- Delegacion Que (`ubis.pantalla.delegacion_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la delegación de destino

1. En el listado, marcar los centros o casas a trasladar.
2. Pulsar trasladar.
3. En **Trasladar a**, elegir la delegación y confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/delegacion_que_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.delegacion_que`
- `ubis.pantalla.delegacion_que`

## Objetivo

Ofrecer las delegaciones a las que se pueden trasladar los centros o casas marcados.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
