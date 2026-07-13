---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "devel_db_admin"
titulo: "Db Lugar"
flujo: "devel_db_admin.db_lugar.gestionar.flujo"
preguntas: ["Como ejecutar en Db Lugar?"]
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_cambiar_nombre_que", "devel_db_admin.pantalla.db_crear_esquema_que", "devel_db_admin.pantalla.db_eliminar_esquema_que"]
endpoints: ["/src/devel_db_admin/db_lugar"]
source: "docs/catalogo/devel_db_admin/flujos/db_lugar.md"
estado_revision: "generado"
---

# Ayuda IA - Db Lugar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Db Lugar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Db Lugar?

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

- `devel_db_admin.pantalla.db_cambiar_nombre_que`
- `devel_db_admin.pantalla.db_crear_esquema_que`
- `devel_db_admin.pantalla.db_eliminar_esquema_que`

## Objetivo

Recargar desplegable de delegación al cambiar región en formularios DB.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
