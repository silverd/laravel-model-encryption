<?php

namespace Silverd\Encryptable;

class Encrypter
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function encrypt($value, string $iv = null)
    {
        $encrypted = openssl_encrypt($value, $this->config['cipher'], $this->config['key'], OPENSSL_RAW_DATA, $iv ?: $this->config['iv']);

        return strtoupper(bin2hex($encrypted));
    }

    public function decrypt($value, string $iv = null)
    {
        try {
            $encrypted = hex2bin($value);
        }
        catch (\Throwable $e) {
            return $value;
        }

        return openssl_decrypt($encrypted, $this->config['cipher'], $this->config['key'], OPENSSL_RAW_DATA, $iv ?: $this->config['iv']);
    }

    public function getEncryptExpr(string $field)
    {
        return 'HEX(AES_ENCRYPT(' . $field . ', \'' . $this->config['key'] . '\', \'' . $this->config['iv'] . '\'))';
    }

    public function getDecryptExpr(string $field)
    {
        return 'AES_DECRYPT(UNHEX(' . $field . '), \'' . $this->config['key'] . '\', \'' . $this->config['iv'] . '\')';
    }
}
