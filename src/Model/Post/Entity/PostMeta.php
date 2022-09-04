<?php

declare(strict_types=1);

namespace App\Model\Post\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class PostMeta
{
    /**
     * @ORM\Column(type="string")
     * @Assert\Ip()
     */
    private $ip;

    /**
     * @ORM\Column(type="string")
     */
    private $browser;

    public function __construct(string $ip, string $browser)
    {
        $this->ip = $ip;
        $this->browser = $browser;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getBrowser(): string
    {
        return $this->browser;
    }
}