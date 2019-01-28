<?php

namespace SpShareBasket\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_plugin_sharebasket_baskets")
 */
class Baskets extends ModelEntity
{
    /**
     * Unique identifier
     *
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="basketID", type="string", nullable=false)
     */
    protected $basketID;

    /**
     * @var string
     * @ORM\Column(name="articles", type="text", nullable=false)
     */
    protected $articles;

    /**
     * @var string
     * @ORM\Column(name="session_id", type="string", nullable=false)
     */
    protected $sessionId = '';

    /**
     * @var DateTime
     * @ORM\Column(name="time", type="datetime", nullable=false)
     */
    protected $time;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getBasketID()
    {
        return $this->basketID;
    }

    /**
     * @param int $basketID
     */
    public function setBasketID($basketID)
    {
        $this->basketID = $basketID;
    }

    /**
     * @return string
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param string $articles
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }
}
