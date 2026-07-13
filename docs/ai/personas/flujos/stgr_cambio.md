---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Cambiar nivel STGR (formulario)"
flujo: "personas.stgr_cambio.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["personas.pantalla.stgr_cambio"]
endpoints: ["/src/personas/stgr_cambio_data"]
source: "docs/catalogo/personas/flujos/stgr_cambio.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar nivel STGR (formulario)

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar nivel STGR (formulario)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.stgr_cambio`

## Objetivo

Ver el nivel actual y las opciones disponibles antes de guardar el cambio.

## Errores Documentados

- `No existe la clase de la persona`
- `No se encuentra la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
