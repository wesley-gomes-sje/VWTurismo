<?php

namespace App\Controller\Api;

use OpenApi\Generator;

class DocsController
{
    /** GET /api/openapi.json — especificação OpenAPI em JSON */
    public function spec(): void
    {
        $openapi = (new Generator())->generate([__DIR__]);

        header('Content-Type: application/json');
        echo $openapi->toJson();
    }

    /** GET /api/docs — Swagger UI (HTML) */
    public function ui(): void
    {
        header('Content-Type: text/html; charset=UTF-8');
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>VWTurismo API — Documentação</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css">
        </head>
        <body>
            <div id="swagger-ui"></div>
            <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
            <script>
                SwaggerUIBundle({
                    url: '/api/openapi.json',
                    dom_id: '#swagger-ui',
                    presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
                    layout: 'BaseLayout',
                    deepLinking: true,
                });
            </script>
        </body>
        </html>
        HTML;
    }
}
