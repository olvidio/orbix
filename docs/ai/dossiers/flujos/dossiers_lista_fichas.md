---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Dossiers Lista Fichas"
flujo: "dossiers.dossiers_lista_fichas.gestionar.flujo"
preguntas: ["Como obtener datos en Dossiers Lista Fichas?"]
pantallas_principales: []
fragmentos: ["dossiers.pantalla.lista_dossiers"]
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
source: "docs/catalogo/dossiers/flujos/dossiers_lista_fichas.md"
estado_revision: "generado"
---

# Ayuda IA - Dossiers Lista Fichas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Dossiers Lista Fichas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Dossiers Lista Fichas?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.lista_dossiers`

## Objetivo

Gestiona DossiersListaFichas. Filas de la tabla de relación de dossiers (modo lista en dossiers_ver). href_ver / href_abrir se firman en el borde HTTP (ver dossiers_lista_fichas_data.php).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
