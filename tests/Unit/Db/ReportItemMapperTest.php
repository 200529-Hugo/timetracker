<?php

namespace OCA\TimeTracker\Tests\Unit\Db;

use PHPUnit\Framework\TestCase;
use OCA\TimeTracker\Db\ReportItemMapper;
use OCP\IDBConnection;
use OCP\DB\IPreparedStatement;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;

class ReportItemMapperTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $this->db = $this->createMock(IDBConnection::class);
    }

    private function getMapperWithPlatform($platform) {
        $this->db->method('getDatabasePlatform')->willReturn($platform);
        return new ReportItemMapper($this->db);
    }

    public function testReportMySql() {
        $platform = $this->createMock(MySqlPlatform::class);
        $mapper = $this->getMapperWithPlatform($platform);
        
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->callback(function($sql) {
                return strpos($sql, 'DATE(FROM_UNIXTIME(start))') !== false;
            }))
            ->willReturn($stmt);
        
        $mapper->report('user', 0, 0, [], [], [], 'day', '', '', false, 0, 100);
    }

    public function testReportPostgres() {
        $platform = $this->createMock(PostgreSqlPlatform::class);
        $mapper = $this->getMapperWithPlatform($platform);
        
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->callback(function($sql) {
                return strpos($sql, "to_char(to_timestamp(start), 'YYYY-MM-DD')") !== false;
            }))
            ->willReturn($stmt);
        
        $mapper->report('user', 0, 0, [], [], [], 'day', '', '', false, 0, 100);
    }

    public function testReportSqlite() {
        $platform = $this->createMock(SqlitePlatform::class);
        $mapper = $this->getMapperWithPlatform($platform);
        
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->callback(function($sql) {
                return strpos($sql, "strftime('%Y-%m-%d', datetime(start, 'unixepoch'))") !== false;
            }))
            ->willReturn($stmt);
        
        $mapper->report('user', 0, 0, [], [], [], 'day', '', '', false, 0, 100);
    }

    public function testReportWithComplexFilters() {
        $platform = $this->createMock(MySqlPlatform::class);
        $mapper = $this->getMapperWithPlatform($platform);
        
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->callback(function($sql) {
                // Verify that BOTH project_id and client id filters are present in the SQL
                return strpos($sql, 'wi.project_id in (?,?)') !== false && 
                       strpos($sql, 'c.id in (?,?)') !== false;
            }))
            ->willReturn($stmt);
        
        $mapper->report('user', 0, 0, [1, 2], [10, 11], [], 'day', '', '', false, 0, 100);
    }

    public function testReportReturnsBothEntries() {
        $platform = $this->createMock(MySqlPlatform::class);
        $mapper = $this->getMapperWithPlatform($platform);
        
        $stmt = $this->createMock(IPreparedStatement::class);
        $this->db->method('prepare')->willReturn($stmt);
        
        // Simulate two rows returned by the database
        // One for Project 1 (Client 10) and one for Project 2 (Client 11)
        $stmt->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                [
                    'id' => 101,
                    'name' => 'Work 1',
                    'projectId' => 1,
                    'project' => 'Project X',
                    'clientId' => 10,
                    'client' => 'Client 10',
                    'totalDuration' => 3600,
                    'time' => 1600000000,
                    'ftime' => '2020-09-13',
                    'userUid' => 'user'
                ],
                [
                    'id' => 102,
                    'name' => 'Work 2',
                    'projectId' => 2,
                    'project' => 'Project X',
                    'clientId' => 11,
                    'client' => 'Client 11',
                    'totalDuration' => 1800,
                    'time' => 1600000000,
                    'ftime' => '2020-09-13',
                    'userUid' => 'user'
                ],
                false // End of results
            );

        $results = $mapper->report('user', 0, 0, [1, 2], [10, 11], [], 'day', 'project', 'client', false, 0, 100);
        
        $this->assertCount(2, $results);
        $this->assertEquals('Client 10', $results[0]->client);
        $this->assertEquals('Client 11', $results[1]->client);
        $this->assertEquals('Project X', $results[0]->project);
        $this->assertEquals('Project X', $results[1]->project);
    }
}
