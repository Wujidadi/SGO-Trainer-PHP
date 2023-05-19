# SGO PHP Trainer

This is a simple PHP trainer for the excellent Web MUD [Sword Gale Online](https://swordgale.online/).

## Notice

DO NOT start the container directly via `docker-compose.yml`!

To pre-build the necessary files and directories before starting the container, please run `./build` first (located at the root of this project):
```sh
./build
./build -f # Force recreate
./build -d # Down all the containers
```

`DOCKER_VOLUME_PATH` (the volume path for the database, cache, and Zsh history files) is located under `/opt/docker`. If the directory does not exist, you should the steps below to run the containers:
1. Create `/opt/docker`. `sudo` or root permission is required, and `chown` may be required, too.
2. If you are using macOS, share `/opt/docker` to Docker: Docker Desktop -> Settings -> Resources -> File Sharing -> Add `/opt/docker`.
