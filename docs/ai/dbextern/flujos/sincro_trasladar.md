---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Traer una ficha a esta dl"
flujo: "dbextern.sincro_trasladar.gestionar.flujo"
preguntas: ["Como traer una ficha a esta dl en Traer una ficha a esta dl?"]
pantallas_principales: ["dbextern.pantalla.ver_traslados"]
fragmentos: ["dbextern.pantalla.ver_traslados"]
endpoints: ["/src/dbextern/sincro_trasladar"]
source: "docs/catalogo/dbextern/flujos/sincro_trasladar.md"
estado_revision: "generado"
---

# Ayuda IA - Traer una ficha a esta dl

Usa este documento para responder preguntas de usuario sobre como trabajar con `Traer una ficha a esta dl`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como traer una ficha a esta dl en Traer una ficha a esta dl?

## Donde Entrar

- Traslados desde otra DL (`dbextern.pantalla.ver_traslados`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Traer una ficha a esta dl

1. En el punto 2, pulsar **trasladar** en la persona.
2. Aparece un aviso sobre la fecha del traslado (hoy); aceptarlo.
3. Si se ha hecho bien, la fila aparece tachada.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_trasladar`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_traslados`
- `dbextern.pantalla.ver_traslados`

## Objetivo

Traer una ficha a la delegación actual. La fecha de traslado queda en el día de hoy.

## Errores Documentados

- `Mensajes del dominio Trasladar / Error al trasladar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
