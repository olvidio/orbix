---
tipo: "manual_usuario"
modulo: "dossiers"
flujos: 5
estado_revision: "revisado"
---

# Manual De Usuario - dossiers

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Ver la relación de dossiers

### Para Que Sirve

Ver las carpetas (dossiers) disponibles de una persona, actividad o centro/casa y qué puede hacer el usuario en cada una.

### Donde Entrar

- Lista Dossiers (frontend/dossiers/controller/dossiers_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver la relación de dossiers

1. Abrir la ficha de la persona, actividad o centro/casa y pulsar dossiers.
2. Aparece la **relación de dossiers** con un símbolo por carpeta: **dossier modificable**, **dossier de sólo lectura** o **dossier sin permisos**.
3. Pulsar una carpeta con permiso para abrirla.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- No hay control de acceso propio en el endpoint. La visibilidad de cada tipo de dossier depende de

### Referencias Internas

- Flujo: `dossiers.dossiers_lista_fichas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/dossiers_lista_fichas.md`

## Abrir un dossier

### Para Que Sirve

Abrir y recorrer los dossiers de una persona, actividad o centro/casa: cabecera, relación de carpetas y el contenido de cada una (matrículas, asistentes, certificados, tablas).

### Donde Entrar

- Dossiers Ver (frontend/dossiers/controller/dossiers_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Abrir un dossier

1. Desde la ficha, pulsar dossiers y luego la carpeta que interesa.
2. Se muestra su contenido; según el permiso se puede solo consultar o también modificar.
3. Usar los enlaces de la cabecera para volver a la relación de dossiers o a la ficha de origen.

### Errores O Avisos Frecuentes

- `clase_info invalida`
- `No encuentro a nadie con id_nom: <id>`
- `ubi no encontrada`
- `actividad no encontrada`
- `pau desconocido`
- `El dossier <id> no está disponible (sin widget ni datos configurados en d_tipos_dossiers).`

### Permisos

- El endpoint no aplica un middleware de permisos propio; el `permiso` viaja como parámetro y se

### Referencias Internas

- Flujo: `dossiers.dossiers_ver_pantalla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/dossiers_ver_pantalla.md`

## Consultar o cambiar los permisos de un dossier

### Para Que Sirve

Ver o cambiar la definición y los permisos de un tipo de dossier, y volver a la lista.

### Donde Entrar

- Perm Dossier Ver (frontend/dossiers/controller/perm_dossier_ver.php)
- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades

### Tareas Habituales

#### Consultar o cambiar los permisos de un dossier

1. En la lista de **perm_dossiers**, abrir el tipo de dossier.
2. Revisar su descripción, el código y las máscaras de permiso. «depende_modificar» activo implica que solo modificará la oficina correspondiente (agd para los agd, sm para los n, sg para los s).
3. El formulario también muestra campos técnicos editables (**tabla_from**, **tabla_to**, **clase**, **código**). Es intencionado que un administrador de delegación pueda cambiarlos, pero conviene no tocarlos salvo que se sepa lo que se hace.
4. Pulsar **guardar cambios**; después se vuelve a la lista.

### Errores O Avisos Frecuentes

- `No se encuentra el dossier: <id>`

### Permisos

- El caso de uso consulta `$_SESSION['oPerm']`: si el usuario tiene permiso de oficina `admin_sv`

### Referencias Internas

- Flujo: `dossiers.perm_dossier_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/perm_dossier_ver.md`

## Elegir los dossiers a configurar

### Para Que Sirve

Elegir si se quieren configurar los dossiers de personas, centros/casas o actividades, y abrir los permisos de cada tipo.

### Donde Entrar

- Perm Dossiers (frontend/dossiers/controller/perm_dossiers.php)
- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades

### Tareas Habituales

#### Elegir los dossiers a configurar

1. Entrar en **perm_dossiers** y elegir **personas**, **ubis** o **actividades**.
2. Aparece la lista de **dossiers seleccionados**.
3. Pulsar **ver o modificar permisos** del tipo que interese.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- El caso de uso no aplica un control de permisos propio: la autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `dossiers.perm_dossiers.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/perm_dossiers.md`

## Guardar o eliminar un tipo de dossier

### Para Que Sirve

Guardar o eliminar un tipo de dossier (solo administradores).

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sistema > perm_dossiers > ubis · sistema > perm_dossiers > personas · sistema > perm_dossiers > actividades
- **Pills2:** ADMIN LOCAL > perm_dossiers > ubis · ADMIN LOCAL > perm_dossiers > personas · ADMIN LOCAL > perm_dossiers > actividades

### Tareas Habituales

#### Guardar o eliminar un tipo de dossier

1. Abrir el tipo de dossier desde **perm_dossiers**.
2. Pulsar **guardar cambios** para aplicar, o **eliminar** y confirmar para quitarlo.
3. Solo los administradores (admin_sv/admin_sf) pueden hacerlo.

### Errores O Avisos Frecuentes

- `falta id_tipo_dossier`
- `No se encuentra el dossier: <id>`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`

### Permisos

- El caso de uso no aplica un control de permisos propio. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `dossiers.tipo_dossier.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/tipo_dossier.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/dossiers/flujos/`.
