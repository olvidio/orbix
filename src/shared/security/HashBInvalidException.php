<?php

namespace src\shared\security;

use RuntimeException;

/**
 * Excepción lanzada por {@see HashB::open()} cuando una cápsula no supera
 * alguna de las validaciones (formato, firma, sesión, acción, caducidad).
 *
 * Usa {@see HashBInvalidException::getReason()} para distinguir el motivo
 * concreto sin acoplarse al mensaje textual, que puede cambiar.
 */
class HashBInvalidException extends RuntimeException
{
    public const MALFORMED = 'malformed';
    public const SIGNATURE_MISMATCH = 'signature_mismatch';
    public const ACTION_MISMATCH = 'action_mismatch';
    public const SESSION_MISMATCH = 'session_mismatch';
    public const EXPIRED = 'expired';

    private string $reason;

    public function __construct(string $message, string $reason)
    {
        parent::__construct($message);
        $this->reason = $reason;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
