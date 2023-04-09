enter:
	docker exec -it media_data zsh

run_be:
	cd ./docker && docker-compose up -d media_data --remove-orphans

stop_all:
	docker stop `(docker ps -q)`
	pkill -f "symfony serve"
