<?php

namespace App\Repository;

use App\Entity\SensorData;
use App\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SensorData|null find($id, $lockMode = null, $lockVersion = null)
 * @method SensorData|null findOneBy(array $criteria, array $orderBy = null)
 * @method SensorData[]    findAll()
 * @method SensorData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SensorData::class);
    }

    /**
    * @return SensorData[] Returns an array of SensorData objects
    */
    public function findBetween( $sensor, $from, $to)
    {
        $qb = $this->createQueryBuilder('d');
        return $qb
            ->andWhere('d.sensor = :sensor')
            ->setParameter('sensor', $sensor->getId())

            ->andWhere( $qb->expr()->gte( 'd.date', ':from' ))
            ->setParameter( 'from', $from )

            ->andWhere( $qb->expr()->lte( 'd.date', ':to' ))
            ->setParameter( 'to', $to )

            ->orderBy( 'd.date', 'ASC' )

            ->getQuery()
            ->getResult()
        ;
    }

    public function deleteSendorData( Sensor $sensor )
    {
        $qb = $this->createQueryBuilder( 's' );
        $qb
            ->delete()
            ->where( $qb->expr()->eq( 's.sensor', ':id' ) )
            ->setParameter( 'id', $sensor->getId() )
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

}
