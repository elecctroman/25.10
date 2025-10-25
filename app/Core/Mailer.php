<?php

declare(strict_types=1);

namespace App\Core;

class Mailer
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function send(string $to, string $subject, string $body): bool
    {
        if (($this->config['driver'] ?? 'smtp') === 'smtp') {
            return $this->sendSmtp($to, $subject, $body);
        }

        $headers = sprintf("From: %s\r\nContent-Type: text/plain; charset=utf-8", $this->config['from_email']);
        return mail($to, $subject, $body, $headers);
    }

    private function sendSmtp(string $to, string $subject, string $body): bool
    {
        $host = $this->config['host'];
        $port = (int) $this->config['port'];
        $username = $this->config['username'];
        $password = $this->config['password'];
        $from = $this->config['from_email'];

        $socket = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$socket) {
            ErrorLogger::log('error', 'SMTP connection failed', ['error' => $errstr]);
            return false;
        }

        $this->expect($socket, 220);
        $this->command($socket, 'EHLO localhost');
        if (!empty($username)) {
            $this->command($socket, 'AUTH LOGIN');
            $this->command($socket, base64_encode($username));
            $this->command($socket, base64_encode($password));
        }
        $this->command($socket, 'MAIL FROM: <' . $from . '>');
        $this->command($socket, 'RCPT TO: <' . $to . '>');
        $this->command($socket, 'DATA');
        $headers = "From: {$from}\r\nTo: {$to}\r\nSubject: {$subject}\r\nContent-Type: text/plain; charset=utf-8\r\n";
        $this->command($socket, $headers . "\r\n" . $body . "\r\n.");
        $this->command($socket, 'QUIT');
        fclose($socket);
        return true;
    }

    private function command($socket, string $command): void
    {
        fwrite($socket, $command . "\r\n");
        $this->expect($socket, null);
    }

    private function expect($socket, ?int $code): void
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        if ($code !== null && (int) substr($response, 0, 3) !== $code) {
            throw new \RuntimeException('SMTP error: ' . $response);
        }
    }
}
