<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Homer;

class Queue
{
    /**
     * @var array
     */
    private $memory = array();

    /**
     * @var \PDO
     */
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function pop()
    {
        if (!empty($this->memory)) {
            $deep = end($this->memory);
            $url = key($this->memory);
            unset($this->memory[$url]);

            return ['url' => $url, 'deep' => $deep];
        }

        $result = $this->db->query("SELECT * FROM queue ORDER BY id ASC LIMIT 1");
        $row = $result ? $result->fetch(\PDO::FETCH_ASSOC) : false;

        if (false === $row) {
            return false;
        }

        $query = $this->db->prepare('DELETE FROM queue WHERE id = :id');
        $query->execute([':id' => $row['id']]);

        return $row;
    }

    public function push($url, $deep)
    {
        $query = $this->db->prepare('INSERT OR IGNORE INTO queue (url, deep) VALUES (:url, :deep)');
        $query->bindValue(':url', $url);
        $query->bindValue(':deep', $deep);
        $query->execute();
    }

    public function pushMemory($url, $deep)
    {
        $this->memory[$url] = $deep;
    }
}