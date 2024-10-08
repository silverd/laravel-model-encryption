<?php

namespace Silverd\Encryptable\Commands;

use Illuminate\Console\Command;

class EncryptModel extends Command
{
    protected $signature = 'encryptable:encryptModel {--model=}';

    private $model;

    public function handle()
    {
        $class = $this->option('model');

        $this->model = new $class();

        $table = $this->model->getTable();

        \DB::table($table)->chunkById(100, function ($manys) use ($table) {
            foreach ($manys as $record) {
                $updated = $this->getEncryptedAttributes($record);
                \DB::table($table)->where('id', $record->id)->update($updated);
            }
        });

        $this->comment('DONE');
    }

    private function getEncryptedAttributes($record)
    {
        $encrypteds = [];

        foreach ($this->model->getEncryptable() as $attribute) {
            $encrypteds[$attribute] = $this->model->encryptAttribute($record->{$attribute});
        }

        return $encrypteds;
    }
}
