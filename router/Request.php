<?php

final class Request
{

    private array $body = array();

    public function __construct(
        private readonly string $uri,
        private readonly string $method,
        private readonly array $queryParams
    )
    {
        $body = file_get_contents('php://input');
        if ($body && str_starts_with($body, '{')) {
            $body = new Json($body);
            $this->body['json'] = $body;
            $this->body['obj'] = $body->getJsonArray();
            return;
        }
        $this->body['json'] = new Json('{}');
        $this->body['obj'] = array();
    }

    public static function createFromGlobal(): self
    {
        /** Add # to start of the string to replace only beginning of the URI */
        $scriptName = '#' . $_SERVER['SCRIPT_NAME'];
        $requestUri = '#' . $_SERVER['REQUEST_URI'];
        $baseDir = dirname($scriptName);

        $queryString = $_SERVER['QUERY_STRING'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $queryParams = [];

        parse_str($queryString, $queryParams);

        $url = '/' . str_replace([$scriptName, $baseDir, '?' . $queryString], '', $requestUri);
        $url = str_replace('//', '/', $url);


        return new Request($url, $requestMethod, $queryParams);
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        if (isset($this->queryParams[$name])) {
            return $this->queryParams[$name];
        }
        if (filter_has_var(INPUT_POST, $name)) {
            return filter_input(INPUT_POST, $name);
        }
        return $default;
    }

    public function getBody(): array
    {
        return $this->body;
    }

}