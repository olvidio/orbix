<?php
// Rutas del módulo Certificados
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/certificados/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /certificados/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/src/certificados/certificado_emitido_delete', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_delete.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_emitido_enviar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_enviar.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_emitido_guardar_pdf', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_guardar_pdf.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_emitido_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_emitido_imprimir_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_imprimir_datos.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_emitido_imprimir_mpdf_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_imprimir_mpdf_datos.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_emitido_lista_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_lista_datos.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_emitido_ver_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_ver_datos.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_recibido_delete', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_recibido_delete.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/certificado_recibido_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_recibido_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/certificados/textos_certificados', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/textos_certificados.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificado_emitido_pdf_download', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_pdf_download.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificado_recibido_pdf_download', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_recibido_pdf_download.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificados_locales_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificados_locales_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificado_recibido_modificar_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_recibido_modificar_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificado_emitido_upload_firmado_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_upload_firmado_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificado_emitido_adjuntar_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_adjuntar_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificado_recibido_adjuntar_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_recibido_adjuntar_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificado_emitido_pdf_upload', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_emitido_pdf_upload.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/certificados/certificado_recibido_pdf_upload', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/certificado_recibido_pdf_upload.php';
    });
};
