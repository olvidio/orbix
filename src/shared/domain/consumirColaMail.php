<?php

namespace src\shared\domain;

use DateInterval;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

class consumirColaMail
{
    private $ColaMailRepository;
    public function __construct()
    {

        $this->ColaMailRepository = $GLOBALS['container']->get(ColaMailRepositoryInterface::class);
    }
    public function purge()
    {
        // 1 mes
        $ahora = new DateTimeLocal();
        $ahora->sub(new DateInterval('P1M'));
        $date_iso = $ahora->getIso();
        $this->ColaMailRepository->deleteColaMails($date_iso);
    }

    public function seleccionar($limit)
    {
        $aWhere = ['sended' => 'x', '_limit' => $limit];
        $aOperador['sended'] = 'IS NULL';
        return $this->ColaMailRepository->getColaMails($aWhere, $aOperador);
    }

    public function enviar()
    {
        // de 5 en 5 para no consumir mucha memoria
        while ( $cMails = $this->seleccionar(5)) {
            $ahora = new DateTimeLocal();
            foreach ($cMails as $oMail) {
                //$uuid_item = $oMail->getUuid_item();
                $mail_to = $oMail->getMail_to();
                $subject = $oMail->getSubject();
                $message = $oMail->getMessage();
                $headers = $oMail->getHeaders();
                // para pruebas
                //$mail_to = 'dserrabou@gmail.com';
                mail($mail_to, $subject, $message, $headers);
                $oMail->setSended($ahora);
                $this->ColaMailRepository->Guardar($oMail);
            }
        }
    }
}