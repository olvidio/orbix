---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Elegir una asignatura"
flujo: "notas.asignaturas_search.gestionar.flujo"
preguntas: ["Como elegir una asignatura en Elegir una asignatura?"]
pantallas_principales: ["notas.pantalla.acta_ver"]
fragmentos: ["notas.pantalla.acta_ver"]
endpoints: ["/src/notas/asignaturas_search"]
source: "docs/catalogo/notas/flujos/asignaturas_search.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir una asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir una asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir una asignatura en Elegir una asignatura?

## Donde Entrar

- Acta Ver (`notas.pantalla.acta_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir una asignatura

1. En la ficha del acta, escribir parte del nombre de la asignatura.
2. Elegir de la lista que aparece.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/asignaturas_search`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_ver`
- `notas.pantalla.acta_ver`

## Objetivo

Autocompletar el nombre de la asignatura al rellenar un acta.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
