<?php

namespace src\shared\application;

use DateInterval;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Consume la cola de mails pendientes: purga los antiguos (>1 mes) y
 * envia por `mail()` los marcados como no enviados.
 *
 * Sustituye al antiguo `src\shared\domain\consumirColaMail`. La logica
 * se dispara desde el driver CLI
 * `src/shared/infrastructure/cli/enviar_mails_en_cola.php`, que a su
 * vez se invoca desde crontab en el servidor exterior (el unico con
 * acceso al MTA).
 */
final class EnviarMailsEnCola
{
    private const BATCH_SIZE = 5;
    private const PURGE_INTERVAL = 'P1M';

    private ColaMailRepositoryInterface $ColaMailRepository;

    public function __construct(?ColaMailRepositoryInterface $ColaMailRepository = null)
    {
        $this->ColaMailRepository = $ColaMailRepository
            ?? DependencyResolver::get(ColaMailRepositoryInterface::class);
    }

    /**
     * Purga los mails antiguos y envia los pendientes.
     *
     * @return array{purgados:bool,enviados:int}
     */
    public function execute(): array
    {
        $purgados = $this->purge();
        $enviados = $this->enviar();
        return [
            'purgados' => $purgados,
            'enviados' => $enviados,
        ];
    }

    public function purge(): bool
    {
        $ahora = new DateTimeLocal();
        $ahora->sub(new DateInterval(self::PURGE_INTERVAL));
        return $this->ColaMailRepository->deleteColaMails($ahora->getIso()) !== false;
    }

    public function enviar(): int
    {
        $enviados = 0;
        while ($cMails = $this->seleccionar(self::BATCH_SIZE)) {
            $ahora = new DateTimeLocal();
            foreach ($cMails as $oMail) {
                $mail_to = $oMail->getMail_to();
                $subject = $oMail->getSubject();
                $message = $oMail->getMessage();
                $headers = $oMail->getHeaders();
                mail($mail_to, $subject, $message, $headers);
                $oMail->setSended($ahora);
                $this->ColaMailRepository->Guardar($oMail);
                $enviados++;
            }
        }
        return $enviados;
    }

    private function seleccionar(int $limit): array
    {
        $aWhere = ['sended' => 'x', '_limit' => $limit];
        $aOperador = ['sended' => 'IS NULL'];
        return $this->ColaMailRepository->getColaMails($aWhere, $aOperador);
    }
}
