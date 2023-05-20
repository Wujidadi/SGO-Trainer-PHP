<?php

namespace App\Services\SGO;

use App\Constants\JsonFlag;
use App\Exceptions\GetPlayerException;
use App\Services\Player\PlayerService;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\HandlerStack as HttpHandlerStack;
use GuzzleHttp\Middleware as HttpMiddleware;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wujidadi\LogFacade\LogFormatter;

class Client
{
    private string $originHost = 'https://swordgale.online';
    private string $apiHost = 'https://api.swordgale.online';
    private string $playerName;
    private ?string $token;
    private array $headers;
    private array $options;

    /**
     * @throws GetPlayerException
     */
    public function __construct(string $playerName)
    {
        $this->playerName = $playerName;
        $this->getToken();
        $this->headers = [
            'token' => $this->token,
            'user-agent' => config('app.user_agent'),
            'origin' => $this->originHost,
            'referer' => "$this->originHost/",
        ];
        $this->options = [
            RequestOptions::VERIFY => false,
            RequestOptions::CONNECT_TIMEOUT => 5,
            RequestOptions::TIMEOUT => 10,
            RequestOptions::COOKIES => resolve(CookieJar::class),
            'handler' => $this->createHttpHandlerStack(),
        ];
    }

    private function createHttpHandlerStack(): HttpHandlerStack
    {
        $stack = HttpHandlerStack::create();
        if (config('logging.channels.sgo.by_middleware', true)) {
            $middleware = HttpMiddleware::log(Log::channel('sgo'), new LogFormatter());
            $stack->push($middleware, 'log');
        }
        return $stack;
    }

    /**
     * @throws GetPlayerException
     */
    private function getToken(): void
    {
        if (empty($this->token = PlayerService::getToken($this->playerName))) {
            throw new GetPlayerException('Token is empty');
        }
    }

    public function get(string $url, array $headers = []): object|string
    {
        $url = $this->apiHost . $url;
        $this->headers = array_merge($this->headers, $headers);
        $response = Http::retry(5, 1000)
            ->withHeaders($this->headers)
            ->withOptions($this->options)
            ->async()
            ->get($url)
            ->wait();
        return $this->parseResponseBody($response->getBody());
    }

    public function post(string $url, array|object $body = [], array $headers = []): object|string
    {
        $url = $this->apiHost . $url;
        $this->headers['content-type'] = 'application/json; charset=UTF-8';
        $this->headers = array_merge($this->headers, $headers);
        $response = Http::retry(5, 1000)
            ->withBody(json_encode($body, JsonFlag::UNESCAPED))
            ->withHeaders($this->headers)
            ->withOptions($this->options)
            ->async()
            ->post($url)
            ->wait();
        return $this->parseResponseBody($response->getBody()->getContents());
    }

    private function parseResponseBody(string $response): object|string
    {
        return json_decode($response) ?? $response;
    }
}
