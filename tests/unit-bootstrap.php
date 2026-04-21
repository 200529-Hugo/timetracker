<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

// Manually add OCP to autoloader
$loader->addPsr4('OCP\\', __DIR__ . '/../vendor/christophwurst/nextcloud/OCP');
$loader->addPsr4('OCA\\TimeTracker\\', __DIR__ . '/../lib');

if (!\class_exists('\OCA\TimeTracker\AppFramework\Db\CompatibleMapper')) {
    if (\class_exists(\OCP\AppFramework\Db\Mapper::class)) {
        \class_alias(\OCP\AppFramework\Db\Mapper::class, 'OCA\TimeTracker\AppFramework\Db\CompatibleMapper');
    } else {
        \class_alias(\OCA\TimeTracker\AppFramework\Db\OldNextcloudMapper::class, 'OCA\TimeTracker\AppFramework\Db\CompatibleMapper');
    }
}

// Mock OC class
class OC {
    public static $server;
}

class OC_User {
    public static $isAdmin = false;
    public static function isAdminUser($user) { return self::$isAdmin; }
    public static function getUser() { return 'test-user'; }
}

OC::$server = new class {
    public function get($class) {
        if ($class === \OCP\IRequest::class) {
            return new class {
                public function getId() { return 'test-id'; }
            };
        }
        return null;
    }
    public function query($class) {
        return $this->get($class);
    }
};

if (!defined('PHPUNIT_RUN')) {
    define('PHPUNIT_RUN', 1);
}
