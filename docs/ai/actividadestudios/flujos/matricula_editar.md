---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matricula Editar"
flujo: "actividadestudios.matricula_editar.gestionar.flujo"
preguntas: ["Como ejecutar en Matricula Editar?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
endpoints: ["/src/actividadestudios/matricula_editar"]
source: "docs/catalogo/actividadestudios/flujos/matricula_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Matricula Editar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matricula Editar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Matricula Editar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. En dossier 1303 o 3103, seleccionar una matrícula y pulsar **modificar**.
2. Ajustar nivel, asignatura o preceptor en el formulario.
3. Pulsar **guardar**; el sistema persiste los cambios en la matrícula.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matricula_editar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_matriculas_de_una_persona`

## Objetivo

El usuario modifica nivel, asignatura, preceptor u otros datos de una matrícula ya creada y guarda los cambios. Sustituye el case `editar` de `update_3103.php`.

## Errores Documentados

- `faltan claves de la matricula`
- `hay un error, no se ha guardado`
- `no encuentro la matricula`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
