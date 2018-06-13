<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\ChunkServer;

use Doctrine\Common\Collections\ArrayCollection;

class PublicConnectionCollection extends ArrayCollection
{
    /**
     * Finds the connection by its id
     * @param string $id
     * @return PublicConnection
     */
    public function findById($id)
    {
        foreach ($this as $publicConnection) {
            if ($publicConnection->getId() == $id) {
                return $publicConnection;
            }
        }
        return null;
    }
}