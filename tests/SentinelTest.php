<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 2016/12/27
 * Time: 15:22
 */

namespace Jenner\Redis\Test;


use Jenner\Redis\Sentinel;

class SentinelTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMasterByName() {
        global $argv;
        $host = $argv[1];
        $port = $argv[2];
        $name = $argv[3];
        $true_name = $argv[4];
        $sentinel = new Sentinel($host, $port, $name);
        $this->assertEquals($true_name, $sentinel->get_master_addr_by_name($name));
    }
}