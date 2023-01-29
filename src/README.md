<p align="center"><img src="#" width="400"></p>

## About Despos'It API

### Development Installation 

#### <ins>1. Requirements</ins>

This application is dockerized. You will need to install [Docker Desktop](https://www.docker.com/products/docker-desktop/) in order to start using it. Windows, Linux and MacOS are supported.
> **⚠️ Windows might ask you to activate [Hyper-V functionnality](https://learn.microsoft.com/en-us/virtualization/hyper-v-on-windows/quick-start/enable-hyper-v).**

#### <ins>2. Clone and build</ins>

Clone the repository to your local machine.
```shell
> git clone git@github.com:SalmonDeposit/deposit_api.git
```
Next, go to your installation directory, and build the application.
```shell
> cd deposit_api
> docker-compose up
```

#### <ins>3. Database setup</ins>

To generate fake data, you will **first need to create the database schema**. Go into `deposit_api_app_1`'s container and type the following command.
```bash
> php artisan migrate:fresh
```
Then, seed the database :
```bash
> php artisan db:seed
```
**The api should now be ready to use.**
### Generating IDE Helpers
