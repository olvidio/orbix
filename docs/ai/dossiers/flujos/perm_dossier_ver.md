---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Perm Dossier Ver"
flujo: "dossiers.perm_dossier_ver.gestionar.flujo"
preguntas: ["Como obtener datos en Perm Dossier Ver?"]
pantallas_principales: []
fragmentos: ["dossiers.pantalla.perm_dossier_ver"]
endpoints: ["/src/dossiers/perm_dossier_ver_data"]
source: "docs/catalogo/dossiers/flujos/perm_dossier_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Perm Dossier Ver

Usa este documento para responder preguntas de usuario sobre como trabajar con `Perm Dossier Ver`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Perm Dossier Ver?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.perm_dossier_ver`

## Objetivo

Gestiona PermDossierVer. Formulario "permisos de acceso" para un tipo de dossier. El backend devuelve sólo datos: - go_to_link_spec ({path, query}) para que el frontend firme con HashFront. - hash_config (campos_form, campos_no, campos_hidden) para que el frontend componga el bloque hidden con HashFront; el valor de go_to dentro de campos_hidden se inyecta firmado en el borde del frontend. - permiso_dossier_bit_map + enteros permiso_lectura / permiso_escritura; el HTML de checkboxes lo genera el controlador frontend con {.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
