# About

PHP application which sanitizes anime title from dirty filenames.
For example, input `[Subsplease] Shingeki no Kyojin - 01 (1080P) [3E953c31].mkv` will be converted
to `Attack on Titan E01.mkv`.

```bash
curl -X 'GET' \
  'http://127.0.0.1:8090/api/v1/anime/filename?rawName=%5BSubsplease%5D%20Shingeki%20no%20Kyojin%20-%2001%20%281080P%29%20%5B3E953c31%5D.mkv&type=path' \
  -H 'accept: application/json'
```

## Docs

```bash
http://127.0.0.1:8090/api/doc
```

## Docker-compose

```bash
    media_data:
        image: ghcr.io/solitus0/media_data:main
        container_name: media_data
        restart: unless-stopped
        ports:
            - "8090:80"
```
