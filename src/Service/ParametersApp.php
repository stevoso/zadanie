<?php
namespace App\Service;

class ParametersApp {

    public function __construct(
        public string $pathRoot,
        public string $databaseUrl,
    ){ }

}
