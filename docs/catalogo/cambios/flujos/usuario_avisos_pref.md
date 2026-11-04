---
id: "cambios.usuario_avisos_pref.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Configurar un aviso de cambios en actividades"
capacidad: "cambios.usuario_avisos_pref.gestionar"
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref", "cambios.pantalla.usuario_avisos_pref_fases", "cambios.pantalla.usuario_avisos_pref_propiedades", "cambios.pantalla.usuario_avisos_pref_condicion"]
acciones: ["cargar", "grabar"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data", "/src/cambios/cambio_usuario_objeto_pref_guardar", "/src/cambios/cambio_usuario_propiedad_pref_guardar_todas", "/src/cambios/cambio_usuario_propiedad_pref_preview"]
estado_revision: "revisado"
---

# Flujo - Configurar preferencia de aviso

## Objetivo De Usuario

Configurar un aviso de cambios: qué se vigila, en qué actividades y cómo se avisa.

## Punto De Entrada

Menú **sistema > usuarios web > lista usuarios**: buscar al usuario, pulsar **editar** en su fila. Botón **añadir aviso**.

## Escenarios Inferidos

### Configurar un aviso de cambios en actividades

Pasos propuestos:
1. En «Avisos para …», elegir **cambios en:** (el objeto a vigilar).
2. Elegir si es para actividades que organiza **la propia dl** u **otras dl**, y el tipo (**sf/sv**, **asistentes**, **actividad**, **tipo de actividad**).
3. En **fase de referencia** elegir la fase (salen todas las posibles, aunque no tengas permiso; el permiso se comprueba al generar el aviso).
4. Marcar cuándo avisar: **avisar antes de que esté marcada (off)**, **avisar si está marcada (on)** (viene marcada) y **avisar aunque la actividad haya finalizado**.
5. En «opciones» elegir **ver cambios sólo de las casas** (opcional) y **avisar por:** lista o email.
6. En «campos o propiedades» marcar los campos a vigilar (enlaces **[todos]**, **[ninguno]**, **[invertir]**) y, si hace falta, una **condición**.
7. Pulsar **guardar**.

Endpoints asociados:
- `/src/cambios/usuario_avisos_pref_form_data`
- `/src/cambios/cambio_usuario_objeto_pref_guardar`
- `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`
- `/src/cambios/cambio_usuario_propiedad_pref_preview`

## Errores Conocidos

- `falta id_usuario`, `usuario/grupo no encontrado`, `preferencia no encontrada`
- `id_tipo_activ invalido`, `Hay un error, no se ha guardado`
- `faltan parametros` (propiedades)

## Ruta de menú

sin entrada de menú en el índice
