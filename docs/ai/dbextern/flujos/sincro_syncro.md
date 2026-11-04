---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Sincronizar las fichas unidas"
flujo: "dbextern.sincro_syncro.gestionar.flujo"
preguntas: ["Como sincronizar las fichas unidas en Sincronizar las fichas unidas?"]
pantallas_principales: ["dbextern.pantalla.sincro_index"]
fragmentos: ["dbextern.pantalla.sincro_index"]
endpoints: ["/src/dbextern/sincro_syncro"]
source: "docs/catalogo/dbextern/flujos/sincro_syncro.md"
estado_revision: "generado"
---

# Ayuda IA - Sincronizar las fichas unidas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sincronizar las fichas unidas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como sincronizar las fichas unidas en Sincronizar las fichas unidas?

## Donde Entrar

- Sincronizar con los datos de Listas (`dbextern.pantalla.sincro_index`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Sincronizar las fichas unidas

1. En la pantalla de sincronización, pulsar **ejecutar** en el punto 1.
2. Al terminar sale un aviso con el resumen; los fallos de algunas personas se indican en el mismo aviso.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_syncro`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.sincro_index`
- `dbextern.pantalla.sincro_index`

## Objetivo

Actualizar en Aquinate los datos de todas las personas ya vinculadas a la BDU en la delegación.

## Errores Documentados

- `Mensajes de syncro por persona (dentro de mensaje, no siempre success: false)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
