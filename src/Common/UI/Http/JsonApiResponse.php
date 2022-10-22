<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\UI\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonApiResponse extends JsonResponse
{
    /**
     * JsonApiResponse constructor.
     * @param mixed|null $data
     * @param int $status
     * @param array $headers
     * @param bool $json
     */
    private function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
    }

    /**
     * @param mixed|null $data
     * @return static
     */
    public static function success($data = null): self
    {
        return new self(
            [
                'success' => true,
                'data' => $data,
                'error' => null,
            ]
        );
    }

    /**
     * @param mixed|null $error
     * @param int $status
     * @return static
     */
    public static function fail($error = null, int $status = 400): self
    {
        return new self(
            [
                'success' => false,
                'data' => null,
                'error' => $error,
            ],
            $status
        );
    }
}
