<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

class OpenApiFactory implements OpenApiFactoryInterface{

 public function __construct(private OpenApiFactoryInterface $decorated)
 {
 }
 public function __invoke(array $context = []): OpenApi {
     $openAPI = $this->decorated->__invoke($context);
    /*
     $schemas = $openAPI->getComponents()->getSecuritySchemes();
     $schemas['cookieAuth'] = new ArrayObject([
        'type' => 'apiKey',
        'in' => 'cookie',
        'name' => 'PHPSSEID'
     ]);
      $openAPI = $openAPI->withSecurity(['cookieAuth'=>[]]);*/
     return $openAPI;
 }
}