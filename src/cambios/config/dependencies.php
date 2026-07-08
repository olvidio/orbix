<?php

use src\cambios\application\AvisosEnviarMails;
use src\cambios\application\AvisosGenerarListaData;
use src\cambios\application\AvisosGenerarTabla;
use src\cambios\application\CambioAvisoTxtBuilder;
use src\cambios\application\CambioUsuarioEliminar;
use src\cambios\application\CambioUsuarioEliminarHastaFecha;
use src\cambios\application\CambioUsuarioObjetoPrefEliminar;
use src\cambios\application\CambioUsuarioObjetoPrefFasesData;
use src\cambios\application\CambioUsuarioObjetoPrefGuardar;
use src\cambios\application\CambioUsuarioObjetoPrefPropiedadesData;
use src\cambios\application\CambioUsuarioPropiedadPrefGuardarTodas;
use src\cambios\application\CambioUsuarioPropiedadPrefItemData;
use src\cambios\application\CambioUsuarioPropiedadPrefPreview;
use src\cambios\application\ActividadParaAvisoLookup;
use src\cambios\application\CambioParaAvisoLookup;
use src\cambios\application\legacy\Avisos;
use src\cambios\application\RegistrarCambio;
use src\cambios\application\UsuarioAvisosPrefFormData;
use src\cambios\application\UsuarioFormAvisosData;
use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\infrastructure\persistence\postgresql\PgCambioAnotadoRepository;
use src\cambios\infrastructure\persistence\postgresql\PgCambioDlRepository;
use src\cambios\infrastructure\persistence\postgresql\PgCambioRepository;
use src\cambios\infrastructure\persistence\postgresql\PgCambioUsuarioObjetoPrefRepository;
use src\cambios\infrastructure\persistence\postgresql\PgCambioUsuarioPropiedadPrefRepository;
use src\cambios\infrastructure\persistence\postgresql\PgCambioUsuarioRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    CambioAnotadoRepositoryInterface::class => autowire(PgCambioAnotadoRepository::class),
    CambioDlRepositoryInterface::class => autowire(PgCambioDlRepository::class),
    CambioRepositoryInterface::class => autowire(PgCambioRepository::class),
    CambioUsuarioObjetoPrefRepositoryInterface::class => autowire(PgCambioUsuarioObjetoPrefRepository::class),
    CambioUsuarioPropiedadPrefRepositoryInterface::class => autowire(PgCambioUsuarioPropiedadPrefRepository::class),
    CambioUsuarioRepositoryInterface::class => autowire(PgCambioUsuarioRepository::class),

    // Casos de uso / Application classes
    Avisos::class => autowire(Avisos::class),
    AvisosEnviarMails::class => autowire(AvisosEnviarMails::class),
    AvisosGenerarListaData::class => autowire(AvisosGenerarListaData::class),
    AvisosGenerarTabla::class => autowire(AvisosGenerarTabla::class),
    CambioAvisoTxtBuilder::class => autowire(CambioAvisoTxtBuilder::class),
    CambioParaAvisoLookup::class => autowire(CambioParaAvisoLookup::class),
    ActividadParaAvisoLookup::class => autowire(ActividadParaAvisoLookup::class),
    CambioUsuarioEliminar::class => autowire(CambioUsuarioEliminar::class),
    CambioUsuarioEliminarHastaFecha::class => autowire(CambioUsuarioEliminarHastaFecha::class),
    CambioUsuarioObjetoPrefEliminar::class => autowire(CambioUsuarioObjetoPrefEliminar::class),
    CambioUsuarioObjetoPrefFasesData::class => autowire(CambioUsuarioObjetoPrefFasesData::class),
    CambioUsuarioObjetoPrefGuardar::class => autowire(CambioUsuarioObjetoPrefGuardar::class),
    CambioUsuarioObjetoPrefPropiedadesData::class => autowire(CambioUsuarioObjetoPrefPropiedadesData::class),
    CambioUsuarioPropiedadPrefGuardarTodas::class => autowire(CambioUsuarioPropiedadPrefGuardarTodas::class),
    CambioUsuarioPropiedadPrefItemData::class => autowire(CambioUsuarioPropiedadPrefItemData::class),
    CambioUsuarioPropiedadPrefPreview::class => autowire(CambioUsuarioPropiedadPrefPreview::class),
    RegistrarCambio::class => autowire(RegistrarCambio::class),
    UsuarioAvisosPrefFormData::class => autowire(UsuarioAvisosPrefFormData::class),
    UsuarioFormAvisosData::class => autowire(UsuarioFormAvisosData::class),
];
