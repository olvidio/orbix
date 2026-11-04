---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Ver la lista de propuestas"
flujo: "encargossacd.propuestas_lista.gestionar.flujo"
preguntas: ["Como ver la lista de propuestas en Ver la lista de propuestas?", "Como asignar o cambiar el sacd de un encargo en Ver la lista de propuestas?", "Como ver el detalle o cambiar la dedicación en Ver la lista de propuestas?"]
pantallas_principales: ["encargossacd.pantalla.propuestas_lista"]
fragmentos: ["encargossacd.pantalla.propuestas_ajax"]
endpoints: ["/src/encargossacd/opciones_seccion_data", "/src/encargossacd/propuestas_ajax"]
source: "docs/catalogo/encargossacd/flujos/propuestas_lista.md"
estado_revision: "generado"
---

# Ayuda IA - Ver la lista de propuestas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver la lista de propuestas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver la lista de propuestas en Ver la lista de propuestas?
- Como asignar o cambiar el sacd de un encargo en Ver la lista de propuestas?
- Como ver el detalle o cambiar la dedicación en Ver la lista de propuestas?

## Donde Entrar

- Propuestas Lista (`encargossacd.pantalla.propuestas_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver la lista de propuestas

1. Abrir **modificar propuestas** desde el menú de propuestas.
2. Elegir la sección y, si se quiere, filtrar por centro; la lista se actualiza sola.
3. Si algo falla, sale un aviso con el mensaje.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/opciones_seccion_data`
- `/src/encargossacd/propuestas_ajax`

## Asignar o cambiar el sacd de un encargo

1. Pulsar el nombre del sacd de un encargo: se abre un desplegable.
2. Elegir el sacd. Si se deja un colaborador en blanco, se borra.
3. La fila se actualiza y el desplegable se cierra.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/opciones_seccion_data`
- `/src/encargossacd/propuestas_ajax`

## Ver el detalle o cambiar la dedicación

1. Pulsar la información o la dedicación de un encargo: se abre una ventana.
2. Para cambiar el horario, modificarlo y guardar; la lista se refresca.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/opciones_seccion_data`
- `/src/encargossacd/propuestas_ajax`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.propuestas_lista`
- `encargossacd.pantalla.propuestas_ajax`

## Objetivo

Filtrar por centro, ver los encargos propuestos y cambiar el sacd titular, suplente o colaborador, o su dedicación (mañanas, tardes, vespertino), antes de aprobar las propuestas. (Redactado desde el código; no contrastado con la pantalla.)

## Errores Documentados

- `Debe crear la tabla de propuestas`
- `No se puede guardar. Vuelva a cargar la vista`
- `Registro no encontrado`
- `Alert cliente: «Primero debe introducir un sacd» (id_sacd == 1)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
