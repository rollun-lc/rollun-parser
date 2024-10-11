<?php


namespace HttpClientTest\HttpClientMiddleware;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManager;
use HttpClient\HttpClientMiddleware\ResponseValidatorHttpClientMiddleware;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonZipCodeResponseValidator;
use PHPUnit\Framework\TestCase;
use HttpClient\HttpResponseValidator\StatusOk;
use Zend\Validator\ValidatorInterface;

/**
 * Class UserAgentHttpClientMiddlewareTest
 *
 * @package HttpClientTest\Middleware
 */
class ResponseValidatorHttpClientMiddlewareTest extends TestCase
{

    protected function getObject(ValidatorInterface $responseValidator)
    {
        return new ResponseValidatorHttpClientMiddleware(
            [$responseValidator]
        );
    }

    public function testCreate()
    {
        $responseValidator = new StatusOk();
        $object = $this->getObject($responseValidator);
        $this->assertInstanceOf(ResponseValidatorHttpClientMiddleware::class, $object);
    }

    public function testCreateFromPluginManager()
    {
        global $container;
        $pluginManager = $container->get(HttpClientMiddlewarePluginManager::class);
        $object = $pluginManager->getHttpClientMiddlewareFactoryFunction('TestAmazonResponseValidatorHttpClientMiddleware');
        $handlerStack = new HandlerStack(new MockHandler([new Response(200, [], '')]));
        $handlerStack->push($object);

        $httpClient = new Client(['handler' => $handlerStack]);
        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $promise = $httpClient->sendAsync($request, []);
        $validator = null;
        try {
            $response = $promise->wait();
        } catch (\Exception $e) {
            $handlerContext = $e->getHandlerContext();
            $validator = $handlerContext[ResponseValidatorHttpClientMiddleware::KEY_VALIDATOR];
        }

        $this->assertEquals(AmazonZipCodeResponseValidator::class, $validator);
    }

    public function testWithValidResponse()
    {
        $responseValidator = new StatusOk();
        $object = $this->getObject($responseValidator);
        $object->setHandler(new MockHandler([new Response(200)]));

        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $httpClient = new Client(['handler' => $object]);

        $resp = $httpClient->send($request, []);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    public function testWithBadResponse()
    {
        $responseValidator = new StatusOk();
        $object = $this->getObject($responseValidator);
        $object->setHandler(new MockHandler([new Response(400)]));

        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $httpClient = new Client(['handler' => $object]);

        $this->expectException(Exception::class);
        $resp = $httpClient->send($request, []);
    }

    public function testWithGoodBodyAndBadStatusCode()
    {
        $responseValidator = new GoodBodyBadStatusCodeResponseValidator();
        $object = $this->getObject($responseValidator);
        $object->setHandler(new MockHandler([new Response(400, [], 'good')]));

        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $httpClient = new Client(['handler' => $object]);

        $resp = $httpClient->send($request, []);
        $this->assertEquals(400, $resp->getStatusCode());
    }

    /**
     * Тест показывает что Middleware может вернуть fullfilled Promise
     * даже после того как предыдущий Middleware выбросил Exception
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testWithGoodBodyAndBadStatusCodeWithMiddleware()
    {
        $responseValidator = new GoodBodyBadStatusCodeResponseValidator();
        $object = $this->getObject($responseValidator);
        /**
         * Middleware Middleware::httpErrors() выбросит Exception
         * Middleware GoodBodyBadStatusCodeResponseValidator вернет Response
         */
        $object->setHandler(MockHandler::createWithMiddleware([new Response(400, [], 'good')]));

        $request = new ServerRequest('GET', 'https://www.google.com', []);
        $httpClient = new Client(['handler' => $object]);

        $promise = $httpClient->sendAsync($request, []);
        $response = $promise->wait();
        $this->assertEquals(400, $response->getStatusCode());
    }
}
