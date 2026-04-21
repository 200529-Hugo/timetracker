<?php

namespace OCA\TimeTracker\Tests\Unit\Db;

use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Db\ProjectMapper;
use OCP\IDBConnection;
use OCP\DB\IPreparedStatement;
use OCA\TimeTracker\Db\Project;

class ProjectMapperTest extends TestCase {
    private $db;
    private $mapper;

    protected function setUp(): void {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new ProjectMapper($this->db);
    }

    public function testFindByName() {
        $name = 'test-project';
        $stmt = $this->createMock(IPreparedStatement::class);
        
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
            
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => $name],
                false
            );

        $result = $this->mapper->findByName($name);
        
        $this->assertInstanceOf(Project::class, $result);
        $this->assertEquals($name, $result->name);
    }

    public function testSearchByName() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'Project 1'],
                ['id' => 2, 'name' => 'Project 2'],
                false
            );

        $results = $this->mapper->searchByName('user', 'Proj');
        $this->assertCount(2, $results);
    }

    public function testFind() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'P1'],
                false
            );

        $result = $this->mapper->find(1);
        $this->assertInstanceOf(Project::class, $result);
    }

    public function testFindAll() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'P1'],
                ['id' => 2, 'name' => 'P2'],
                false
            );

        $results = $this->mapper->findAll('user');
        $this->assertCount(2, $results);
    }

    public function testFindAllAdmin() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'P1'],
                false
            );

        $results = $this->mapper->findAllAdmin();
        $this->assertCount(1, $results);
    }

    public function testDelete() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        
        $this->mapper->delete(1);
        $this->assertTrue(true); // If no exception, it passed
    }
}
