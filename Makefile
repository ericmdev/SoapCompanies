# Build the image.
build:
	docker build -t soapcompanies-image -f Dockerfile .

# Stop and remove the container.
clean:
	docker stop soapcompanies-container
	docker rm soapcompanies-container

# Remove the image.
clean-image:
	docker rm soapcompanies-image

# Show all containers.
containers:
	docker ps -a

# Dig into the container.
exec:
	docker exec -it soapcompanies-container bash

# Show ip of default machine.
ip:
	docker-machine ip default

# List machines.
machines:
	docker-machine ls

# Run the container.
run:
	docker run -d -p 45170:22 -p 45171:80 -p 45172:443 -p 45173:3306 --name soapcompanies-container \
							-v `pwd`/webapp/bin:/srv/www/webapp/bin/ \
							-v `pwd`/webapp/app:/srv/www/webapp/app/ \
							-v `pwd`/webapp/var/bootstrap.php.cache:/srv/www/webapp/var/bootstrap.php.cache \
							-v `pwd`/webapp/var/SymfonyRequirements.php:/srv/www/webapp/var/SymfonyRequirements.php \
							-v `pwd`/webapp/src:/srv/www/webapp/src/ \
							-v `pwd`/webapp/web:/srv/www/webapp/web/ \
							-v `pwd`/webapp/vendor:/srv/www/webapp/vendor/ \
							-v `pwd`/provision/mnt/var/log/nginx:/var/log/nginx/ \
							soapcompanies-image
	docker exec -it soapcompanies-container bash

# Stop the container.
stop:
	docker stop soapcompanies-container