<?php
return [
  'paths' => ['api/*', 'sanctum/csrf-cookie'],
  'allowed_methods' => ['*'],
  'allowed_origins' => ['*'], // tighten in prod
  'allowed_headers' => ['*'],
  'supports_credentials' => false,
];
