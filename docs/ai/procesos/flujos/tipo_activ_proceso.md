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

Gestiona TipoActivProcesoLista. Caso de uso: devuelve el listado estructurado de tipos de actividad con el proceso propio / no-propio asignado. El frontend renderiza la tabla con frontend\shared\web\Lista.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
