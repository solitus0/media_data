# About
PHP application which sanitizes anime filenames.

## Docker

```bash
docker build -t sol0/animefilename ./docker/php
docker run -p 8090:8090 sol0/animefilename
```

## Docs

```bash
http://127.0.0.1/api/doc
```

## Usage

```bash
curl -X 'GET' \
  'http://127.0.0.1/api/v1/anime/filename?rawName=%5BSubsplease%5D%20Akuyaku%20Reijou%20Nanode%20Last%20Boss%20Wo%20Kattemimashita%20-%2001%20%281080P%29%20%5B3E953c31%5D.mkv' \
  -H 'accept: application/json'
```
