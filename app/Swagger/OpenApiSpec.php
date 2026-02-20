<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: "1.0.0",
        title: "LKHDATAR API",
        description: "API documentation for LKHDATAR project"
    ),
    servers: [
        new OA\Server(
            url: "http://localhost",
            description: "Local Development Server"
        )
    ]
)]
class OpenApiSpec
{
}