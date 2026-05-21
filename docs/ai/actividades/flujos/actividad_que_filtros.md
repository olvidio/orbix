---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Actividad Que Filtros"
flujo: "actividades.actividad_que_filtros.gestionar.flujo"
preguntas: ["Como ejecutar en Actividad Que Filtros?"]
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que"]
endpoints: ["/src/actividades/actividad_que_filtros"]
source: "docs/catalogo/actividades/flujos/actividad_que_filtros.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad Que Filtros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad Que Filtros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Actividad Que Filtros?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_que`

## Objetivo

Gestiona ActividadQueFiltrosBloque. Genera el HTML del bloque "filtros extra" (filtro_lugar + lugar + organiza + publicada) en la pantalla actividad_que. El bloque solo se muestra a usuarios con permiso de control (perm_ctr); para el resto devuelve cadena vacia. Encapsula todos los accesos a repositorios y entidades de dominio necesarios (Role, DelegacionDropdown, ActividadLugar) de forma que el frontend controller no tenga que depender directamente de src/.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
