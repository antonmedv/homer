<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Homer;

use React\Http\Request;
use React\Http\Response;

class Statistic
{
    private $stat = [];

    private $max = 1000;

    public function app(Request $request, Response $response)
    {

        $response->writeHead(200, [
            'Content-Type' => 'text/plain',
            'Access-Control-Allow-Origin' => '*',
        ]);
        $body = implode(',', $this->stat['memory']);
        $response->end($body);
    }

    public function add($name, $value)
    {
        if (!isset($this->stat[$name])) {
            $this->stat[$name] = [];
        }

        $this->stat[$name][] = $value;

        if (count($this->stat[$name]) > $this->max) {
            array_shift($this->stat[$name]);
        }
    }
}