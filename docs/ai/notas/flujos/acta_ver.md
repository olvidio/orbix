---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Ver o modificar la cabecera del acta"
flujo: "notas.acta_ver.gestionar.flujo"
preguntas: ["Como ver o modificar la cabecera del acta en Ver o modificar la cabecera del acta?", "Como ver las notas del acta en Ver o modificar la cabecera del acta?", "Como añadir un alumno al acta en Ver o modificar la cabecera del acta?"]
pantallas_principales: ["notas.pantalla.acta_ver"]
fragmentos: ["notas.pantalla.acta_ver"]
endpoints: ["/src/notas/acta_ver_form_data", "/src/notas/acta_ver_notas_listado_data", "/src/notas/acta_ver_add_persona_form_data", "/src/notas/acta_ver_add_persona", "/src/notas/acta_nueva", "/src/notas/acta_modificar"]
source: "docs/catalogo/notas/flujos/acta_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Ver o modificar la cabecera del acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver o modificar la cabecera del acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver o modificar la cabecera del acta en Ver o modificar la cabecera del acta?
- Como ver las notas del acta en Ver o modificar la cabecera del acta?
- Como añadir un alumno al acta en Ver o modificar la cabecera del acta?

## Donde Entrar

- Acta Ver (`notas.pantalla.acta_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver o modificar la cabecera del acta

1. Abrir el acta desde el listado o desde la actividad.
2. Se muestra la cabecera con los datos del acta.
3. Para un acta nueva, rellenarla y guardar; para uno existente, corregir lo que haga falta y guardar los cambios.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_ver_notas_listado_data`
- `/src/notas/acta_ver_add_persona_form_data`
- `/src/notas/acta_ver_add_persona`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`

## Ver las notas del acta

1. Con un acta ya creada y con asignatura, debajo de la cabecera aparece la tabla de alumnos con su nombre, nota y situación.
2. Si de alguna nota no se puede ver el nombre, sale un aviso.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_ver_notas_listado_data`
- `/src/notas/acta_ver_add_persona_form_data`
- `/src/notas/acta_ver_add_persona`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`

## Añadir un alumno al acta

1. Solo está disponible desde el listado de actas, con permiso de escritura y si el acta no tiene el PDF firmado.
2. Elegir a la persona en el desplegable de candidatos (de la dl o publicados para la dl; no salen quienes están en repaso ni quienes ya tienen nota de esa asignatura).
3. Escribir la nota y la nota máxima y guardar.
4. El listado se recarga con el alumno añadido.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_ver_notas_listado_data`
- `/src/notas/acta_ver_add_persona_form_data`
- `/src/notas/acta_ver_add_persona`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_ver`
- `notas.pantalla.acta_ver`

## Objetivo

Consultar o modificar la cabecera de un acta (asignatura, fechas, libro, tribunal, PDF) y, cuando se abre desde el listado de actas, ver las notas ya grabadas y añadir un alumno.

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
