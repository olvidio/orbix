---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Añadir una nota"
flujo: "notas.persona_nota.gestionar.flujo"
preguntas: ["Como añadir una nota en Añadir una nota?", "Como quitar una nota en Añadir una nota?"]
pantallas_principales: ["notas.pantalla.form_notas_de_una_persona"]
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
endpoints: ["/src/notas/persona_nota_eliminar", "/src/notas/persona_nota_nueva"]
source: "docs/catalogo/notas/flujos/persona_nota.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir una nota

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir una nota`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir una nota en Añadir una nota?
- Como quitar una nota en Añadir una nota?

## Donde Entrar

- Form Notas De Una Persona (`notas.pantalla.form_notas_de_una_persona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir una nota

1. Abrir el dossier de notas de la persona (se titula «Notas del stgr»; sin notas dice «no hay ninguna fila») y pulsar **añadir nota**.
2. Se abre «asignaturas aprobadas». Elegir si procede de un **acta** o de un **certificado**: delegación (por ejemplo dlb) y número (formato num/aa, por ejemplo 38/26).
3. Elegir la **Asignatura**, escribir la **nota** (y «sobre» cuánto, por defecto 10), la **situación** (por ejemplo «nota numérica») y la **fecha acta**.
4. Marcar **preceptor** si procede, elegir la **época** (sin especificar, ca o semestre invierno), la actividad en **cursada en** (enlace **añadir ca**) y un **detalle** opcional.
5. Pulsar **Guardar** una sola vez (un doble clic no guarda la nota dos veces). Una asignatura que ya tiene nota deja de salir en el desplegable de asignaturas, así que no se puede guardar dos veces. Sale el aviso «Nota guardada en el esquema H-dlbv» (el nombre del esquema es intencionado) y vuelve la tabla con asignatura, nota (por ejemplo «Cum laude (8/10)»), acta, fecha acta, preceptor, época (ca, semestre invierno o sin especificar), detalle y cursada en.
6. Si se guarda dos veces la misma nota, quedan dos filas iguales.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/persona_nota_eliminar`
- `/src/notas/persona_nota_nueva`

## Quitar una nota

1. En la tabla de notas marcar la fila y pulsar **borrar asignatura**.
2. Confirmar «¿Está seguro que desea borrar la nota de esta asignatura?».

Referencias tecnicas para verificar la respuesta:
- `/src/notas/persona_nota_eliminar`
- `/src/notas/persona_nota_nueva`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.form_notas_de_una_persona`
- `notas.pantalla.form_notas_de_una_persona`

## Objetivo

Apuntar a una persona una asignatura aprobada, con su nota y el acta o certificado.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
