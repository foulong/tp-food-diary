<?php

namespace Tests\AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Security\GithubUserProvider;
use PHPUnit\Framework\TestCase;

/**
 * Description of GithubUserProviderTest
 * @property GuzzleHttp\Client $client
 * @author guillaume
 */
class GithubUserProviderTest extends TestCase {
    public $client;
    public $serializer;
    public $response;
    public $streamMessage;
    
    
    public function setUp() {
	parent::setUp();
	
	$this->client = $this->getMockBuilder('GuzzleHttp\Client')->disableOriginalConstructor()
	    ->setMethods(['get'])
	    ->getMock();
	
	$this->serializer = $this->getMockBuilder('JMS\Serializer\Serializer')
	    ->disableOriginalConstructor()
	    ->getMock();
	
	$this->response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
	    ->getMock();
	
	$this->streamMessage = $this->getMockBuilder('Psr\Http\Message\StreamInterface')->getMock();
    }
    
    public function tearDown() {
	$this->client = null;	
	$this->serializer = null;	
	$this->response = null;	
	$this->streamMessage = null;
    }
    
    public function testLoadUserByUsernameReturningAUser()
    {
	$this->client//->expects($this->once())
	    ->method('get')->willReturn($this->response);
	
	$this->response//->expects($this->once())
	    ->method('getBody')->willReturn($this->streamMessage);
	
	$t_userParams = [
	    'login' => 'gui',
	    'name' => 'F',
	    'email' => 'g.foulon@agt.fr',
	    'avatar_url' => 'guillaume.jpg',
	    'html_url' => 'http://www.guillaume.fr'
	];

	$this->serializer->expects($this->once())->method('deserialize')->willReturn($t_userParams);
	
	
	$githubUserProvider = new GithubUserProvider($this->client,$this->serializer);
	$userLoaded = $githubUserProvider->loadUserByUsername('toto');
	
	$userParams = new User(
            $t_userParams['login'],
            $t_userParams['name'],
            $t_userParams['email'],
            $t_userParams['avatar_url'],
            $t_userParams['html_url']
        );
	
	$this->assertEquals($userParams, $userLoaded, 'données user corroumpues');
    }
    
    public function testLoadUserByEmptyUserReturningException() {
	$this->client//->expects($this->once())
	    ->method('get')->willReturn($this->response);
	
	$this->response//->expects($this->once())
	    ->method('getBody')->willReturn($this->streamMessage);
	
	$this->serializer->method('deserialize')->willReturn(false);
		
	$this->expectException(\LogicException::class);
	
	$githubUserProvider2 = new GithubUserProvider($this->client,$this->serializer);
	$userLoaded2 = $githubUserProvider2->loadUserByUsername('toto2');
    }
}
