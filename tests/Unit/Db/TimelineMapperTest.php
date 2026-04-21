<?php

namespace OCA\TimeTracker\Tests\Unit\Db;

use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Db\TimelineMapper;
use OCP\IDBConnection;
use OCP\DB\IPreparedStatement;
use OCA\TimeTracker\Db\Timeline;

class TimelineMapperTest extends TestCase {
    private $db;
    private $mapper;

    protected function setUp(): void {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new TimelineMapper($this->db);
    }

    public function testFind() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'user_uid' => 'u1'],
                false
            );

        $result = $this->mapper->find(1);
        $this->assertInstanceOf(Timeline::class, $result);
    }

    public function testFindAll() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'user_uid' => 'u1'],
                ['id' => 2, 'user_uid' => 'u1'],
                false
            );

        $results = $this->mapper->findAll('u1');
        $this->assertCount(2, $results);
    }

    public function testFindLatest() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'user_uid' => 'u1'],
                false
            );

        $results = $this->mapper->findLatest();
        $this->assertCount(1, $results);
    }

    public function testFindByStatus() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'status' => 'active'],
                false
            );

        $results = $this->mapper->findByStatus('active');
        $this->assertCount(1, $results);
    }
}
