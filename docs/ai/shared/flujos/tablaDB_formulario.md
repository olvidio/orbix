---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Formulario tabla genérica"
flujo: "shared.tablaDB_formulario.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
endpoints: ["/src/shared/tablaDB_formulario_datos", "/src/shared/tablaDB_update"]
source: "docs/catalogo/shared/flujos/tablaDB_formulario.md"
estado_revision: "generado"
---

# Ayuda IA - Formulario tabla genérica

Usa este documento para responder preguntas de usuario sobre como trabajar con `Formulario tabla genérica`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_formulario_ver`

## Objetivo

Crear o modificar un registro en el mantenimiento genérico de tablas.

## Errores Documentados

- `Mensajes de tablaDB_update en alert vía json.mensaje.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
