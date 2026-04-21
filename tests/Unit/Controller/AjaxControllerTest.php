<?php

namespace OCA\TimeTracker\Tests\Unit\Controller;

use OCA\TimeTracker\Tests\Unit\Mock\RequestMock;
use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Controller\AjaxController;
use OCP\IUserSession;
use OCP\IL10N;
use OCA\TimeTracker\Db\WorkIntervalMapper;
use OCA\TimeTracker\Db\ClientMapper;
use OCA\TimeTracker\Db\UserToClientMapper;
use OCA\TimeTracker\Db\ProjectMapper;
use OCA\TimeTracker\Db\UserToProjectMapper;
use OCA\TimeTracker\Db\TagMapper;
use OCA\TimeTracker\Db\WorkIntervalToTagMapper;
use OCA\TimeTracker\Db\ReportItemMapper;
use OCA\TimeTracker\Db\TimelineMapper;
use OCA\TimeTracker\Db\TimelineEntryMapper;
use OCA\TimeTracker\Db\GoalMapper;
use OCA\TimeTracker\Db\WorkInterval;
use OCA\TimeTracker\Db\Client;
use OCA\TimeTracker\Db\Project;
use OCA\TimeTracker\Db\Tag;
use OCA\TimeTracker\Db\UserToClient;
use OCA\TimeTracker\Db\UserToProject;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http;

class AjaxControllerTest extends TestCase {
    private $controller;
    private $request;
    private $userSession;
    private $l10n;
    private $workIntervalMapper;
    private $clientMapper;
    private $userToClientMapper;
    private $projectMapper;
    private $userToProjectMapper;
    private $tagMapper;
    private $workIntervalToTagMapper;
    private $reportItemMapper;
    private $timelineMapper;
    private $timelineEntryMapper;
    private $goalMapper;
    private $userId = 'test-user';

    protected function setUp(): void {
        $this->request = new RequestMock();
        $this->userSession = $this->createMock(IUserSession::class);
        $this->l10n = $this->createMock(IL10N::class);
        $this->workIntervalMapper = $this->createMock(WorkIntervalMapper::class);
        $this->clientMapper = $this->createMock(ClientMapper::class);
        $this->userToClientMapper = $this->createMock(UserToClientMapper::class);
        $this->projectMapper = $this->createMock(ProjectMapper::class);
        $this->userToProjectMapper = $this->createMock(UserToProjectMapper::class);
        $this->tagMapper = $this->createMock(TagMapper::class);
        $this->workIntervalToTagMapper = $this->createMock(WorkIntervalToTagMapper::class);
        $this->reportItemMapper = $this->createMock(ReportItemMapper::class);
        $this->timelineMapper = $this->createMock(TimelineMapper::class);
        $this->timelineEntryMapper = $this->createMock(TimelineEntryMapper::class);
        $this->goalMapper = $this->createMock(GoalMapper::class);

        $this->controller = new AjaxController(
            'timetracker',
            $this->request,
            $this->userSession,
            $this->l10n,
            $this->workIntervalMapper,
            $this->clientMapper,
            $this->userToClientMapper,
            $this->projectMapper,
            $this->userToProjectMapper,
            $this->tagMapper,
            $this->workIntervalToTagMapper,
            $this->reportItemMapper,
            $this->timelineMapper,
            $this->timelineEntryMapper,
            $this->goalMapper,
            $this->userId
        );
    }

    public function testWorkIntervals() {
        $this->request->params['from'] = 100;
        $this->request->params['to'] = 200;
        $this->request->params['tzoffset'] = 0;

        $this->workIntervalMapper->expects($this->once())
            ->method('findLatestInterval')
            ->with($this->userId, 100, 200)
            ->willReturn([]);

        $this->workIntervalMapper->expects($this->once())
            ->method('findAllRunning')
            ->with($this->userId)
            ->willReturn([]);

        $response = $this->controller->workIntervals();

        $this->assertInstanceOf(JSONResponse::class, $response);
        $data = $response->getData();
        $this->assertArrayHasKey('WorkIntervals', $data);
        $this->assertArrayHasKey('running', $data);
        $this->assertArrayHasKey('days', $data);
    }

