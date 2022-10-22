<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\UI\Http;

class JsonApiResponseGenerator
{
    /**
     * @param mixed|null $data
     * @return JsonApiResponse
     */
    public function success($data = null): JsonApiResponse
    {
        return JsonApiResponse::success($data);
    }

    /**
     * @param mixed|null $error
     * @param int $status
     * @return JsonApiResponse
     */
    public function fail($error = null, int $status = 400): JsonApiResponse
    {
        return JsonApiResponse::fail($error);
    }
}
