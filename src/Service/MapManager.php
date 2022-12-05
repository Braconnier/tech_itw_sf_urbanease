<?php

namespace App\Service;

use App\Entity\Boat;
use App\Entity\Tile;
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

    public function  moveBoat(string $direction): bool
    {
        $boat = $this->boatRepository->findOneBy([]);

        $boat =  match ($direction) {
            'N' => $boat->setCoordY($boat->getCoordY() - 1),
            'S' => $boat->setCoordY($boat->getCoordY() + 1),
            'W' => $boat->setCoordX($boat->getCoordX() - 1),
            'E' => $boat->setCoordX($boat->getCoordX() + 1),
        };
        if ($this->tileExists($boat->getCoordX(), $boat->getCoordY())) {
            $this->em->flush();
            return true;
        } else return false;
    }

    public function tileExists(int $x, int $y): bool
    {
        return !!$this->tileRepository->findOneBy(['coordX' => $x, 'coordY' => $y]);
    }

    public function getRandomIsland(): Tile
    {
        $islandTiles = $this->getIslandTiles();
        return $islandTiles[array_rand($islandTiles)];
    }

    public function resetBoat(Boat $boat): void
    {
        $boat->setCoordX(0)
            ->setCoordY(0);
        $this->em->persist($boat);
        $this->em->flush();
    }

    public function resetTreasure(): void
    {
        $islandTiles = $this->getIslandTiles();
        foreach ($islandTiles as $islandTile) {
            $islandTile->setHasTreasure(false);
            $this->em->persist($islandTile);
        }
        $island = $this->getRandomIsland();
        $island->setHasTreasure(true);
        $this->em->persist($island);
        $this->em->flush();
    }

    private function getIslandTiles(): array
    {
        return $this->tileRepository->findBy(['type' => 'island']);
    }
}
