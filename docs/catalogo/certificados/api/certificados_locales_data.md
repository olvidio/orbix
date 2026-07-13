---
id: "certificados.certificados_locales_data"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificados_locales_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificados_locales_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_adjuntar.php", "frontend/certificados/controller/certificado_recibido_adjuntar.php", "frontend/certificados/controller/certificado_recibido_modificar.php"]
casos_uso: []
tags: ["certificados", "certificados", "locales", "data"]
estado_revision: "revisado"
---

# Certificados Locales Data

Listado de idiomas/locales para desplegables de certificados.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve `a_locales` desde `LocalRepository::getArrayLocales()` para poblar el selector de idioma
en formularios de adjuntar/modificar certificados.

## Endpoint

- URL: `/src/certificados/certificados_locales_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificados_locales_data.php`

## Entrada

Sin parámetros POST.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload: `a_locales` (`array` id → nombre idioma)

## Errores conocidos

- Excepciones del repositorio propagadas en `mensaje`

## Permisos

- Sin control de permisos propio.

## Casos De Uso

- Lógica inline (`LocalRepositoryInterface`).

## Frontend Relacionado

- Formularios adjuntar/modificar emitidos y recibidos (desplegable idioma).
