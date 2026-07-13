---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Sincronizar fichas unidas"
flujo: "dbextern.sincro_syncro.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["dbextern.pantalla.sincro_index"]
endpoints: ["/src/dbextern/sincro_syncro"]
source: "docs/catalogo/dbextern/flujos/sincro_syncro.md"
estado_revision: "generado"
---

# Ayuda IA - Sincronizar fichas unidas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sincronizar fichas unidas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.sincro_index`

## Objetivo

Actualizar en Aquinate los datos de todas las personas ya vinculadas a la BDU en la DL actual.

## Errores Documentados

- `Mensajes de syncro por persona (dentro de mensaje, no siempre success: false)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
