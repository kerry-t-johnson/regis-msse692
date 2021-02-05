# Containers and Docker

## Definitions

* A **container** is an isolated unit of executing software that runs quickly and reliably from one computing environment to another (adapted from https://www.docker.com/resources/what-container)

* An **image** is a lightweight, standalone, executable package of software that includes everything needed to run an application: code, runtime, system tools, system libraries and settings (https://www.docker.com/resources/what-container).

## Containers vs VMs

|Containers       | VMs                                                |
|-----------------|----------------------------------------------------|
|![](https://www.docker.com/sites/default/files/d8/2018-11/docker-containerized-appliction-blue-border_2.png)        | ![](https://www.docker.com/sites/default/files/d8/2018-11/container-vm-whatcontainer_2.png) |
|* Shared kernel  | * Entire guest OS, including init system           |
|* Immutable      | * Installation of (e.g.) App A changes state of VM |



## Container Details

### **File System Isolation**
Each container has its own view of the file system along with a thin R/W layer to which file system writes are directed:

| Layer View | File System View |
|------------|------------------|
|![File System Isolation](https://docs.docker.com/storage/storagedriver/images/container-layers.jpg)|![](https://www.oreilly.com/library/view/docker-and-kubernetes/9781786468390/assets/479c53d0-6c64-4e91-b460-d0c44270f2a7.jpg)|
|*https://docs.docker.com/storage/storagedriver/images/container-layers.jpg*|https://www.oreilly.com/library/view/docker-and-kubernetes/9781786468390/assets/479c53d0-6c64-4e91-b460-d0c44270f2a7.jpg|


This provides a solution for the *conflicting dependency* form of [Dependency Hell](https://en.wikipedia.org/wiki/Dependency_hell)!


### **Network Isolation**

Each container has its own view of the network, including (optionally) its own hostname, an isolated virtual network for talking to other containers, etc.

![Network Isolation](https://miro.medium.com/max/2310/1*3dH8hl3ovZj5H9zuHShAXQ.png)

### **Process Isolation**

For Linux containers, process numbering starts with PID 1.  Each container is entirely unaware of processes running in other containers on the same host.

![Process Isolation](https://www.docker.com/sites/default/files/d8/2018-11/docker-containerized-appliction-blue-border_2.png)
*https://www.docker.com/sites/default/files/d8/2018-11/docker-containerized-appliction-blue-border_2.png*

### User Isolation

Each container may have its own local users, e.g. node, centos, mysql, etc.

## Image Details

### Standalone

This example Dockerfile is adapted from https://docs.docker.com/engine/examples/postgresql_service:

```Dockerfile
FROM ubuntu:16.04

# ... snip ...

# KTJ: This Dockerfile installs the PostgreSQL dependencies such that the
# resulting image always has a working copy:
RUN apt-get update                                  &&  \
    apt-get install -y  python-software-properties      \
                        software-properties-common      \
                        postgresql-9.3                  \
                        postgresql-client-9.3           \
                        postgresql-contrib-9.3

# ... snip ...

# Expose the PostgreSQL port
# KTJ: Note that this is optional... any ports may be 'exposed' at runtime
EXPOSE 5432

# Add VOLUMEs to allow backup of config, logs and databases
# KTJ: Volumes are used to persiste data outside of the container
VOLUME  ["/etc/postgresql", "/var/log/postgresql", "/var/lib/postgresql"]

# Set the default command to run when starting the container
CMD ["/usr/lib/postgresql/9.3/bin/postgres", "-D", "/var/lib/postgresql/9.3/main", "-c", "config_file=/etc/postgresql/9.3/main/postgresql.conf"]
```

### Immutable

* Each *layer* is identified by a cryptographic hash, i.e. digest, of its contents
* Each *image* is an ordered collection of layers, also identified by a hash
* Any modification to an image results in a new hash
* Modifications are stored in an image's history

### Extensible

An image can be used as the base image for another image

## Vendor Details

### Docker on your machine actually consists of a CLI and a daemon/service (Docker Engine).

* When building an image, the Docker "context" is sent to the daemon and the daemon does the build
* ==> The single daemon is a possible point of conflict, if two individual attempt to build/tag the same image
* When running a container, the daemon actually spawns the process
* ==> Since the daemon/service runs as a privileged user, it is possible to escalate privileges when running Docker containers.

Open Container Initiative (OCI) - https://opencontainers.org

docker build <==> podman build (no engine, executed by the user!)


# Show and Tell

### Run the ubuntu image (demonstrate that same kernel is used)
```bash
wsl$ uname -r        # Shows microsoft kernel
wsl$ docker run --rm -it ubuntu:20.04 bash
root@xyz: uname -r   # Shows microsoft kernel
```

### Demonstrate immutability
root@xyz: touch /var/tmp/foo 


