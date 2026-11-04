---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Consultar la tessera de una persona"
flujo: "notas.tessera_ver.gestionar.flujo"
preguntas: ["Como consultar la tessera de una persona en Consultar la tessera de una persona?"]
pantallas_principales: ["notas.pantalla.tessera_ver"]
fragmentos: ["notas.pantalla.tessera_ver"]
endpoints: ["/src/notas/tessera_ver_data"]
source: "docs/catalogo/notas/flujos/tessera_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar la tessera de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar la tessera de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la tessera de una persona en Consultar la tessera de una persona?

## Donde Entrar

- Tessera Ver (`notas.pantalla.tessera_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la tessera de una persona

1. Abrir la tessera de la persona.
2. Aparecen el número de asignaturas y créditos realizados en el curso y en total, separados por filosofía y teología, y lo que queda pendiente.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/tessera_ver_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.tessera_ver`
- `notas.pantalla.tessera_ver`

## Objetivo

Ver la tessera de estudios de una persona: asignaturas hechas y créditos, de filosofía y teología.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
