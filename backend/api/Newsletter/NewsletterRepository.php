<?php namespace BVZ\Newsletter;

use BVZ\BvzRepository;
use BVZ\BvzRepositoryException;
use BVZ\Logging\LoggerFactory;
use BVZ\Newsletter\UnsubscribeStatus;
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

    public function unsubscribe(string $email, string $removalKey): UnsubscribeStatus
    {
        try
        {
            $delete = $this->getQueryFactory()->newDelete()
                ->from('subscriber')
                ->where('email = :email')
                ->where('removal_key = :token')
                ->bindValues(["email" => $email, "token" => $removalKey]);

            $affectedRows = $this->getConnection()->fetchAffected($delete->getStatement(), $delete->getBindValues());
            if ($affectedRows > 0)
            {
                return UnsubscribeStatus::SUCCESSFULLY_DELETED;
            }
            else
            {
                $email = $this->fetchOne($email);
                if ($email === null)
                {
                    return UnsubscribeStatus::ALREADY_DELETED;
                }
                else
                {
                    return UnsubscribeStatus::TOKEN_WRONG;
                }
            }
        }
        catch (PDOException $e)
        {
            $this->logger->error($e->getMessage());
            throw new BvzRepositoryException("Deleting from subscribers DB failed!");
        }
    }

    private function fetchOne(string $email): ?string
    {
        $queryBuilder = $this->getQueryFactory();
        $select = $queryBuilder->newSelect()
            ->cols(['s.email', 's.removal_key'])
            ->from('subscriber AS s')
            ->where('s.email = :email')
            ->bindValue('email', $email);

        $result = $this->getConnection()->fetchObjects($select->getStatement(), $select->getBindValues());
        if (empty($result))
        {
            return null;
        }
        else {
            return $result[0];
        }
    }
}
