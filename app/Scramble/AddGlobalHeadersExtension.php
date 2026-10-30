<?php

namespace App\Scramble;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\RouteInfo;

class AddGlobalHeadersExtension extends OperationExtension
{
    public function handle(Operation $operation, RouteInfo $routeInfo)
    {
        $operation->addParameters([
            Parameter::make('Accept-Language', 'header')
                ->description("select the language for the response.")
                ->setSchema(Schema::fromType(new StringType))
                ->example('ar')
                ->example('en'),
                

            Parameter::make('Accept', 'header')
                ->description("specifies the media type of the response.")
                ->setSchema(Schema::fromType(new StringType))
                ->example('application/json'),
        ]);
    }
}