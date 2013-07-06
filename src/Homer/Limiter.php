<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Homer;

class Limiter 
{
    private $limits = array();

    public function limit($thing)
    {
        $this->limits[$thing] = time();
    }

    public function available($thing)
    {
        return !isset($this->limits[$thing]);
    }

    public function release($max)
    {
        while($time = reset($this->limits)) {
            if(time() - $time > $max) {
                unset($this->limits[key($this->limits)]);
            } else {
                break;
            }
        }
    }
}