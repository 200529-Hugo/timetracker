<?php

namespace OCA\TimeTracker\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\AppFramework\Http\TemplateResponse;
use OCA\TimeTracker\Controller\ClientsController;
use OCA\TimeTracker\Controller\DashboardController;
use OCA\TimeTracker\Controller\GoalsController;
use OCA\TimeTracker\Controller\PageController;
use OCA\TimeTracker\Controller\ProjectsController;
use OCA\TimeTracker\Controller\ReportsController;
use OCA\TimeTracker\Controller\TagsController;
use OCA\TimeTracker\Controller\TimelinesAdminController;
use OCA\TimeTracker\Controller\TimelinesController;

class SimpleControllersTest extends TestCase {
    private $request;
    private $userSession;
    private $appName = 'timetracker';

    protected function setUp(): void {
        $this->request = $this->createMock(IRequest::class);
        $this->userSession = $this->createMock(IUserSession::class);
    }

    public function testClientsController() {
        $controller = new ClientsController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/clients', $response->getParams()['appPage']);
    }

    public function testDashboardController() {
        $controller = new DashboardController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/dashboard', $response->getParams()['appPage']);
    }

    public function testGoalsController() {
        $controller = new GoalsController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/goals', $response->getParams()['appPage']);
    }

    public function testPageController() {
        $controller = new PageController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/index', $response->getParams()['appPage']);
    }

    public function testProjectsController() {
        $controller = new ProjectsController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/projects', $response->getParams()['appPage']);
    }

    public function testReportsController() {
        $controller = new ReportsController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/reports', $response->getParams()['appPage']);
    }

    public function testTagsController() {
        $controller = new TagsController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/tags', $response->getParams()['appPage']);
    }

    public function testTimelinesAdminController() {
        $controller = new TimelinesAdminController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/timelines-admin', $response->getParams()['appPage']);
    }

    public function testTimelinesController() {
        $controller = new TimelinesController($this->appName, $this->request, $this->userSession);
        $response = $controller->index();
        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals('content/timelines', $response->getParams()['appPage']);
    }
}
