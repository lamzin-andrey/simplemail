#### Content / Содержание

[Ru](#ru)

[En](#en)

# En

#### Content

[About](#about)

[Installation](#installation)

[Usage on product server](#usage-on-product-server)

[Usage on localhost linux ubuntu and gmail service](#usage-on-localhost-linux-ubuntu-and-gmail-service)

## About

This reliable and comfortable send email class with attachment support using PHP mail() function.
I use it PHP class last ten years and I see, than it more reliable then Swift_Mailer and Symfony Mailer email tools.

It is not modest, but I will write.
In November 2019 I created Symfony 3.4 project, it use gmail service on my localhost and hosting provider 
mailbox in production site.

03 January 2020 year I find, that suddenly emails stopped sending from my localhost (I use gmail account and ssmtp). There were no errors in the logs of Symphonies 3.4.

I do not edited my Symfony scripts. 

I try send email from Symfony 5.0.2 project, but got error "Exception occurred while flushing email queue: Connection could not be established with host smtp.gmail.com :stream_socket_client(): SSL operation failed with code 1. OpenSSL Error messages: error:1416F086:SSL routines:tls_process_server_certificate:certificate verify failed".


I try send email use my class SimpleMail - and  it was successfull.

I many used this script, on different php hostings. It realy is working (for example - one from first my commits with it class here https://github.com/lamzin-andrey/gz.loc/blob/master/www/lib/classes/mail/SampleMail.php).

Therefore I public this PHP class, I hope that will helpfull for you too.

Happy New Year and Merry Christmas.

## Installation

`composer require landlib/simplemail`

or

`git clone https://github.com/lamzin-andrey/simplemail`

## Usage on product server

### Configure mailbox in ISP Manager on your hosting provider

Create mailbox using ISP Manager interface.
Will be sure, than you can send email from it use RoundCude or other interface.


### Example

```php
use Landlib\SimpleMail;

//Simple email
$sender = 'yoursendmailbox@yoursite.com';
$recipient = 'yourothermailbox@gmail.com';
$mailer = new SimpleMail();
$mailer->setSubject('It test package landlib/simplemail');
$mailer->setFrom($sender, 'Your name');
$mailer->setTo($recipient);
$mailer->setBody('Hello, my friend', 'text/html', 'UTF-8');
$r = $mailer->send();
var_dump($r);

//Mail with attach
$mailer->setSubject('It test package landlib/simplemail - mail with inline attachment');
$mailer->setTextWithImages('Hello, my friend, {smile}!' . "\nI am a very satisfied person!", ['{smile}' => __DIR__ . '/smile.png']);
$r = $mailer->send();
var_dump($r);

```

## Usage on localhost linux ubuntu and gmail service

It for linux ubuntu Desktop users.

### Create gmail account and allow access for unsafe applications

On 03 01 2020 it possible on link https://myaccount.google.com/lesssecureapps

If link do not work, configure your ssmtp (see [Configure ssmtp](#instal-and-configure-ssmtp-server)) and try run example script app.php.

```bash
php app.php
```

You can see text like this:

```bash
ssmtp: Authorization failed (535 5.7.8  https://support.google.com/mail/?p=BadCredentials h7sm24406885lfj.29 - gsmtp)
/opt/lampp/htdocs/mh.loc/www/q/q/simplemail/example/app.php:31:
bool(false)

```

Goto link from message and see support page - it containts link to settings page, where you can set allow access unsafe applications.

### Instal and configure ssmtp server

It for linux ubuntu Desktop users.

```bash
sudo apt-get install ssmtp
```

Let your gmail address will `testshop@gmail.com`.

Open file `/etc/ssmtp/revaliases`

```bash
sudo gedit /etc/ssmtp/revaliases
```


Set string

```ini
root:testshop@gmail.com:smtp.gmail.com:587
```

Open file `/etc/ssmtp/ssmtp.conf`

```bash
sudo gedit /etc/ssmtp/ssmtp.conf
```

Set content

```ini
root=testshop@gmail.com
mailhub=smtp.gmail.com:587
hostname=smtp.gmail.com:587
UseSTARTTLS=YES
AuthUser=testshop@gmail.com
AuthPass=***** #your password must be here
FromLineOverride=YES
```

Open your php.ini file (I use XAMPP, my php.ini location is `/opt/lampp/etc/php.ini`)

and add (or replace) string

```ini
sendmail_path = /usr/sbin/ssmtp -t
```

Restart your apache server (I use XAMPP, therefore run `/opt/lampp/lampp restart`)

Run example script app.php (see [Create gmail account section](#create-gmail-account-and-allow-access-for-unsafe-applications))

# Ru

#### Содержание

[Что это](#что-это)

[Установка](#установка)

[Использование на продакшене](#использование-на-продакшене)

[Использование на локальном хосте Linux Ubuntu и службы Gmail](#использование-на-локальном-хосте-linux-ubuntu-и-службы-gmail)


## Что это

Это надежный и удобный класс для отправки электронной почты с поддержкой вложений с помощью функции PHP mail().
Я использую этот класс PHP последние десять лет и вижу, что он более надежен, чем инструменты электронной почты Swift_Mailer и Symfony Mailer.

Это звучит нескромно, но я напишу почему я так считаю.

В ноябре 2019 года я создал проект Symfony 3.4, он использует службу gmail на моем локальном хосте и почтовый ящик хостинг-провайдера на продакшене.

03 января 2020 года я обнаружил, что внезапно письма перестали отправляться с моего локального хоста (я использую учетную запись gmail и ssmtp). В логах Symfony 3.4 ошибок не было.

Я не редактировал свои скрипты Symfony.

Я попытался отправить письмо из проекта Symfony 5.0.2, но получил ошибку "Exception occurred while flushing email queue: Connection could not be established with host smtp.gmail.com :stream_socket_client(): SSL operation failed with code 1. OpenSSL Error messages: error:1416F086:SSL routines:tls_process_server_certificate:certificate verify failed".

Тогда я попытался отправить электронную почту, используя мой класс SimpleMail - и это было успешно.

Я много использовал этот скрипт раньше, на разных хостингах php. Это действительно работает (например, один из первых моих коммитов с этим классом здесь https://github.com/lamzin-andrey/gz.loc/blob/master/www/lib/classes/mail/SampleMail.php).

Поэтому я публикую этот класс PHP, я надеюсь, что он вам пригодится.

С Новым годом и Рождеством.

## Установка

`composer require landlib/simplemail`

или

`git clone https://github.com/lamzin-andrey/simplemail`

## Использование на продакшене

### Настройте почтовый ящик в ISP Manager на хостинг-провайдере

Создаqnt почтовый ящик с помощью интерфейса ISP Manager.
Убедитесь, что вы можете отправить письмо с него, используя RoundCude или другой интерфейс.

### Пример кода

```php
use Landlib\SimpleMail;

//Простой email
$sender = 'yoursendmailbox@yoursite.com';
$recipient = 'yourothermailbox@gmail.com';
$mailer = new SimpleMail();
$mailer->setSubject('It test package landlib/simplemail');
$mailer->setFrom($sender, 'Your name');
$mailer->setTo($recipient);
$mailer->setBody('Hello, my friend', 'text/html', 'UTF-8');
$r = $mailer->send();
var_dump($r);

//Письмо с вложением
$mailer->setSubject('It test package landlib/simplemail - mail with inline attachment');
$mailer->setTextWithImages('Hello, my friend, {smile}!' . "\nI am a very satisfied person!", ['{smile}' => __DIR__ . '/smile.png']);
$r = $mailer->send();
var_dump($r);

```

## Использование на локальном хосте Linux Ubuntu и службы Gmail

Это для пользователей Linux Ubuntu Desktop.

### Создать учетную запись Gmail и разрешить доступ для небезопасных приложений

На 03 01 2020 это возможно по ссылке https://myaccount.google.com/lesssecureapps

Если ссылка не работает, настройте ssmtp (см. [Настройка ssmtp] (#установить-и-настроить-сервер-ssmtp)) и попробуйте запустить пример сценария app.php из папки example.

```bash
php app.php
```

Вы можете увидеть текст похожий на этот:

```bash
ssmtp: Authorization failed (535 5.7.8  https://support.google.com/mail/?p=BadCredentials h7sm24406885lfj.29 - gsmtp)
/opt/lampp/htdocs/mh.loc/www/q/q/simplemail/example/app.php:31:
bool(false)

```

Переходите по ссылке из сообщения и смотрите страницу поддержки - она содержит ссылку на страницу настроек, где вы можете разрешить доступ небезопасных приложений.

### Установить и настроить сервер ssmtp

Это для пользователей Linux Ubuntu Desktop.


```bash
sudo apt-get install ssmtp
```

Например ваш адрес `testshop@gmail.com`.

Откройте файл `/etc/ssmtp/revaliases`

```bash
sudo gedit /etc/ssmtp/revaliases
```


Добавьте строку

```ini
root:testshop@gmail.com:smtp.gmail.com:587
```

Откройте файл `/etc/ssmtp/ssmtp.conf`

```bash
sudo gedit /etc/ssmtp/ssmtp.conf
```

Замените содержимое

```ini
root=testshop@gmail.com
mailhub=smtp.gmail.com:587
hostname=smtp.gmail.com:587
UseSTARTTLS=YES
AuthUser=testshop@gmail.com
AuthPass=***** #здесь должен быть ваш пароль, кавычки не нужны
FromLineOverride=YES
```

Откройте ваш php.ini file (Я использую XAMPP, мой php.ini находится `/opt/lampp/etc/php.ini`)

и добавьте или отредактируйте строку

```ini
sendmail_path = /usr/sbin/ssmtp -t
```

Рестартуйте apache (Я использую XAMPP, поэтому запускаю `sudo /opt/lampp/lampp restart`)

Запустите пример скрипта app.php (смотрите раздел [Создать учетную запись Gmail и разрешить доступ для небезопасных приложений](#создать-учетную-запись-gmail-и-разрешить-доступ-для-небезопасных-приложений))
