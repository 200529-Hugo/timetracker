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
}
