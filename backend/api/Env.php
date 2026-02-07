<?php

namespace BVZ;

use Dotenv\Dotenv;

require_once __DIR__ . "/../vendor/autoload.php";

class Env
{
    static Dotenv $dotenv;

    const DB_HOST = "DB_HOST";
    const DB_USER = "DB_USER";
    const DB_PW = "DB_PW";
    const DB_NAME = "DB_NAME";

    const LOG_FILE_PATH = "LOG_FILE_PATH";
    const LOG_LEVEL = "LOG_LEVEL";

    const MAIL_HOST = "MAIL_HOST";
    const MAIL_PORT = "MAIL_PORT";
    const MAIL_PW = "MAIL_PW";

    const PROFILE = "PROFILE";
    const DEV_PROFILE = "dev";
    const PRODUCTION_PROFILE = "production";

    static function init(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    }

    static function get(string $key): string
    {
        if (!isset(Env::$dotenv)) {
            Env::init();
        }
        return $_ENV[$key];
    }

    static function isDevEnv(): bool
    {
        return Env::get(Env::PROFILE) === Env::DEV_PROFILE;
    }

    static function getLogPath(): string
    {
        $path = Env::get(Env::LOG_FILE_PATH);
        if (str_starts_with($path, "/"))
        {
            return $path;
        }
        else
        {
            return __DIR__ . "/" . $path;
        }
    }
}
