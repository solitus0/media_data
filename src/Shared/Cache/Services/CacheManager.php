<?php

declare(strict_types=1);

namespace App\Shared\Cache\Services;

use App\Shared\Cache\Enum\CacheTTL;
use Symfony\Contracts\Cache\CacheInterface;

class CacheManager
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {}

    public function get(string $key): mixed
    {
        $item = $this->cache->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }

        return null;
    }

    public function save(string $key, mixed $data, CacheTTL $ttl): void
    {
        $ttlValue = $ttl->value;
        $item = $this->cache->getItem($key);
        $item->set($data);
        if ($ttlValue > 0) {
            $item->expiresAfter($ttlValue);
        }

        $this->cache->save($item);
    }
}
