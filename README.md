# ELEC

## What the heck is this?
elec is a TUI (Text User Interface) you can SSH to that shows you nearby World Heritage Sites.

## Use
Just SSH to elec.my

```
ssh elec.my
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
- How do we neatly test TUI's? They run in a loop, output to the screen, wait for input, etc.. :thinking:
- `csv-to-sqlite` isn't good enough - doesn't handle types, no primary key or indexes. But, 90 minutes isn't a lot of time
