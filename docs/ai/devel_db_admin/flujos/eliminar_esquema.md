---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "devel_db_admin"
titulo: "Eliminar Esquema"
flujo: "devel_db_admin.eliminar_esquema.gestionar.flujo"
preguntas: ["Como ejecutar en Eliminar Esquema?"]
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_eliminar"]
endpoints: ["/src/devel_db_admin/eliminar_esquema"]
source: "docs/catalogo/devel_db_admin/flujos/eliminar_esquema.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar Esquema

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar Esquema`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Eliminar Esquema?

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

- `devel_db_admin.pantalla.db_eliminar`

## Objetivo

Eliminar esquema DL y trasladar datos a resto.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
