---
id: "dossiers.perm_dossier_ver.gestionar"
tipo: "capacidad"
modulo: "dossiers"
nombre: "Gestionar Perm Dossier Ver"
entidades: ["PermDossierVer"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/perm_dossier_ver_data"]
pantallas: ["frontend/dossiers/controller/perm_dossier_ver.php"]
casos_uso: ["src\\dossiers\\application\\PermDossierVerFormData"]
tags: ["data", "dossier", "dossiers", "perm", "perm_dossier_ver", "ver"]
estado_revision: "generado"
---

# Gestionar Perm Dossier Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `perm_dossier_ver`.

## Objetivo Funcional

Gestiona PermDossierVer. Formulario "permisos de acceso" para un tipo de dossier. El backend devuelve sólo datos: - go_to_link_spec ({path, query}) para que el frontend firme con HashFront. - hash_config (campos_form, campos_no, campos_hidden) para que el frontend componga el bloque hidden con HashFront; el valor de go_to dentro de campos_hidden se inyecta firmado en el borde del frontend. - permiso_dossier_bit_map + enteros permiso_lectura / permiso_escritura; el HTML de checkboxes lo genera el controlador frontend con {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dossiers/perm_dossier_ver_data`

## Pantallas Relacionadas

- `frontend/dossiers/controller/perm_dossier_ver.php`

## Casos De Uso Detectados

- `src\dossiers\application\PermDossierVerFormData`

## Pistas Desde Endpoints

- Formulario "permisos de acceso" para un tipo de dossier. El backend devuelve sólo datos: - `go_to_link_spec` ({path, query}) para que el frontend firme con HashFront. - `hash_config` (campos_form, campos_no, campos_hidden) para que el frontend componga el bloque hidden con HashFront; el valor de `go_to` dentro de `campos_hidden` se inyecta firmado en el borde del frontend. - `permiso_dossier_bit_map` + enteros `permiso_lectura` / `permiso_escritura`; el HTML de checkboxes lo genera el controlador frontend con {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
