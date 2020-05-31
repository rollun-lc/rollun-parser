<?php


namespace HttpClient\LoaderPluginManager;

use Psr\Http\Message\RequestInterface;

interface LoaderInterface
{
    public function __invoke(RequestInterface $request): ?string;
}
