---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Acta Ver"
flujo: "notas.acta_ver.gestionar.flujo"
preguntas: ["Como ver / editar cabecera en Acta Ver?", "Como listado de notas (solo standalone) en Acta Ver?", "Como añadir alumno (solo standalone + permiso escritura + sin pdf) en Acta Ver?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_ver"]
endpoints: ["/src/notas/acta_ver_form_data", "/src/notas/acta_ver_notas_listado_data", "/src/notas/acta_ver_add_persona_form_data", "/src/notas/acta_ver_add_persona", "/src/notas/acta_nueva", "/src/notas/acta_modificar"]
source: "docs/catalogo/notas/flujos/acta_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Acta Ver

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta Ver`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver / editar cabecera en Acta Ver?
- Como listado de notas (solo standalone) en Acta Ver?
- Como añadir alumno (solo standalone + permiso escritura + sin pdf) en Acta Ver?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver / editar cabecera

1. Abrir acta desde el listado o desde la actividad.
2. Cargar cabecera (`acta_ver_form_data`).
3. Guardar alta (`acta_nueva`) o cambios (`acta_modificar`).

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Listado de notas (solo standalone)

1. Con acta existente (no modo nueva) y asignatura válida, la UI pide `acta_ver_notas_listado_data`.
2. Se muestra tabla id/nombre/nota/situación; avisos si hay notas sin acceso al nombre.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Añadir alumno (solo standalone + permiso escritura + sin pdf)

1. Pedir `acta_ver_add_persona_form_data` (desplegable de candidatos).
2. Elegir persona, nota y máximo; enviar `acta_ver_add_persona`.
3. Recargar listado / mensaje de esquema de escritura.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_ver`

## Objetivo

Consultar o modificar la cabecera del acta (asignatura, fechas, libro, tribunal, PDF) y, cuando se abre desde el listado de actas, ver las notas ya grabadas y añadir un alumno.

## Errores Documentados

- `Faltan acta o persona`
- `No se encuentra el acta`
- `El acta está firmada y no se puede modificar`
- `El acta no tiene asignatura`
- `Falta el acta`
- `Aviso listado: existe una nota de la que no se tiene acceso al nombre (id_nom = %s)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
