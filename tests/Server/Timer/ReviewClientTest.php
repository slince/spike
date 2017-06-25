<?php
namespace Spike\Tests\Server\Timer;

use Spike\Server\Timer\ReviewClient;
use Spike\Tests\Server\Fixtures\Stub\ServerStub;
use Spike\Tests\Timer\TestCase;

class ReviewClientTest extends TestCase
{
    public function testConstruct()
    {
        $server = new ServerStub();
        $timer = $this->getMockBuilder(ReviewClient::class)
            ->setMethods(['getInterval'])
            ->setConstructorArgs([
                $this->getServerMock()
            ])
            ->getMock()
            ->method('getInterval')
            ->willReturn(0.1);
        $server =
    }
}