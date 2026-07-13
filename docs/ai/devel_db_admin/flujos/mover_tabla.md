---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "devel_db_admin"
titulo: "Mover Tabla"
flujo: "devel_db_admin.mover_tabla.gestionar.flujo"
preguntas: ["Como ejecutar en Mover Tabla?"]
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_mover"]
endpoints: ["/src/devel_db_admin/mover_tabla"]
source: "docs/catalogo/devel_db_admin/flujos/mover_tabla.md"
estado_revision: "generado"
---

# Ayuda IA - Mover Tabla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Mover Tabla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Mover Tabla?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `devel_db_admin.pantalla.db_mover`

## Objetivo

Mover tabla de sv a sv-e en todos los esquemas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
