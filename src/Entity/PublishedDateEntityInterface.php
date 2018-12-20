<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 20.12.18
 * Time: 21:16
 */

namespace App\Entity;


interface PublishedDateEntityInterface
{
    public function setPublished(\DateTimeInterface $published): PublishedDateEntityInterface;
}