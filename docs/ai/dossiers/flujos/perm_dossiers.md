---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Perm Dossiers"
flujo: "dossiers.perm_dossiers.gestionar.flujo"
preguntas: ["Como obtener datos en Perm Dossiers?"]
pantallas_principales: []
fragmentos: ["dossiers.pantalla.perm_dossiers"]
endpoints: ["/src/dossiers/perm_dossiers_data"]
source: "docs/catalogo/dossiers/flujos/perm_dossiers.md"
estado_revision: "generado"
---

# Ayuda IA - Perm Dossiers

Usa este documento para responder preguntas de usuario sobre como trabajar con `Perm Dossiers`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Perm Dossiers?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.perm_dossiers`

## Objetivo

Gestiona PermDossiers. Listado de tipos de dossier para pantalla de permisos. pagina_link_spec se firma en perm_dossiers_data.php.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
