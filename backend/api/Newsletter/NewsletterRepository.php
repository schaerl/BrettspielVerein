<?php namespace BVZ\Newsletter;

use BVZ\BvzRepository;
use BVZ\BvzRepositoryException;
use BVZ\Logging\LoggerFactory;
use Monolog\Logger;
use PDOException;

class NewsletterRepository extends BvzRepository
{
    private Logger $logger;
    public function __construct(
        LoggerFactory $loggerFactory = new LoggerFactory()
    )
    {
        $this->logger = $loggerFactory->getLogger('NewsletterRepository');
    }

    /*
     * @throw BvzRepositoryException
     */
    public function signUp(string $email, string $removalKey): bool
    {
        try {
            $insert = $this->getQueryFactory()->newInsert()
                ->into('subscriber')
                ->cols([
                    'email' => $email,
                    'removal_key' => $removalKey
                ]);

            $this->getConnection()->prepare($insert)->execute($insert->getBindValues());
            return true;
        }
        catch(PDOException $e)
        {
            if ($e->getCode() === "23000")
            {
                $this->logger->info('Provided mail already exists in DB!');
                return false;
            }
            $this->logger->error($e->getMessage());
            throw new BvzRepositoryException("Inserting into subscribers DB failed!");
        }
    }
}
