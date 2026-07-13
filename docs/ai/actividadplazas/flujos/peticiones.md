---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Peticiones"
flujo: "actividadplazas.peticiones.gestionar.flujo"
preguntas: ["Como guardar en Peticiones?", "Como eliminar en Peticiones?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
endpoints: ["/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
source: "docs/catalogo/actividadplazas/flujos/peticiones.md"
estado_revision: "generado"
---

# Ayuda IA - Peticiones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Peticiones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Peticiones?
- Como eliminar en Peticiones?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar

1. En la pantalla de peticiones, ordenar las actividades con los desplegables (`DesplegableArray`).
2. Añadir filas con **más actividades** (`fnjs_mas_actividades`) si hace falta.
3. Pulsar el botón de guardar (`fnjs_guardar`): envía `id_nom`, `sactividad` y la lista ordenada a
4. Si tiene éxito, vuelve atrás (`fnjs_nav_atras`).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/peticiones_guardar`

## Eliminar

1. En la misma pantalla, pulsar **Borrar** (`fnjs_borrar`).
2. El sistema elimina todas las peticiones de esa persona+tipo vía `peticiones_eliminar`.
3. Si tiene éxito, refresca la pantalla (`fnjs_actualizar`).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/peticiones_eliminar`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.peticiones_activ`

## Objetivo

Definir (o borrar) la lista priorizada de actividades que una persona solicita como petición de plaza para un tipo y colectivo (`n`, `a`, `agd`).

## Errores Documentados

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`
- `hay un error, no se han guardado todas las peticiones`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
