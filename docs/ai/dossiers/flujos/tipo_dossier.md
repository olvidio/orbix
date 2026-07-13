---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Tipo Dossier"
flujo: "dossiers.tipo_dossier.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: []
endpoints: ["/src/dossiers/tipo_dossier_eliminar", "/src/dossiers/tipo_dossier_guardar"]
source: "docs/catalogo/dossiers/flujos/tipo_dossier.md"
estado_revision: "generado"
---

# Ayuda IA - Tipo Dossier

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tipo Dossier`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Persistir cambios (`tipo_dossier_guardar`) o eliminar (`tipo_dossier_eliminar`) un tipo de dossier desde el formulario `perm_dossier_ver` (solo administradores `admin_sv`/`admin_sf`).

## Errores Documentados

- `falta id_tipo_dossier`
- `No se encuentra el dossier: <id>`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
