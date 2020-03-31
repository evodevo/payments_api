<?php

declare(strict_types=1);

namespace PaymentsAPI\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Driver\DriverInterface;
use Behat\MinkExtension\Context\RawMinkContext;
use PaymentsAPI\Application\Command\ProcessTransactionsHandler;
use Symfony\Component\BrowserKit\Client;

/**
 * Class PaymentsApiContext
 * @package PaymentsAPI\Tests\Behat
 */
class PaymentsApiContext extends RawMinkContext implements Context
{
    /**
     * @var ProcessTransactionsHandler
     */
    private $processTransactionsHandler;

    /**
     * PaymentsApiContext constructor.
     * @param ProcessTransactionsHandler $processTransactionsHandler
     */
    public function __construct(ProcessTransactionsHandler $processTransactionsHandler)
    {
        $this->processTransactionsHandler = $processTransactionsHandler;
    }

    /**
     * Make request specifying http method and uri and parameters as JSON.
     *
     * @When I make :method json request to :uri with content:
     *
     * @param $method
     * @param $uri
     * @param PyStringNode $json
     */
    public function iMakeJSONRequestWithContent($method, $uri, PyStringNode $json)
    {
        $this->request($method, $uri, [],[], (string)$json);
    }

    /**
     * @Given the system processed confirmed transactions
     */
    public function theSystemProcessedConfirmedTransactions()
    {
        $this->processTransactionsHandler->handle(null);
    }

    /**
     * @When I set :headerName header to :headerValue
     *
     * @param $name
     * @param $value
     */
    public function iSetHeader($name, $value)
    {
        $nonPrefixed = array('CONTENT_TYPE');

        $headerName = strtoupper(str_replace('-', '_', $name));
        $headerName = in_array($headerName, $nonPrefixed) ? $headerName : 'HTTP_'.$headerName;

        $this->getClient()->setServerParameter($headerName, $value);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $params
     * @param array $headers
     * @param null $content
     */
    protected function request($method, $uri, array $params = [], array $headers = [], $content = null)
    {
        $server = $this->createServerArray($headers);
        $this->getClient()->request($method, $this->locatePath($uri), $params, [], $server, $content);
    }

    /**
     * @param array $headers
     * @return array
     */
    protected function createServerArray(array $headers = []): array
    {
        $server = [];
        $nonPrefixed = ['CONTENT_TYPE'];
        foreach ($headers as $name => $value) {
            $headerName = strtoupper(str_replace('-', '_', $name));
            $headerName = in_array($headerName, $nonPrefixed) ? $headerName : 'HTTP_'.$headerName;
            $server[$headerName] = $value;
        }
        return $server;
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        /** @var DriverInterface $driver */
        $driver = $this->getSession()->getDriver();

        return $driver->getClient();
    }
}