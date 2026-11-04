---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Buscar antes de listar"
flujo: "shared.tablaDB_buscar.gestionar.flujo"
preguntas: ["Como buscar antes de listar en Buscar antes de listar?"]
pantallas_principales: ["shared.pantalla.tablaDB_lista_ver"]
fragmentos: []
endpoints: ["/src/shared/tablaDB_buscar_datos"]
source: "docs/catalogo/shared/flujos/tablaDB_buscar.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar antes de listar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar antes de listar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar antes de listar en Buscar antes de listar?

## Donde Entrar

- Mantenimiento genérico de tablas (listado) (`shared.pantalla.tablaDB_lista_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar antes de listar

1. Abrir el mantenimiento desde el menú: aparece primero el formulario de criterios.
2. Rellenar los criterios que interesen.
3. Pulsar buscar: se muestra la tabla con los registros que cumplen.

Referencias tecnicas para verificar la respuesta:
- `/src/shared/tablaDB_buscar_datos`

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_lista_ver`

## Objetivo

Filtrar los registros antes de ver la tabla, en los mantenimientos que piden criterios de búsqueda.

## Errores Documentados

- `Ninguno documentado en el builder.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
