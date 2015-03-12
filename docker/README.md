# Baikal & Docker

## Build the image :
Clone Baikal go to the Docker folder and execute :

`docker build -t mySuperImageName .`

When done, run the images by doing :

`docker run -ti -p 8000:80 mySuperImageName`

Or start it as a daemon with : 

`docker run -d -p 8000:80 mySuperImageName`

Then go to [YourDockerIP:8000]()