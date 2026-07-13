---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Dossiers Lista Fichas"
flujo: "dossiers.dossiers_lista_fichas.gestionar.flujo"
preguntas: []
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

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.lista_dossiers`

## Objetivo

Mostrar la tabla de carpetas de dossiers disponibles para la entidad actual, con iconos de permiso y enlace a cada ficha (`href_ver` firmado en frontend).

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
