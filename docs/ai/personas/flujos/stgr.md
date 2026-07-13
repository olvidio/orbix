---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Guardar nivel STGR"
flujo: "personas.stgr.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["personas.pantalla.stgr_cambio"]
endpoints: ["/src/personas/stgr_update"]
source: "docs/catalogo/personas/flujos/stgr.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar nivel STGR

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar nivel STGR`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.stgr_cambio`

## Objetivo

Actualizar el nivel STGR de una persona del listado.

## Errores Documentados

- `No existe la clase de la persona`
- `No se encuentra la persona`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
