<?php
/**
 * Created by PhpStorm.
 * User: filip
 * Date: 11.01.16
 * Time: 21:13
 */

namespace JobeetBundle\Tests\Utils;

use JobeetBundle\Utils\Jobeet;

class JobeetTest extends \PHPUnit_Framework_TestCase
{
    public function testSlugify() {
        $this->assertEquals('sensio', Jobeet::slugify('Sensio'));
        $this->assertEquals('sensio-labs', Jobeet::slugify('sensio labs'));
        $this->assertEquals('sensio-labs', Jobeet::slugify('sensio   labs'));
        $this->assertEquals('paris-france', Jobeet::slugify('paris,france'));
        $this->assertEquals('sensio', Jobeet::slugify('  sensio'));
        $this->assertEquals('sensio', Jobeet::slugify('sensio  '));


        if (function_exists('iconv')) {
            $this->assertEquals('developpeur-web', Jobeet::slugify('Développeur Web'));
        }
    }
}