---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Tipo Activ Proceso"
flujo: "procesos.tipo_activ_proceso.gestionar.flujo"
preguntas: ["Como consultar el listado en Tipo Activ Proceso?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.tipo_activ_proceso_lista"]
endpoints: ["/src/procesos/tipo_activ_proceso_lista"]
source: "docs/catalogo/procesos/flujos/tipo_activ_proceso.md"
estado_revision: "generado"
---

# Ayuda IA - Tipo Activ Proceso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tipo Activ Proceso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Tipo Activ Proceso?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/tipo_activ_proceso_lista`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.tipo_activ_proceso_lista`

## Objetivo

Listado de tipos de actividad con el proceso asignado (propio y no propio) para su gestión desde la pantalla de asignación.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
