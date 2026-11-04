---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Copiar una tessera a otra persona"
flujo: "notas.tessera_copiar_select.gestionar.flujo"
preguntas: ["Como copiar una tessera a otra persona en Copiar una tessera a otra persona?"]
pantallas_principales: ["notas.pantalla.tessera_copiar_select"]
fragmentos: ["notas.pantalla.tessera_copiar_select"]
endpoints: ["/src/notas/tessera_copiar_select_data"]
source: "docs/catalogo/notas/flujos/tessera_copiar_select.md"
estado_revision: "generado"
---

# Ayuda IA - Copiar una tessera a otra persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Copiar una tessera a otra persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como copiar una tessera a otra persona en Copiar una tessera a otra persona?

## Donde Entrar

- Tessera Copiar Select (`notas.pantalla.tessera_copiar_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Copiar una tessera a otra persona

1. Abrir la tessera de la persona.
2. En **copiar la tesera de … a**, elegir la persona destino: se listan las personas con el mismo primer apellido entre numerarios y agregados.
3. Pulsar **copiar**.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/tessera_copiar_select_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.tessera_copiar_select`
- `notas.pantalla.tessera_copiar_select`

## Objetivo

Copiar la tessera de una persona a otra con el mismo primer apellido (por ejemplo de numerario a agregado).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
