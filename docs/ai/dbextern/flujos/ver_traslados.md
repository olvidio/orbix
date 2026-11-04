---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Revisar los traslados del punto 2"
flujo: "dbextern.ver_traslados.gestionar.flujo"
preguntas: ["Como revisar los traslados del punto 2 en Revisar los traslados del punto 2?"]
pantallas_principales: ["dbextern.pantalla.ver_traslados"]
fragmentos: ["dbextern.pantalla.ver_traslados"]
endpoints: ["/src/dbextern/ver_traslados_datos"]
source: "docs/catalogo/dbextern/flujos/ver_traslados.md"
estado_revision: "generado"
---

# Ayuda IA - Revisar los traslados del punto 2

Usa este documento para responder preguntas de usuario sobre como trabajar con `Revisar los traslados del punto 2`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como revisar los traslados del punto 2 en Revisar los traslados del punto 2?

## Donde Entrar

- Traslados desde otra DL (`dbextern.pantalla.ver_traslados`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Revisar los traslados del punto 2

1. Pulsar **ver** en el punto 2.
2. Aparece la tabla con el nombre, la delegación actual y la acción **trasladar**.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/ver_traslados_datos`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_traslados`
- `dbextern.pantalla.ver_traslados`

## Objetivo

Identificar a quién hay que trasladar a esta delegación desde otra.

## Errores Documentados

- `No existe la clase de la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
