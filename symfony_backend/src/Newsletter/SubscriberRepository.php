<?php

namespace App\Newsletter;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscriber>
 */
class SubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry
    )
    {
        parent::__construct($registry, Subscriber::class);
    }

    public function signUp(string $email, string $removalKey): void
    {
        $subscriber = new Subscriber();
        $subscriber->setEmail($email);
        $subscriber->setRemovalKey($removalKey);

        $this->getEntityManager()->persist($subscriber);
        $this->getEntityManager()->flush();
    }

    public function unsubscribe(string $email, string $removalKey): UnsubscribeStatus
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.email = :email')
            ->setParameter(':email', $email);

        try {
            $result = $qb->getQuery()->getSingleResult();
        }
        catch(NoResultException)
        {
            return UnsubscribeStatus::ALREADY_DELETED;
        }

        if ($result->getRemovalKey() != $removalKey)
        {
            return UnsubscribeStatus::TOKEN_WRONG;
        }
        else
        {
            $this->getEntityManager()->remove($result);
            $this->getEntityManager()->flush();
            return UnsubscribeStatus::SUCCESSFULLY_DELETED;
        }
    }
}
