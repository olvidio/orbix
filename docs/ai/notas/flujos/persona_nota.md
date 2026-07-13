---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Persona Nota"
flujo: "notas.persona_nota.gestionar.flujo"
preguntas: ["Como crear en Persona Nota?", "Como eliminar en Persona Nota?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
endpoints: ["/src/notas/persona_nota_eliminar", "/src/notas/persona_nota_nueva"]
source: "docs/catalogo/notas/flujos/persona_nota.md"
estado_revision: "generado"
---

# Ayuda IA - Persona Nota

Usa este documento para responder preguntas de usuario sobre como trabajar con `Persona Nota`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Persona Nota?
- Como eliminar en Persona Nota?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/persona_nota_eliminar`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.form_notas_de_una_persona`

## Objetivo

Alta de nota en dossier 1011 (`persona_nota_nueva`).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
