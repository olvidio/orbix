---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "devel_db_admin"
titulo: "Corregir Renombrar Esquema"
flujo: "devel_db_admin.corregir_renombrar_esquema.gestionar.flujo"
preguntas: ["Como ejecutar en Corregir Renombrar Esquema?"]
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_corregir_renombrar_esquema"]
endpoints: ["/src/devel_db_admin/corregir_renombrar_esquema"]
source: "docs/catalogo/devel_db_admin/flujos/corregir_renombrar_esquema.md"
estado_revision: "generado"
---

# Ayuda IA - Corregir Renombrar Esquema

Usa este documento para responder preguntas de usuario sobre como trabajar con `Corregir Renombrar Esquema`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Corregir Renombrar Esquema?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `devel_db_admin.pantalla.db_corregir_renombrar_esquema`

## Objetivo

Gestiona CorregirEstadoRenombrarEsquema, RenombrarEsquemaVerificacionContexto. POST: esquema_origen opcional (vacío = solo defaults sobre destino); región y dl obligatorios; acepta POST esquema legado como origen.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
