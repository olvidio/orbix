---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Guardar el nivel stgr"
flujo: "personas.stgr.gestionar.flujo"
preguntas: ["Como guardar el nivel stgr en Guardar el nivel stgr?"]
pantallas_principales: ["personas.pantalla.stgr_cambio"]
fragmentos: ["personas.pantalla.stgr_cambio"]
endpoints: ["/src/personas/stgr_update"]
source: "docs/catalogo/personas/flujos/stgr.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar el nivel stgr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar el nivel stgr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar el nivel stgr en Guardar el nivel stgr?

## Donde Entrar

- Cambio nivel STGR (`personas.pantalla.stgr_cambio`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar el nivel stgr

1. Elegir el nivel en el desplegable y pulsar **Guardar**.
2. No sale mensaje: se vuelve a la lista de personas. El nuevo nivel se ve en la ficha de la persona (campo «stgr»).

Referencias tecnicas para verificar la respuesta:
- `/src/personas/stgr_update`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.stgr_cambio`
- `personas.pantalla.stgr_cambio`

## Objetivo

Guardar el nivel de estudios (stgr) elegido para una persona.

## Errores Documentados

- `No existe la clase de la persona`
- `No se encuentra la persona`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
