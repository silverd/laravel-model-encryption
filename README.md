# laravel-model-encryption

A trait to encrypt data models in Laravel, this automatically encrypt and decrypt model data overriding getAttribute an setAttribute methods of Eloquent Model.

## How to install

```bash
composer require silverd/laravel-model-encryption:dev-master
```

Publish configuration file, this will create config/encrypt.php

```bash
php artisan vendor:publish --provider="Silverd\Encryptable\ServiceProvider"
``` 

## How to use

1.  You must add `XES_ENCRYPT_CIPHER` / `XES_ENCRYPT_KEY` / `XES_ENCRYPT_IV` in your .env file or set it in your `config/encrypt.php` file

2. Use the `Silverd\Encryptable\Encryptable` trait:

```php
use Silverd\Encryptable\Encryptable;
```

3. Set the `$encryptable` array on your Model.

```php
protected $encryptable = ['encrypted_property'];
```

4. Here's a complete example:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Silverd\Encryptable\Encryptable;

class User extends Model
{
    use Encryptable;

    protected $encryptable = [
        'email',
        'address' ,
        'name',
    ];
}
```

5. Optional. Encrypt your current data

If you have current data in your database you can encrypt it with the: `php artisan encryptable:encryptModel --model="App\\Models\\RxHolder"` command.

Additionally you can decrypt it using the:`php artisan encryptable:decryptModel --model="App\\Models\\RxHolder"` command.

Note: You must implement first the `Encryptable` trait and set `$encryptable` attributes

6. If you are using exists and unique rules with encrypted values replace it with exists_encrypted and unique_encrypted 

```php
$validator = validator(['email'=>'foo@bar.com'], ['email'=>'exists_encrypted:users,email']);
```

7. You can still use `where` functions 

```php
$validator = User::where('email','foo@bar.com')->first();
```

Automatically `foo@bar.com` will be encrypted and pass it to the query builder.
