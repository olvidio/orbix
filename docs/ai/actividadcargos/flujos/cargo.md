---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadcargos"
titulo: "Cargo"
flujo: "actividadcargos.cargo.gestionar.flujo"
preguntas: ["Como crear en Cargo?", "Como eliminar en Cargo?"]
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.select_cargos_de_actividad", "actividadcargos.pantalla.select_cargos_personas_en_actividad"]
endpoints: ["/src/actividadcargos/cargo_eliminar", "/src/actividadcargos/cargo_nuevo"]
source: "docs/catalogo/actividadcargos/flujos/cargo.md"
estado_revision: "generado"
---

# Ayuda IA - Cargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Cargo?
- Como eliminar en Cargo?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Abrir el dossier de cargos (actividad o persona).
2. Pulsar el enlace **añadir …** del colectivo o tipo de actividad permitido.
3. Completar el formulario **Cargo de una actividad** (persona/actividad, tipo de cargo, AGD, observaciones; en altas, **¿asiste?** si aplica).
4. Pulsar **Guardar datos del cargo**.
5. Comprobar que la fila aparece en la relación de cargos (y en asistentes si marcó **¿asiste?**).

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Eliminar

1. En la relación de cargos, marcar **una sola fila**.
2. Pulsar **quitar cargo**.
3. Leer el aviso de confirmación (puede indicar borrado del asistente si `des`/`vcsd` y tipo `s`/`sg`).
4. Confirmar.
5. El listado se refresca automáticamente (`fnjs_actualizar`).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadcargos/cargo_eliminar`

## Pantallas Y Fragmentos Relacionados

- `actividadcargos.pantalla.select_cargos_de_actividad`
- `actividadcargos.pantalla.select_cargos_personas_en_actividad`

## Objetivo

Ver, añadir y quitar cargos de personas en actividades. La consulta y los enlaces de alta se hacen en el widget; la alta concreta pasa por el formulario (`cargo_nuevo`); la baja es directa vía `cargo_eliminar`.

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
