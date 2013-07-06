<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Homer;

use Psr\Log\InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class Search
{
    /**
     * @var \PDO
     */
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function index($url, Crawler $html)
    {
        $title = $html->filter('title');
        if (count($title)) {
            $title = $title->text();
        } else {
            return;
        }

        $body = $html->filter('body');
        if (count($body)) {
            $body = $body->text();
        } else {
            $body = '';
        }

        $query = $this->db->prepare('INSERT OR REPLACE INTO indexes (url, title, body) VALUES (?, ?, ?)');
        $query->execute([$url, $title, $body]);
    }

    public function search($text, $limit)
    {
        $query = $this->db->prepare('SELECT * FROM indexes WHERE indexes MATCH :text LIMIT :limit');
        $query->bindValue(':text', $text);
        $query->bindValue(':limit', $limit);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
}