---
id: "shared.locales_posibles"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/locales_posibles"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/shared/infrastructure/ui/http/controllers/locales_posibles.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_imprimir.php", "frontend/certificados/controller/certificado_emitido_ver.php", "frontend/usuarios/controller/preferencias.php"]
casos_uso: []
tags: ["shared", "locales", "posibles"]
estado_revision: "revisado"
---

# Locales Posibles

Listado de idiomas/locales activos para desplegables de interfaz.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve los locales activos de `x_locales` (`id_locale` → `nom_idioma`) para poblar selects de
idioma en certificados y preferencias de usuario. Sin parámetros de entrada.

## Endpoint

- URL: `/src/shared/locales_posibles`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/shared/infrastructure/ui/http/controllers/locales_posibles.php`

## Entrada

Sin parámetros POST. Usa `LocalRepositoryInterface::getArrayLocales()`.

## Salida

- Helper: `ContestarJson::enviar`
- Payload (doble `JSON.parse`): `{ a_locales: { id_locale: "nom_idioma", … } }`.

## Errores conocidos

- Sin mensajes `_()` en el controller.

## Permisos

- Sin control en el endpoint; la pantalla que lo invoca aplica sus permisos.

## Casos De Uso

Lógica inline vía `DependencyResolver` → `PgLocalRepository`.

## Frontend Relacionado

- Carga auxiliar en `preferencias.php`, `certificado_emitido_ver.php` y
  `certificado_emitido_imprimir.php` (sin entrada de menú propia).
