---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadcargos"
titulo: "Añadir un cargo"
flujo: "actividadcargos.cargo.gestionar.flujo"
preguntas: ["Como añadir un cargo en Añadir un cargo?", "Como quitar un cargo en Añadir un cargo?"]
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.select_cargos_de_actividad", "actividadcargos.pantalla.select_cargos_personas_en_actividad"]
endpoints: ["/src/actividadcargos/cargo_eliminar", "/src/actividadcargos/cargo_nuevo"]
source: "docs/catalogo/actividadcargos/flujos/cargo.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir un cargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir un cargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir un cargo en Añadir un cargo?
- Como quitar un cargo en Añadir un cargo?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir un cargo

1. Abrir el dossier de cargos, de la actividad o de la persona.
2. Pulsar el enlace **añadir …** del colectivo o tipo de actividad que corresponda.
3. Completar el formulario **Cargo de una actividad**: persona o actividad, tipo de cargo, agd, observaciones y, en las altas, **¿asiste?** si procede.
4. Pulsar **Guardar datos del cargo**.
5. Comprobar que la fila aparece en la relación de cargos (y en asistentes, si se marcó **¿asiste?**).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

## Quitar un cargo

1. En la relación de cargos, marcar **una sola fila**.
2. Pulsar **quitar cargo**.
3. Leer el aviso de confirmación; puede avisar de que también se borra al asistente si se trata del tipo s o sg y el usuario es de des o vcsd.
4. Confirmar. El listado se actualiza solo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

## Pantallas Y Fragmentos Relacionados

- `actividadcargos.pantalla.select_cargos_de_actividad`
- `actividadcargos.pantalla.select_cargos_personas_en_actividad`

## Objetivo

Ver, añadir y quitar cargos de personas en actividades. Se consulta y se pulsa el enlace de alta en la relación de cargos; el alta se hace en un formulario y la baja es directa.

## Errores Documentados

- `falta id_item`
- `faltan parametros id_activ / id_nom / id_cargo`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha eliminado el asistente`
- `hay un error, no se ha guardado el asistente`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
