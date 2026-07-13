---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "devel_db_admin"
titulo: "Db Propiedades"
flujo: "devel_db_admin.db_propiedades.gestionar.flujo"
preguntas: ["Como obtener datos en Db Propiedades?"]
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.apptables", "devel_db_admin.pantalla.db_absorber_esquema_que", "devel_db_admin.pantalla.db_cambiar_nombre_que", "devel_db_admin.pantalla.db_crear_esquema_que", "devel_db_admin.pantalla.db_eliminar_esquema_que", "devel_db_admin.pantalla.db_mover_que"]
endpoints: ["/src/devel_db_admin/db_propiedades_data"]
source: "docs/catalogo/devel_db_admin/flujos/db_propiedades.md"
estado_revision: "generado"
---

# Ayuda IA - Db Propiedades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Db Propiedades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Db Propiedades?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `devel_db_admin.pantalla.apptables`
- `devel_db_admin.pantalla.db_absorber_esquema_que`
- `devel_db_admin.pantalla.db_cambiar_nombre_que`
- `devel_db_admin.pantalla.db_crear_esquema_que`
- `devel_db_admin.pantalla.db_eliminar_esquema_que`
- `devel_db_admin.pantalla.db_mover_que`

## Objetivo

Cargar desplegables de esquemas/tablas según operación (`op`).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
