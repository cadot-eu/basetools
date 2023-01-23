<?php
foreach (array_diff(scandir('/app/src/Entity'), array('..', '.', 'ResetPasswordRequest.php', 'base')) as $entity) {
    $entity = ucfirst(substr($entity, 0, -strlen('.php')));
    //création du fichier repository manquant
    if (file_exists('/app/src/Repository/' . $entity . 'Repository.php') == false) {
        file_put_contents('/app/src/Repository/' . $entity . 'Repository.php', '<?php

            namespace App\Repository;
            
            use App\Entity\\' . $entity . ';
            use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
            use Doctrine\ORM\OptimisticLockException;
            use Doctrine\ORM\ORMException;
            use Doctrine\Persistence\ManagerRegistry;
            
            /**
             * @method ' . $entity . '|null find($id, $lockMode = null, $lockVersion = null)
             * @method ' . $entity . '|null findOneBy(array $criteria, array $orderBy = null)
             * @method ' . $entity . '[]    findAll()
             * @method ' . $entity . '[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
             */
            class ' . $entity . 'Repository extends ServiceEntityRepository
            {
                public function __construct(ManagerRegistry $registry)
                {
                    parent::__construct($registry, ' . $entity . '::class);
                }
            
                /**
                 * @throws ORMException
                 * @throws OptimisticLockException
                 */
                public function add(' . $entity . ' $entity, bool $flush = true): void
                {
                    $this->_em->persist($entity);
                    if ($flush) {
                        $this->_em->flush();
                    }
                }
            
                /**
                 * @throws ORMException
                 * @throws OptimisticLockException
                 */
                public function remove(' . $entity . ' $entity, bool $flush = true): void
                {
                    $this->_em->remove($entity);
                    if ($flush) {
                        $this->_em->flush();
                    }
                }
              }');
    }
}
