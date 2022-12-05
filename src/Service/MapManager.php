<?php

namespace App\Service;

use App\Repository\BoatRepository;
use App\Repository\TileRepository;
use Doctrine\ORM\EntityManagerInterface;

class MapManager
{
    public function __construct(private TileRepository $tileRepository, EntityManagerInterface $em, BoatRepository $boatRepository)
    {
        $this->tileRepository = $tileRepository;
        $this->boatRepository = $boatRepository;
        $this->em = $em;
    }
    public function  moveBoat($direction)
    {
        $boat = $this->boatRepository->findOneBy([]);

        $boat =  match ($direction) {
            'N' => $boat->setCoordY($boat->getCoordY() - 1),
            'S' => $boat->setCoordY($boat->getCoordY() + 1),
            'W' => $boat->setCoordX($boat->getCoordX() - 1),
            'E' => $boat->setCoordX($boat->getCoordX() + 1),
        };
        $this->em->flush();
        return true;
    }
}
