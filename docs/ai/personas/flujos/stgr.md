---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Stgr"
flujo: "personas.stgr.gestionar.flujo"
preguntas: ["Como crear o modificar en Stgr?"]
pantallas_principales: []
fragmentos: ["personas.pantalla.stgr_cambio"]
endpoints: ["/src/personas/stgr_update"]
source: "docs/catalogo/personas/flujos/stgr.md"
estado_revision: "generado"
---

# Ayuda IA - Stgr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Stgr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Stgr?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/stgr_update`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.stgr_cambio`

## Objetivo

Gestiona Stgr. Endpoint JSON: actualiza el nivel_stgr de una persona.

## Errores Documentados

- `No existe la clase de la persona`
- `No se encuentra la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
