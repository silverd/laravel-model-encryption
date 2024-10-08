<?php

namespace Silverd\Encryptable;

trait Encryptable
{
    public function isEncryptable(string $field)
    {
        $field = str_replace($this->getTable() . '.', '', $field);

        return in_array($field, $this->encryptable);
    }

    public function decryptAttribute($value)
    {
        return $value ? app('encryption')->decrypt($value, $this->aes_iv) : '';
    }

    public function encryptAttribute($value)
    {
        return $value ? app('encryption')->encrypt($value, $this->aes_iv) : '';
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($this->isEncryptable($key)) {
            $value = $this->decryptAttribute($value);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if ($this->isEncryptable($key)) {
            $value = $this->encryptAttribute($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getArrayableAttributes()
    {
        $attributes = parent::getArrayableAttributes();

        foreach ($attributes as $key => $attribute) {
            if ($this->isEncryptable($key)) {
                $attributes[$key] = $this->decryptAttribute($attribute);
            }
        }

        return $attributes;
    }

    public function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        
        return new EncryptableQueryBuilder($connection, $this);
    }

    public function getEncryptable()
    {
        return $this->encryptable ?: [];
    }

    public function getEncryptExpr(string $field)
    {
        return app('encryption')->getEncryptExpr($field);
    }

    public function getDecryptExpr(string $field)
    {
        return app('encryption')->getDecryptExpr($field);
    }
}
