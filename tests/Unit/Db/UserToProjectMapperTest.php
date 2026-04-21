<?php

namespace OCA\TimeTracker\Tests\Unit\Db;

use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Db\UserToProjectMapper;
use OCP\IDBConnection;
use OCP\DB\IPreparedStatement;
use OCA\TimeTracker\Db\UserToProject;
use OCA\TimeTracker\Db\Project;

class UserToProjectMapperTest extends TestCase {
    private $db;
    private $mapper;

    protected function setUp(): void {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new UserToProjectMapper($this->db);
    }

    public function testFind() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'user_uid' => 'u1', 'project_id' => 10],
                false
            );

        $result = $this->mapper->find(1);
        $this->assertInstanceOf(UserToProject::class, $result);
    }

    public function testFindAllForUser() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'user_uid' => 'u1', 'project_id' => 10],
                false
            );

        $results = $this->mapper->findAllForUser('u1');
        $this->assertCount(1, $results);
    }

    public function testFindAllForProject() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'user_uid' => 'u1', 'project_id' => 10],
                false
            );

        $results = $this->mapper->findAllForProject(10);
        $this->assertCount(1, $results);
    }

    public function testFindForUserAndProject() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'user_uid' => 'u1', 'project_id' => 10],
                false
            );

        $project = new Project();
        $project->id = 10;
        $result = $this->mapper->findForUserAndProject('u1', $project);
        $this->assertInstanceOf(UserToProject::class, $result);
    }

    public function testDeleteAllForProject() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        
        $this->mapper->deleteAllForProject(10);
        $this->assertTrue(true);
    }
}
