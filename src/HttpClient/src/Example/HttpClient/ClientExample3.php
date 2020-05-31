<?php


namespace HttpClient\Example\HttpClient;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManager;
use function GuzzleHttp\choose_handler;

/**
 * Class ClientExample1
 *
 * @package HttpClient\Example\HttpClient
 */
class ClientExample3
{
    /**
     * @param $serviseName
     *
     * @return mixed
     */
    public function createMiddlewareFactoryFunctionWithPluginManager($serviseName)
    {
        global $container;
        $httpClientMiddlewarePluginManager = $container->get(HttpClientMiddlewarePluginManager::class);
        /**
         * service serviceStatusOkResponseValidatorHttpMiddleware described at
         * config/autoload/development.handlerstack.test.global.php
         */
        $middlewareFactoryFunction = $httpClientMiddlewarePluginManager->getHttpClientMiddlewareFactoryFunction(
            $serviseName
        );
        return $middlewareFactoryFunction;
    }


    /**
     *  returns HttpClient with middleware `serviceStatusOkResponseValidatorHttpMiddleware` and MockHandler
     *
     * @return Client
     * @see test/HttpClientTest/Example/HttpClient/ClientExample3Test.php
     */
    public function createClientWithResponseValidatorHandler(): Client
    {
        $serviseName = 'serviceStatusOkResponseValidatorHttpMiddleware';
        $middlewareFactoryFunction = $this->createMiddlewareFactoryFunctionWithPluginManager($serviseName);

        $handler = new MockHandler([new Response(500)]);
        $handlerStack = new HandlerStack($handler);
        $handlerStack->push($middlewareFactoryFunction);
        return new Client(['handler' => $handlerStack]);
    }

    /**
     * returns HttpClient with middleware `serviceRollunComResponseValidatorHttpMiddleware`
     * and curl_handler
     *
     * @return Client
     * @see test/HttpClientTest/Example/HttpClient/ClientExample3Test.php
     */
    public function createClientWithRollunResponseValidatorHandler(): Client
    {
        $serviseName = 'serviceRollunComResponseValidatorHttpMiddleware';
        $middlewareFactoryFunction = $this->createMiddlewareFactoryFunctionWithPluginManager($serviseName);

        $response1 = new Response(200, [], '');
        $response2 = new Response(404, [], '<!DOCTYPE html>
            <head></head>
            <body class="error404 lightbox nav-dropdown-has-arrow">
            <a class="skip-link screen-reader-text" href="#main">Skip to content</a>
            <div id="wrapper">
            <main id="main" class="">
                <div id="primary" class="content-area">
                    <main id="main" class="site-main container pt" role="main">
                        <section class="error-404 not-found mt mb">
                            <div class="row">
                                <div class="col medium-3"><span class="header-font" style="font-size: 6em; font-weight: bold; opacity: .3">404</span></div>
                                <div class="col medium-9">
                                    <header class="page-title">
                                        <h1 class="page-title">Oops! That page can&rsquo;t be found.</h1>
                                    </header><!-- .page-title -->
                                </div>
                            </div><!-- .row -->
                        </section><!-- .error-404 -->
                    </main><!-- #main -->
                </div><!-- #primary -->
            </main><!-- #main -->
            </body>
            </html>
            ');

        $handler = new MockHandler([
            $response1,
            $response2,
        ]);
        $handlerStack = new HandlerStack($handler);
        $handlerStack->push($middlewareFactoryFunction);
        return new Client(['handler' => $handlerStack]);
    }


}