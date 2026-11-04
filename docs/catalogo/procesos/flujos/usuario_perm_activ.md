---
id: "procesos.usuario_perm_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Definir un permiso de actividades de un usuario"
capacidad: "procesos.usuario_perm_activ.gestionar"
pantallas_principales: ["procesos.pantalla.usuario_perm_activ"]
fragmentos: ["procesos.pantalla.usuario_perm_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/usuario_perm_activ_data"]
estado_revision: "revisado"
---

# Flujo - Permisos de actividad de usuario

## Objetivo De Usuario

Dar de alta o modificar un permiso de actividades de un usuario: tipo de actividad, ámbito, fase y permiso.

## Punto De Entrada

Ficha del usuario, apartado **Permisos en actividades > propios**.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.usuario_perm_activ`

## Escenarios Inferidos

### Definir un permiso de actividades de un usuario

Pasos propuestos:
1. En la ficha del usuario, pulsar **añadir permiso**.
2. Elegir el tipo de actividad y el ámbito (propia dl u otras).
3. Elegir la fase de referencia y el permiso.
4. Guardar. Manda el nivel más concreto («sv n crt» sobre «sv n»).

Endpoints asociados:
- `/src/procesos/usuario_perm_activ_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl_propia`
- `form.extendida`
- `form.fase_ref`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `form.perm_off`
- `form.perm_on`
- `post.dl_propia`
- `post.id_tipo_activ_txt`
- `post.id_usuario`
- `post.que`
- `post.quien`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/usuario_perm_activ_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
