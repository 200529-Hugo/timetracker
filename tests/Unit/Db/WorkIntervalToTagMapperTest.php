<?php

namespace OCA\TimeTracker\Tests\Unit\Db;

use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Db\WorkIntervalToTagMapper;
use OCP\IDBConnection;
use OCP\DB\IPreparedStatement;
use OCA\TimeTracker\Db\WorkIntervalToTag;

class WorkIntervalToTagMapperTest extends TestCase {
    private $db;
    private $mapper;

    protected function setUp(): void {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new WorkIntervalToTagMapper($this->db);
    }

    public function testFind() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'work_interval_id' => 100, 'tag_id' => 5],
                false
            );

        $result = $this->mapper->find(1);
        $this->assertInstanceOf(WorkIntervalToTag::class, $result);
    }

    public function testFindAllForWorkInterval() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'work_interval_id' => 100, 'tag_id' => 5],
                false
            );

        $results = $this->mapper->findAllForWorkInterval(100);
        $this->assertCount(1, $results);
    }

    public function testDeleteAllForWorkInterval() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        
        $this->mapper->deleteAllForWorkInterval(100);
        $this->assertTrue(true);
    }

    public function testDeleteAllForTag() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        
        $this->mapper->deleteAllForTag(5);
        $this->assertTrue(true);
    }
}
