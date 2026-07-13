---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Dashboard sincronización BDU"
flujo: "dbextern.sincro_index.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["dbextern.pantalla.sincro_index"]
endpoints: ["/src/dbextern/sincro_index_datos"]
source: "docs/catalogo/dbextern/flujos/sincro_index.md"
estado_revision: "generado"
---

# Ayuda IA - Dashboard sincronización BDU

Usa este documento para responder preguntas de usuario sobre como trabajar con `Dashboard sincronización BDU`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.sincro_index`

## Objetivo

Al abrir la pantalla de sincronización, el sistema calcula los contadores de situación BDU↔Aquinate y prepara enlaces firmados a las subpantallas de resolución.

## Errores Documentados

- `No se encontró la delegación en listas`
- `no tiene permisos`
- `No existe la clase de la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
