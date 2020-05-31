# HttpClient Documentation

## HttpClientMiddleware
HttpClientMiddleware - это объекты для которые могут выполнять некоторые дейтствия 
(например, изменить запрос, добавить прокси) при отправке запроса на серевер.
Каждый HttpClientMiddleware должен в конце своей работы выполнить следующий HttpClientMiddleware.
Его можно установить методом `HttpClientMiddlewareInterface::setHandler(callable $nextHandler)`.

HttpClientMiddleware имплементирует интерфейс 
`HttpClient\HttpClientMiddleware\HttpClientMiddlewareInterface`.

Каждый HttpClientMiddleware возвращает Promise, которому можно передать функции которые 
выполнятся в случае успешного или неуспешного результата:

```php
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $request = $request->withHeader('test_header', 'test_value');
        /**
         * @var PromiseInterface $promise
         */
        $promise = $this->nextHandler->__invoke($request, $options);
        return $promise->then(
            function (ResponseInterface $response) {
                $this->logger->debug('Log Message with successful result');
                return $response->withHeader('test_header', $log);
            },
            function ($reason) {
                $this->logger->debug('Log Message with exception');
                return rejection_for($reason);
            },
        );
    }
```

Функция `onFullfilled` принимает на вход `ResponseInterface` и может вернуть объект `ResponseInterface`
в случае успеха и `RejectedPromise` или выбросить исключение в случае если результат неуспешный


Так как `GuzzleHttp\HadlerStack` в качестве параметра функции `push()` принимает не сам Middleware, а функцию, которая 
принимает на вход `nextHandler` (следующий Middleware), то Middleware лучше создавать через 
`HttpClientMiddlewarePluginManager::getHttpClientMiddlewareFactoryFunction($middlewareServiceName)`. При этом сервисы 
Middleware должны быть зарегистрированы в конфиге HttpClientMiddlewarePluginManager.

Объекты класса `HttpClientMiddleware` могут передавать информацию друг другу через свойство 
`attributes` объекта `ServerRequest` (в прямом направлении и в обратном в случае fullfilled promise),
 или через объект  `RequestException` (в обратном направлении в случае rejected promise).
 
### ResponseValidatorHttpClientMiddleware

Middleware, который с помощью переданных ему валидаторов выбрасывает `BadResponseException` 
с информацией про результат валидации. 
`ResponseValidatorHttpClientMiddleware` нужно ставить в стеке хендлеров
сразу перед curl_handler, чтобы он мог первым навесить свои обработчики на Promise и 
после того как вернется Response, в случае неудачи передать rejected promise остальным Middleware.

Если `ResponseValidatorHttpClientMiddleware` обнаруживает ошибку в Response - он 
генерирует объект `BadResponseException` передавая ему в конструкторе:
- $message = `$message`, // сообщение валидатора
- RequestInterface $request = `$this->request`, // текущий request
- ResponseInterface $response = `$response`, // текущий response
- \Exception $previous = null,
- array $handlerContext = `['validator' => 'ValidatorClass']` // под ключом `validator` 
    передается класс валидатора который вернул false. Следущие Middleware могут 
    использовать эту информацию для принятия решений
 
 
 
 

