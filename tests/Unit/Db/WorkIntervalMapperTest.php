<?php

namespace OCA\TimeTracker\Tests\Unit\Db;

use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Db\WorkIntervalMapper;
use OCP\IDBConnection;
use OCP\DB\IPreparedStatement;
use OCA\TimeTracker\Db\WorkInterval;

class WorkIntervalMapperTest extends TestCase {
    private $db;
    private $mapper;

    protected function setUp(): void {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new WorkIntervalMapper($this->db);
    }

    public function testFindByName() {
        $name = 'test-name';
        $stmt = $this->createMock(IPreparedStatement::class);
        
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('WHERE `name` = ?'))
            ->willReturn($stmt);
            
        $stmt->expects($this->once())
            ->method('execute');
            
        $stmt->expects($this->exactly(2))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => $name, 'user_uid' => 'user'],
                false
            );

        $result = $this->mapper->findByName($name);
        
        $this->assertInstanceOf(WorkInterval::class, $result);
        $this->assertEquals($name, $result->name);
    }
}
