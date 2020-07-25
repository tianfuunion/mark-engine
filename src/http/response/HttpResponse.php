<?php
    declare (strict_types=1);

    namespace mark\http\response;
    /**
     * 自定义响应接口
     * Interface HttpResponse
     * @package mark\http\response
     */
    interface HttpResponse
    {
        function onStart($field): void;

        function onProcess(): void;

        function onSuccess(int $code, array $headers, $response): void;

        function onFailure(int $code, array $headers, $response): void;

        function onFinish(): void;
    }