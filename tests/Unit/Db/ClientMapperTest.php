<?php

namespace OCA\TimeTracker\Tests\Unit\Db;

use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Db\ClientMapper;
use OCP\IDBConnection;
use OCP\DB\IPreparedStatement;
use OCA\TimeTracker\Db\Client;

class ClientMapperTest extends TestCase {
    private $db;
    private $mapper;

    protected function setUp(): void {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new ClientMapper($this->db);
    }

    public function testFindByName() {
        $name = 'test-client';
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
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($name, $result->name);
    }

    public function testFindAll() {
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->any())
            ->method('prepare')
            ->willReturn($stmt);
        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'C1'],
                ['id' => 2, 'name' => 'C2'],
                false
            );

        $results = $this->mapper->findAll('user');
        $this->assertCount(2, $results);
    }
}
