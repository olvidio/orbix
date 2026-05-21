---
id: "dossiers.perm_dossier_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Perm Dossier Ver"
capacidad: "dossiers.perm_dossier_ver.gestionar"
pantallas_principales: []
fragmentos: ["dossiers.pantalla.perm_dossier_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/perm_dossier_ver_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Perm Dossier Ver

Propuesta generada automaticamente desde la capacidad `dossiers.perm_dossier_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PermDossierVer. Formulario "permisos de acceso" para un tipo de dossier. El backend devuelve sólo datos: - go_to_link_spec ({path, query}) para que el frontend firme con HashFront. - hash_config (campos_form, campos_no, campos_hidden) para que el frontend componga el bloque hidden con HashFront; el valor de go_to dentro de campos_hidden se inyecta firmado en el borde del frontend. - permiso_dossier_bit_map + enteros permiso_lectura / permiso_escritura; el HTML de checkboxes lo genera el controlador frontend con {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.perm_dossier_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.app`
- `html.campo_to`
- `html.class`
- `html.codigo`
- `html.depende_modificar`
- `html.descripcion`
- `html.id_tipo_dossier`
- `html.id_tipo_dossier_rel`
- `html.que`
- `html.tabla_from`
- `html.tabla_to`
- `post.id_tipo_dossier`
- `post.tipo`

Acciones JavaScript:
- `fnjs_eliminar`
- `fnjs_guardar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/dossiers/perm_dossier_ver_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
