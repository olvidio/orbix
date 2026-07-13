---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Abrir ficha de persona"
flujo: "personas.personas_editar.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["personas.pantalla.personas_editar"]
endpoints: ["/src/personas/personas_editar_data"]
source: "docs/catalogo/personas/flujos/personas_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir ficha de persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir ficha de persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.personas_editar`

## Objetivo

Crear una persona nueva o editar la ficha existente con los campos del colectivo correspondiente.

## Errores Documentados

- `No se ha pasado el id_nom`
- `No se encuentra la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
