---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Guardar o eliminar un tipo de dossier"
flujo: "dossiers.tipo_dossier.gestionar.flujo"
preguntas: ["Como guardar o eliminar un tipo de dossier en Guardar o eliminar un tipo de dossier?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/dossiers/tipo_dossier_eliminar", "/src/dossiers/tipo_dossier_guardar"]
source: "docs/catalogo/dossiers/flujos/tipo_dossier.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar o eliminar un tipo de dossier

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar o eliminar un tipo de dossier`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar o eliminar un tipo de dossier en Guardar o eliminar un tipo de dossier?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar o eliminar un tipo de dossier

1. Abrir el tipo de dossier desde **perm_dossiers**.
2. Pulsar **guardar cambios** para aplicar, o **eliminar** y confirmar para quitarlo.
3. Solo los administradores (admin_sv/admin_sf) pueden hacerlo.

Referencias tecnicas para verificar la respuesta:
- `/src/dossiers/tipo_dossier_eliminar`
- `/src/dossiers/tipo_dossier_guardar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Guardar o eliminar un tipo de dossier (solo administradores).

## Errores Documentados

- `falta id_tipo_dossier`
- `No se encuentra el dossier: <id>`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
