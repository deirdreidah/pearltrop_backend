<?php
require 'vendor/autoload.php';
$ref = new ReflectionMethod('Filament\Resources\Resource', 'form');
foreach ($ref->getParameters() as $param) {
    echo "Param: " . $param->getType()->getName() . "\n";
}
echo "Return: " . $ref->getReturnType()->getName() . "\n";
