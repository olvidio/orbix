---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Perm Dossier Ver"
flujo: "dossiers.perm_dossier_ver.gestionar.flujo"
preguntas: []
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

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.perm_dossier_ver`

## Objetivo

Consultar o modificar la definición y máscaras de permiso de un `TipoDossier` concreto; volver al listado tras guardar o eliminar.

## Errores Documentados

- `No se encuentra el dossier: <id>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
