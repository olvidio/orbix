---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Búsqueda previa al listado"
flujo: "shared.tablaDB_buscar.gestionar.flujo"
preguntas: []
pantallas_principales: ["shared.pantalla.tablaDB_lista_ver"]
fragmentos: []
endpoints: ["/src/shared/tablaDB_buscar_datos"]
source: "docs/catalogo/shared/flujos/tablaDB_buscar.md"
estado_revision: "generado"
---

# Ayuda IA - Búsqueda previa al listado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Búsqueda previa al listado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Mantenimiento genérico de tablas (listado) (`shared.pantalla.tablaDB_lista_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_lista_ver`

## Objetivo

Filtrar registros antes de mostrar la tabla en mantenimientos que definen criterios de búsqueda.

## Errores Documentados

- `Ninguno documentado en el builder.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
