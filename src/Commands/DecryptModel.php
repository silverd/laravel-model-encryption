<?php

namespace Silverd\Encryptable\Commands;

use Illuminate\Console\Command;

class DecryptModel extends Command
{
    protected $signature = 'encryptable:decryptModel {--model=}';

    private $model;

    public function handle()
    {
        $class = $this->option('model');

        $this->model = new $class();

        $table = $this->model->getTable();

        \DB::table($table)->chunkById(100, function ($manys) use ($table) {
            foreach ($manys as $record) {
                $updated = $this->getDecryptedAttributes($record);
                \DB::table($table)->where('id', $record->id)->update($updated);
            }
        });

        $this->comment('DONE');
    }

    private function getDecryptedAttributes($record)
    {
        $decrypteds = [];

        foreach ($this->model->getEncryptable() as $attribute) {
            $decrypteds[$attribute] = $this->model->decryptAttribute($record->{$attribute});
        }

        return $decrypteds;
    }
}
