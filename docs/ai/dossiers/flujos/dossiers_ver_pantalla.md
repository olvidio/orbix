---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Dossiers Ver Pantalla"
flujo: "dossiers.dossiers_ver_pantalla.gestionar.flujo"
preguntas: ["Como obtener datos en Dossiers Ver Pantalla?"]
pantallas_principales: []
fragmentos: ["dossiers.pantalla.dossiers_ver"]
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
source: "docs/catalogo/dossiers/flujos/dossiers_ver_pantalla.md"
estado_revision: "generado"
---

# Ayuda IA - Dossiers Ver Pantalla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Dossiers Ver Pantalla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Dossiers Ver Pantalla?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.dossiers_ver`

## Objetivo

Gestiona DossiersVerPantalla. Cuerpo de dossiers_ver: datos de cabecera + lista o ficha. El backend NO firma URLs: devuelve *_link_spec ({path, query}) que firma el frontend. En modo ficha, ficha_segmentos mezcla: - Segmentos html ya generados por los Select_* (TODO: refactorizar para que tampoco lleven HTML/HashFront desde src/). - Segmentos datos_tabla con datos puros (action_tabla_link_spec, ins_traslado_link_spec, script_ctx, hash, tabla, permiso) que el frontend compone con HashFront, Lista y el script JS de DatosTablaRepo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
