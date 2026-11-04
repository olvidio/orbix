---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Guardar las peticiones de una persona"
flujo: "actividadplazas.peticiones.gestionar.flujo"
preguntas: ["Como guardar las peticiones de una persona en Guardar las peticiones de una persona?", "Como borrar las peticiones de una persona en Guardar las peticiones de una persona?"]
pantallas_principales: ["actividadplazas.pantalla.peticiones_activ"]
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
endpoints: ["/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
source: "docs/catalogo/actividadplazas/flujos/peticiones.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar las peticiones de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar las peticiones de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar las peticiones de una persona en Guardar las peticiones de una persona?
- Como borrar las peticiones de una persona en Guardar las peticiones de una persona?

## Donde Entrar

- Peticiones Activ (`actividadplazas.pantalla.peticiones_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar las peticiones de una persona

1. En la pantalla de peticiones, ordenar las actividades con los desplegables: la primera es la preferida.
2. Si hacen falta más filas, pulsar **más actividades**.
3. Pulsar **guardar**. Las peticiones anteriores de esa persona se sustituyen por las nuevas, en ese orden.
4. Al terminar, la pantalla vuelve a la anterior.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

## Borrar las peticiones de una persona

1. En la misma pantalla, pulsar **Borrar**.
2. Se eliminan todas las peticiones de esa persona para ese tipo de actividad y la pantalla se refresca.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.peticiones_activ`
- `actividadplazas.pantalla.peticiones_activ`

## Objetivo

Definir, o borrar, la lista ordenada de actividades que pide una persona como plaza, para un tipo de actividad y un colectivo (n, a o agd).

## Errores Documentados

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`
- `hay un error, no se han guardado todas las peticiones`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
