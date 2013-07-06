<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Homer;

use React\Http\Request;
use React\HttpClient\Client;
use React\HttpClient\Response;
use React\Stream\BufferedSink;
use Symfony\Component\DomCrawler\Crawler;

class Loader
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $deep;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Search
     */
    private $search;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var bool
     */
    private $done = false;

    public function __construct(Client $client, Queue $queue, Search $search)
    {
        $this->client = $client;
        $this->queue = $queue;
        $this->search = $search;
    }

    public function load($url, $deep)
    {
        if (null !== $this->url) {
            throw new \RuntimeException("This Loader object already loading an url.");
        }

        $url = filter_var($url, FILTER_VALIDATE_URL);

        if (false === $url) {
            return false;
        }

        $this->url = $url;
        $this->deep = $deep;

        $this->request = $this->client->request('GET', $url);
        $this->request->on('response', array($this, 'onResponse'));
        $this->request->end();

        return true;
    }

    public function onResponse(Response $response)
    {
        $this->response = $response;
        BufferedSink::createPromise($response)->then(array($this, 'onLoad'));
    }

    public function onLoad($body)
    {
        $this->done = true;
        $headers = $this->response->getHeaders();

        if (isset($headers['Location'])) {
            $this->pushQueue($headers['Location'], $this->deep - 1);

            return;
        }

        $html = new Crawler();
        $html->addHtmlContent($body);

        $this->search->index($this->url, $html);

        if ($this->deep > 0) {
            $base = parse_url($this->url);

            $links = $html->filter('a');
            $links->each(function (Crawler $link) use ($base) {
                $href = explode('#', $link->attr('href'))[0];
                $href = trim($href);

                if (empty($href)) {
                    return;
                }

                if ('/' === $href) {
                    return;
                }

                if (preg_match('/^https?:\/\//i', $href)) {
                    $url = $href;
                } else if (0 === strpos($href, '/')) {
                    $url = $base['scheme']
                        . '://'
                        . $base['host']
                        . $href;
                } else {
                    $url = $base['scheme']
                        . '://'
                        . $base['host']
                        . (isset($base['path']) ? $base['path'] : '/')
                        . $href;
                }

                if (HOMER_KEEP_HOST && $base['host'] !== parse_url($url, PHP_URL_HOST)) {
                    return;
                }

                $this->pushQueue($url, $this->deep - 1);
            });
        }
    }

    private function pushQueue($url, $deep)
    {
        $this->queue->pushMemory($url, $deep);
    }

    public function getDone()
    {
        return $this->done;
    }
}