    public function testAddCost() {
        $id = 1;
        $cost = '12,50';
        $this->request->params['cost'] = $cost;

        $wi = new WorkInterval();
        $this->workIntervalMapper->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($wi);

        $this->workIntervalMapper->expects($this->once())
            ->method('update')
            ->with($wi);

        $response = $this->controller->addCost($id);

        $this->assertInstanceOf(JSONResponse::class, $response);
        $data = $response->getData();
        $this->assertTrue($data['success']);
        $this->assertEquals(12.50 * 100, $wi->cost);
    }

    public function testAddCostNonNumeric() {
        $id = 1;
        $this->request->params['cost'] = 'abc';

        $wi = new WorkInterval();
        $this->workIntervalMapper->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($wi);

        $response = $this->controller->addCost($id);

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(Http::STATUS_BAD_REQUEST, $response->getStatus());
    }

    public function testStartTimer() {
        $name = 'test-timer';
        $this->request->params['projectId'] = 10;
        $this->request->params['tags'] = '1,2';

        $this->workIntervalMapper->expects($this->once())
            ->method('findLatestByName')
            ->with($this->userId, $name)
            ->willReturn(null);

        $this->workIntervalMapper->expects($this->once())
            ->method('insert');

        $this->workIntervalToTagMapper->expects($this->exactly(2))
            ->method('insert');

        $response = $this->controller->startTimer($name);

        $this->assertInstanceOf(JSONResponse::class, $response);
        $data = $response->getData();
        $this->assertInstanceOf(WorkInterval::class, $data['WorkIntervals']);
        $this->assertEquals(1, $data['running']);
    }

    public function testStartTimerNameTooLong() {
        $name = str_repeat('a', 256);
        $response = $this->controller->startTimer($name);
        $data = $response->getData();
        $this->assertEquals('Name too long', $data['Error']);
    }

    public function testStopTimer() {
        $name = 'stopped-timer';
        $wi = new WorkInterval();
        $wi->start = time() - 100;

        $this->workIntervalMapper->expects($this->once())
            ->method('findAllRunning')
            ->with($this->userId)
            ->willReturn([$wi]);

        $this->workIntervalMapper->expects($this->once())
            ->method('update')
            ->with($wi);

        $response = $this->controller->stopTimer($name);

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(0, $wi->running);
        $this->assertGreaterThanOrEqual(100, $wi->duration);
    }

