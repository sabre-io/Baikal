# Baikal & Docker

To dockerize Baikal you have two possibilities :

## 1 - Build your own image :
Clone Baikal go to the Docker folder and execute :

`docker build -t mySuperImageName .`

When done, run the images by doing :

`docker run -ti -p 8000:80 mySuperImageName`

Or start it as a daemon with : 

`docker run -d -p 8000:80 mySuperImageName`

Then go to [YourDockerIP:8000]()

## 2 - Pull existing image :
Pull the image with:

`docker pull p1rox/baikal`

And then run it with :

`docker run -ti -p 8000:80 p1rox/baikal`

Or start it as a daemon with : 

`docker run -d -p 8000:80 p1rox/baikal`

Then go to [YourDockerIP:8000]()