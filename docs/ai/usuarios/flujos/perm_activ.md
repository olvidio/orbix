---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Añadir un permiso en actividades"
flujo: "usuarios.perm_activ.gestionar.flujo"
preguntas: ["Como añadir un permiso en actividades en Añadir un permiso en actividades?", "Como modificar o eliminar un permiso en actividades en Añadir un permiso en actividades?"]
pantallas_principales: ["usuarios.pantalla.perm_activ_lista"]
fragmentos: ["usuarios.pantalla.perm_activ_lista"]
endpoints: ["/src/usuarios/perm_activ_eliminar", "/src/usuarios/perm_activ_guardar", "/src/usuarios/perm_activ_lista"]
source: "docs/catalogo/usuarios/flujos/perm_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir un permiso en actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir un permiso en actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir un permiso en actividades en Añadir un permiso en actividades?
- Como modificar o eliminar un permiso en actividades en Añadir un permiso en actividades?

## Donde Entrar

- Perm Activ Lista (`usuarios.pantalla.perm_activ_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir un permiso en actividades

1. En la ficha del usuario, en «Permisos en actividades», pulsar **añadir permiso**. Se abre «Añadir nuevo permiso a …».
2. Elegir si es para actividades que organiza **la propia dl** u **otras dl**, y el tipo de actividad con **sf/sv**, **asistentes**, **actividad** y **tipo de actividad** (cuanto más concreto, más manda: «sv n crt» manda sobre «sv n»).
3. Marcar en **afecta a** lo que se controla: datos, economic, sacd, ctr, id_tarifa, cargos, asistentes o asistentesSacd.
4. Para cada uno elegir la **fase de referencia** y el **permiso off (sin marcar)** y **permiso on (marcado)**: nada, ocupado, ver, modificar, crear o borrar. «Ocupado» es un punto intermedio entre nada y ver: se ve que la actividad está ocupada, pero no se puede ver la información de qué es lo que ocupa. Si falta la fase de referencia, avisa «OJO! Comprobar la fase de referencia.» (el aviso es intencionado y puede salir más de una vez al cambiar los desplegables).
5. Pulsar **guardar**. La ficha vuelve a mostrar la tabla con dl propia, tipo de actividad, afecta a, fase de referencia, permiso off y permiso on.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/perm_activ_eliminar`
- `/src/usuarios/perm_activ_guardar`
- `/src/usuarios/perm_activ_lista`

## Modificar o eliminar un permiso en actividades

1. En la tabla «propios», marcar la fila.
2. Pulsar **modificar** para cambiarla, o **eliminar** y confirmar «¿Esta seguro que desea borrar este permiso?».
3. A igual nivel, los permisos propios mandan sobre los de los grupos del usuario.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/perm_activ_eliminar`
- `/src/usuarios/perm_activ_guardar`
- `/src/usuarios/perm_activ_lista`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.perm_activ_lista`
- `usuarios.pantalla.perm_activ_lista`

## Objetivo

Dar o quitar a un usuario permisos sobre un tipo de actividad (ver, modificar, crear, borrar) según la fase en que esté la actividad.

## Errores Documentados

- `no existe el registro`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
