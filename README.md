# Homepage Brettspielverein Zofingen
## Setting up dev env
### Required tools
- docker

### Run the application locally
The project contains a script that does all of the tasks you need to do, `./bvz`. To get started with the project, first run `./bvz setup`. This will create the required docker images, setup the local DB in a container with volume for persistence and prepare the backend; this involves downloading the required dependencies and prepare the .env file.

To run the website in dev mode, simply run `./bvz dev` to start the frontend, DB and backend in docker containers. The code is mounted from your local file system, so changes are directly reflected in the page without any rebuild required.

The website will be available on `localhost:8000`, the backend on `localhost:80` and the mail server GUI is available on `localhost:3000`. The DB can be connected to with any data base viewer under port 3306 with user `root` and password `example`.

By calling `./bvz stop` you can stop all running dev containers.

### Running liquibase
[Liquibase](https://docs.liquibase.com/) is used to handle database updates. To work with it locally, you can use `./bvz`. `./bvz migrateDevDb` will run the migrate with the current changelog as defined in `db/liquibase/changelog` first build the docker image with the build script in `docker\liquibase`. Then, use the provided scripts in `db/liquibase`. 

To run against the PROD DB, use `./bvz -p <password> migrateLiquibaseProd`. It expects a `liquibase.prod.properties`. Copy and rename the template file and fill it in with the correct values. To connect sucessfully, you also need to add your host to the Remote DB configuration on cPanel (get it from myip.com). 

There also is a github workflow which can be set up appropriately. It needs to run on a custom runner with a known host, so that said host can be configured to be allowed on the host.
## Release the website
### Test the release
By running `./bvz inttest`, the application is run in a single php server. The frontend is compiled to static pages, the backend copied to an approrpriate location. It is still running against the local Dev DB and mail server, but it's useful to check that everything works after static generation.

### Build the release
To build the release, simply run `./bvz buildDist` script in the root folder. This will update the dependencies as required and zip everything up into a zip in the `dist` directory.

### Push the release to the host
#### Github action
There is a github workflow which uses FTP to push the installed data to the server from the github build. For that, the FTP user, password and URL need to be configured as secrets, and then the build and installation can be triggered manually.

#### Manually (not recommended!)
**Make sure to not delete the .env file during the installation process!**

To "install" the website on the host, simply upload the created zip, and unzip it into the folder to which the URL is mapped. Then, create or update an existing `.env` file in the newly created folder based on the `.env.example` file, and fill in the appropriate values. That `.env` file should already be there when simply updating the page, so make sure to not delete it.
