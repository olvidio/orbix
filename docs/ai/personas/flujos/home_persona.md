---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Ver cabecera de persona"
flujo: "personas.home_persona.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["personas.pantalla.home_persona"]
endpoints: ["/src/personas/home_persona_data"]
source: "docs/catalogo/personas/flujos/home_persona.md"
estado_revision: "generado"
---

# Ayuda IA - Ver cabecera de persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver cabecera de persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.home_persona`

## Objetivo

Consultar datos básicos y acceder a la ficha completa o dossiers sin pasar por el listado.

## Errores Documentados

- `No se encuentra la persona`
- `Aviso: persona no válida`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
