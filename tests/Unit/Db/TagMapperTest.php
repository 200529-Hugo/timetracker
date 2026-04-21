<?php

namespace OCA\TimeTracker\Tests\Unit\Db;

use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Db\TagMapper;
use OCP\IDBConnection;
use OCP\DB\IPreparedStatement;
use OCA\TimeTracker\Db\Tag;

class TagMapperTest extends TestCase {
    private $db;
    private $mapper;

    protected function setUp(): void {
        $this->db = $this->createMock(IDBConnection::class);
        
        $platform = $this->getMockBuilder('\Doctrine\DBAL\Platforms\MySqlPlatform')->getMock();
        $this->db->method('getDatabasePlatform')->willReturn($platform);
        
        $this->mapper = new TagMapper($this->db);
    }

    public function testFindByNameUser() {
        $name = 'test-tag';
        $user = 'user1';
        $stmt = $this->createMock(IPreparedStatement::class);
        
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
            
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => $name, 'user_uid' => $user],
                false
            );

        $result = $this->mapper->findByNameUser($name, $user);
        
        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($name, $result->name);
    }

    public function testFind() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'T1'],
                false
            );

        $result = $this->mapper->find(1);
        $this->assertInstanceOf(Tag::class, $result);
    }

    public function testFindAll() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'T1'],
                ['id' => 2, 'name' => 'T2'],
                false
            );

        $results = $this->mapper->findAll('user');
        $this->assertCount(2, $results);
    }

    public function testFindAllAlowedForProject() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'T1'],
                false
            );

        $results = $this->mapper->findAllAlowedForProject(10);
        $this->assertCount(1, $results);
    }

    public function testAllowedTags() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        
        $this->mapper->allowedTags(10, [1, 2]);
        $this->assertTrue(true);
    }
}
