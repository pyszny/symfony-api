<?php

use App\DataFixtures\AppFixtures;
use Behat\Behat\Context\Context;
use Behatch\HttpCall\Request;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class FeatureContext extends \Behatch\Context\RestContext
{
    const USERS = [
        'admin' => 'secret123#'
    ];
    const AUTH_URL = '/api/login_check';
    const AUTH_JSON = '
        {
            "username": "%s",
            "password": "%s"
        }
    ';

    private $fixtures;
    private $matcher;
    private $em;

    public function __construct(
        Request $request, 
        AppFixtures $fixtures,
        EntityManagerInterface $em
    ) {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->matcher = (new SimpleFactory())->createMatcher();
        $this->em = $em;
    }

    /**
     * @Given I am authenticated as :user
     */
    public function iAmAuthenticatedAs($user)
    {
        $this->request->setHttpHeader('Content-Type', 'application/ld+json');
        $this->request->send(
            'POST',
            $this->locatePath(self::AUTH_URL),
            [],
            [],
            sprintf(self::AUTH_JSON, $user, self::USERS[$user])
        );

        $json = json_decode($this->request->getContent(), true);
        // Make sure the token was returned
        $this->assertTrue(isset($json['token']));

        $token = $json['token'];

        $this->request->setHttpHeader(
            'Authorization',
            'Bearer '.$token
        );
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        $purger = new ORMPurger($this->em);
        $fixturesExecutor = new ORMExecutor($this->em, $purger);

        $fixturesExecutor->execute([$this->fixtures]);
    }
}
