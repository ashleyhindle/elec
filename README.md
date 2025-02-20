# ELEC

## What the heck is this?
elec is a TUI (Text User Interface) you can SSH to that shows you nearby World Heritage Sites.

## Usage
Just SSH to elec.my

```
ssh elec.my
```

## Local usage & development

### Requirements
You'll need:
- PHP 8.3
- [Composer](https://getcomposer.org/)

The fastest way to get setup with PHP is to use [php.new](https://php.new/).

### Setup

**Composer**
```
cd elec
cp .env.example .env
composer install
```

**.env**
You'll need an API key from [ipgeolocation.io](https://ipgeolocation.io/) to run the app locally.

Update .env to include your API key.
```
IPGEOLOCATION_API_KEY=your_api_key
```

### Usage
You can run `src/index.php` directly to test the app locally.

```
php src/index.php 86.2.94.106
```

You can also run it through Docker and SSH locally.

```
composer run dev
ssh localhost -p2201
```

### Running tests

```
composer run test
```


## How it works
- DigitalOcean Droplet running Ubuntu & Docker
- Docker container runs Go based SSH server
- On a new connection Go spawns the src/index.php script in a new PHP process and pipes STDIN/STDOUT to the SSH connection

## Notes
- Possibly a bad example from a code PoV, as most of the work is around infrastructure, and I didn't have time to figure out tests
- Great example of product/interesting thinking though? :)
- Required fork & tiny amount of go coding to get SSH IP passed into PHP - https://github.com/ashleyhindle/ssh-php-docker
- Added `.env` for secrets & `vlucas/phpdotenv` to read them

## Future
- How do we add better tests for this TUI? It runs in a loop, outputs to the screen, waits for input, etc.. :thinking:
- `csv-to-sqlite` isn't good enough - doesn't handle types, no primary key or indexes. But, 90 minutes isn't a lot of time
