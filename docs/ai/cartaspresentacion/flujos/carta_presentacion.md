---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Dar de alta o modificar la carta de un centro"
flujo: "cartaspresentacion.carta_presentacion.gestionar.flujo"
preguntas: ["Como dar de alta o modificar la carta de un centro en Dar de alta o modificar la carta de un centro?", "Como quitar la carta de un centro en Dar de alta o modificar la carta de un centro?"]
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_form"]
endpoints: ["/src/cartaspresentacion/carta_presentacion_eliminar", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update"]
source: "docs/catalogo/cartaspresentacion/flujos/carta_presentacion.md"
estado_revision: "generado"
---

# Ayuda IA - Dar de alta o modificar la carta de un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Dar de alta o modificar la carta de un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como dar de alta o modificar la carta de un centro en Dar de alta o modificar la carta de un centro?
- Como quitar la carta de un centro en Dar de alta o modificar la carta de un centro?

## Donde Entrar

- Cartas Presentacion (`cartaspresentacion.pantalla.cartas_presentacion`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Dar de alta o modificar la carta de un centro

1. En la lista, pulsar **director** en la fila del centro.
2. Se abre el formulario; si el centro no tiene carta, sale vacío y se crea al guardar. No se pueden modificar datos de otra delegación.
3. Rellenar nombre, teléfono, e-mail, zona y observaciones.
4. Pulsar **Guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/carta_presentacion_eliminar`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`

## Quitar la carta de un centro

1. En una fila que ya tiene carta, pulsar **quitar**.
2. Confirmar el aviso.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/carta_presentacion_eliminar`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion`
- `cartaspresentacion.pantalla.cartas_presentacion_form`

## Objetivo

Dar de alta, modificar o quitar los datos de presentación de un centro.

## Errores Documentados

- `Faltan id_ubi o id_direccion`
- `No puede modificar datos de otra dl`
- `Carta de presentacion no encontrada`
- `Hay un error, no se ha guardado. / Hay un error, no se ha borrado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
