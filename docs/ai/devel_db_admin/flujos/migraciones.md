---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "devel_db_admin"
titulo: "Aplicar las migraciones pendientes"
flujo: "devel_db_admin.migraciones.gestionar.flujo"
preguntas: ["Como aplicar las migraciones pendientes en Aplicar las migraciones pendientes?"]
pantallas_principales: ["devel_db_admin.pantalla.migraciones_lista"]
fragmentos: ["devel_db_admin.pantalla.migraciones_lista"]
endpoints: ["/src/devel_db_admin/migraciones_lista_data"]
source: "docs/catalogo/devel_db_admin/flujos/migraciones.md"
estado_revision: "generado"
---

# Ayuda IA - Aplicar las migraciones pendientes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Aplicar las migraciones pendientes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como aplicar las migraciones pendientes en Aplicar las migraciones pendientes?

## Donde Entrar

- Migraciones Lista (`devel_db_admin.pantalla.migraciones_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Aplicar las migraciones pendientes

1. Entrar en **actualizar DB**: aparece la lista de migraciones de base de datos.
2. Revisar las pendientes (las de estructura se ejecutan primero en la base principal y luego en la réplica de lectura).
3. Pulsar para aplicarlas y esperar a «ejecutando...»; al terminar se reactivan las suscripciones.
4. Para anotar una como aplicada sin ejecutarla, usar quitar registro.

Referencias tecnicas para verificar la respuesta:
- `/src/devel_db_admin/migraciones_lista_data`

## Pantallas Y Fragmentos Relacionados

- `devel_db_admin.pantalla.migraciones_lista`
- `devel_db_admin.pantalla.migraciones_lista`

## Objetivo

Revisar y aplicar las migraciones SQL pendientes de la base de datos (herramienta de desarrollo).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