    public function testDeleteWorkInterval() {
        $id = 1;
        $wi = new WorkInterval();

        $this->workIntervalMapper->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($wi);

        $this->workIntervalMapper->expects($this->once())
            ->method('delete')
            ->with($wi);

        $this->workIntervalMapper->expects($this->once())
            ->method('findAllRunning')
            ->willReturn([]);

        $response = $this->controller->deleteWorkInterval($id);

        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testUpdateWorkInterval() {
        $id = 1;
        $wi = new WorkInterval();
        $this->request->params['name'] = 'New Name';
        $this->request->params['details'] = 'New Details';

        $this->workIntervalMapper->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($wi);

        $this->workIntervalMapper->expects($this->once())
            ->method('update')
            ->with($wi);

        $this->workIntervalMapper->expects($this->once())
            ->method('findAllRunning')
            ->willReturn([]);

        $response = $this->controller->updateWorkInterval($id);

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals('New Name', $wi->name);
        $this->assertEquals('New Details', $wi->details);
    }

    public function testUpdateWorkIntervalDates() {
        $id = 1;
        $wi = new WorkInterval();
        $this->request->params['start'] = '01/01/22 10:00';
        $this->request->params['end'] = '01/01/22 11:00';
        $this->request->params['tzoffset'] = 0;

        $this->workIntervalMapper->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($wi);

        $response = $this->controller->updateWorkInterval($id);

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(3600, $wi->duration);
    }

    public function testUpdateWorkIntervalNameTooLong() {
        $id = 1;
        $this->request->params['name'] = str_repeat('a', 256);
        $this->workIntervalMapper->expects($this->once())->method('find')->willReturn(new WorkInterval());
        $response = $this->controller->updateWorkInterval($id);
        $data = $response->getData();
        $this->assertArrayHasKey('Error', $data);
        $this->assertEquals('Name too long', $data['Error']);
    }

    public function testAddWorkInterval() {
        $this->request->params['name'] = 'New WI';
        $this->request->params['start'] = '01/01/22 10:00';
        $this->request->params['end'] = '01/01/22 12:00';

        $this->workIntervalMapper->expects($this->once())
            ->method('insert');
        $this->workIntervalMapper->expects($this->once())
            ->method('findAllRunning')
            ->willReturn([]);

        $response = $this->controller->addWorkInterval();

        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testAddClient() {
        $name = 'New Client';
        $this->clientMapper->expects($this->once())
            ->method('findByName')
            ->with($name)
            ->willReturn(null);
        $this->clientMapper->expects($this->once())
            ->method('insert');
        $this->userToClientMapper->expects($this->once())
            ->method('findForUserAndClient')
            ->willReturn(null);
        $this->userToClientMapper->expects($this->once())
            ->method('insert');
        
        $this->clientMapper->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $response = $this->controller->addClient($name);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testAddClientDuplicate() {
        $name = 'Duplicate Client';
        $client = new Client();
        $this->clientMapper->expects($this->once())->method('findByName')->willReturn($client);
        $this->userToClientMapper->expects($this->once())->method('findForUserAndClient')->willReturn(new UserToClient());

        $response = $this->controller->addClient($name);
        $this->assertEquals('This client is already in your list', $response->getData()['Error']);
    }

    public function testEditClient() {
        $id = 1;
        $name = 'Updated Client';
        $this->request->params['name'] = $name;
        $client = new Client();
        $this->clientMapper->expects($this->once())->method('find')->with($id)->willReturn($client);
        $this->clientMapper->expects($this->once())->method('findByName')->with($name)->willReturn(null);
        $this->clientMapper->expects($this->once())->method('update')->with($client);
        $this->clientMapper->expects($this->once())->method('findAll')->willReturn([]);

        $response = $this->controller->editClient($id);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($name, $client->name);
    }

    public function testDeleteClient() {
        $id = 1;
        $utoc = new UserToClient();
        $this->userToClientMapper->expects($this->once())->method('findForUserAndClient')->willReturn($utoc);
        $this->userToClientMapper->expects($this->once())->method('delete')->with($utoc);
        $this->clientMapper->expects($this->once())->method('findAll')->willReturn([]);

        $response = $this->controller->deleteClient($id);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testGetClients() {
        $this->clientMapper->expects($this->once())->method('findAll')->with($this->userId)->willReturn([]);
        $response = $this->controller->getClients();
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertArrayHasKey('Clients', $response->getData());
    }

    public function testAddProject() {
        $name = 'New Project';
        $this->projectMapper->expects($this->once())->method('findByName')->with($name)->willReturn(null);
        $this->projectMapper->expects($this->once())->method('insert');
        $this->userToProjectMapper->expects($this->once())->method('findForUserAndProject')->willReturn(null);
        $this->userToProjectMapper->expects($this->once())->method('insert');
        $this->projectMapper->expects($this->once())->method('findAll')->willReturn([]);

        $response = $this->controller->addProject($name);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testAddProjectLocked() {
        $name = 'Locked Project';
        $p = new Project();
        $p->setLocked(1);
        $this->projectMapper->expects($this->once())->method('findByName')->willReturn($p);
        \OC_User::$isAdmin = false;

        $response = $this->controller->addProject($name);
        $this->assertEquals('This project is locked', $response->getData()['Error']);
    }

    public function testEditProject() {
        $id = 1;
        $name = 'Updated Project';
        $this->request->params['name'] = $name;
        $p = new Project();
        $this->projectMapper->expects($this->once())->method('find')->with($id)->willReturn($p);
        $this->projectMapper->expects($this->once())->method('findByName')->with($name)->willReturn(null);
        $this->projectMapper->expects($this->once())->method('update')->with($p);
        $this->projectMapper->expects($this->once())->method('findAll')->willReturn([]);

        $response = $this->controller->editProject($id);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($name, $p->name);
    }

    public function testDeleteProject() {
        $id = 1;
        $utop = new UserToProject();
        $this->userToProjectMapper->expects($this->once())->method('findForUserAndProject')->willReturn($utop);
        $this->userToProjectMapper->expects($this->once())->method('delete')->with($utop);
        $this->projectMapper->expects($this->once())->method('findAll')->willReturn([]);

        $response = $this->controller->deleteProject($id);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testGetProjects() {
        $this->projectMapper->expects($this->once())->method('findAll')->with($this->userId)->willReturn([]);
        $response = $this->controller->getProjects();
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertArrayHasKey('Projects', $response->getData());
    }

    public function testAddTag() {
        $name = 'New Tag';
        $this->tagMapper->expects($this->once())->method('findByNameUser')->with($name, $this->userId)->willReturn(null);
        $this->tagMapper->expects($this->once())->method('insert');
        $this->tagMapper->expects($this->once())->method('findAll')->with($this->userId)->willReturn([]);

        $response = $this->controller->addTag($name);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testAddTagDuplicate() {
        $name = 'Duplicate Tag';
        $tag = new Tag();
        $this->tagMapper->expects($this->once())->method('findByNameUser')->willReturn($tag);

        $response = $this->controller->addTag($name);
        $this->assertEquals('This tag name already exists', $response->getData()['Error']);
    }

    public function testEditTag() {
        $id = 1;
        $name = 'Updated Tag';
        $this->request->params['name'] = $name;
        $tag = new Tag();
        $this->tagMapper->expects($this->once())->method('find')->with($id)->willReturn($tag);
        $this->tagMapper->expects($this->once())->method('findByNameUser')->with($name, $this->userId)->willReturn(null);
        $this->tagMapper->expects($this->once())->method('update')->with($tag);
        $this->tagMapper->expects($this->once())->method('findAll')->with($this->userId)->willReturn([]);

        $response = $this->controller->editTag($id);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($name, $tag->name);
    }

    public function testDeleteTag() {
        $id = 1;
        $tag = new Tag();
        $this->tagMapper->expects($this->once())->method('find')->with($id)->willReturn($tag);
        $this->tagMapper->expects($this->once())->method('delete')->with($tag);
        $this->workIntervalToTagMapper->expects($this->once())->method('deleteAllForTag')->with($id);
        $this->tagMapper->expects($this->once())->method('findAll')->willReturn([]);

        $response = $this->controller->deleteTag($id);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testGetTags() {
        $this->tagMapper->expects($this->once())->method('findAll')->with($this->userId)->willReturn([]);
        $response = $this->controller->getTags();
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertArrayHasKey('Tags', $response->getData());
    }

    public function testGetReportWithMultipleSameNameProjectsAndTypoClientAccess() {
        // SCENARIO:
        // Project X (ID: 1, Name: "Project X", ClientID: 10) - User has Client access
        // Project X (ID: 2, Name: "Project X", ClientID: 11) - Typo in Client name, User has NO Client access
        // User has access to both PROJECTS but only CLIENT 10.
        
        \OC_User::$isAdmin = false;
        
        $p1 = new Project();
        $p1->id = 1;
        $p1->name = "Project X";
        $p1->clientId = 10;
        
        $p2 = new Project();
        $p2->id = 2;
        $p2->name = "Project X";
        $p2->clientId = 11; // Typo client (e.g. "Client B ")
        
        // Mock allowed projects: user has access to BOTH projects
        $this->projectMapper->expects($this->once())
            ->method('findAll')
            ->with($this->userId)
            ->willReturn([$p1, $p2]);

        // Mock allowed clients: user has access ONLY to Client 10
        $c1 = new Client();
        $c1->id = 10;
        $this->clientMapper->expects($this->once())
            ->method('findAll')
            ->with($this->userId)
            ->willReturn([$c1]);

        // The expected behavior is that ReportItemMapper::report is called with:
        // filterProjectId including [1, 2]
        // AND filterClientId including [10, 11] (implicitly including Client 11 from Project 2)
        $this->reportItemMapper->expects($this->once())
            ->method('report')
            ->with(
                $this->userId, // name
                $this->anything(), // from
                $this->anything(), // to
                $this->callback(function($filter) {
                    return in_array(1, $filter) && in_array(2, $filter); // Both projects allowed
                }),
                $this->callback(function($filter) {
                    // BOTH clients should be in the filter now!
                    return in_array(10, $filter) && in_array(11, $filter); 
                })
            )
            ->willReturn([]);

        $this->controller->getReport();
    }
}
