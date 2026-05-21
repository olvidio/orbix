---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matricula"
flujo: "actividadestudios.matricula.gestionar.flujo"
preguntas: ["Como crear en Matricula?", "Como eliminar en Matricula?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona", "actividadestudios.pantalla.matriculas_lista", "actividadestudios.pantalla.matriculas_pendientes"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matricula_nueva"]
source: "docs/catalogo/actividadestudios/flujos/matricula.md"
estado_revision: "generado"
---

# Ayuda IA - Matricula

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matricula`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Matricula?
- Como eliminar en Matricula?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matricula_eliminar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_matriculas_de_una_persona`
- `actividadestudios.pantalla.matriculas_lista`
- `actividadestudios.pantalla.matriculas_pendientes`

## Objetivo

Gestiona Matricula. Crea una matricula. Elimina una o varias matriculas.

## Errores Documentados

- `falta id_activ o id_nom`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no encuentro asignatura para ese nivel`
- `no encuentro la matricula`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
