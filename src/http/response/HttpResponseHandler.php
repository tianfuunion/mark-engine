<?php
    declare (strict_types=1);

    namespace mark\http\response;

    abstract class HttpResponseHandler implements HttpResponse
    {
        public function Handler(int $code, array $headers, string $response)
        {
            if ($code == 200) {
                $this->onSuccess($code, $headers, $response);
            } else {
                $this->onFailure($code, $headers, $response);
            }
        }

        public abstract function onSuccess(int $code, array $headers, $response): void;

        public abstract function onFailure(int $code, array $headers, $response): void;
    }