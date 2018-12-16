<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TodoRepository")
 */
class Todo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $owner;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $complete_till_ts;

    /**
     * @ORM\Column(type="datetime")
     */
    private $insert_ts;

    /**
     * @ORM\Column(type="smallint")
     */
    private $priority;

    /**
     * @ORM\Column(type="boolean")
     */
    private $done;

    /**
     * @ORM\Column(type="datetime")
     */
    private $update_ts;
    
    
    // ##### Own methods #####################################################################################
    
    /**
     * Returns an array of all public visible fields pretty formatted
     *
     * @param   void
     * @return  array
     * @author  Markus Buscher
     */
    public function getPublicDataArray()
    {
        $complete_till_ts = $this->getCompleteTillTs();
        if(is_a($complete_till_ts, 'DateTime'))
        {
            $complete_till_ts = $complete_till_ts->format('Y-m-d');
        }
        else
        {
            $complete_till_ts = null;
        }
        
        $insert_ts = $this->getInsertTs();
        if(is_a($insert_ts, 'DateTime'))
        {
            $insert_ts = $insert_ts->format('c');
        }
        else
        {
            $insert_ts = null;
        }
    
        $update_ts = $this->getUpdateTs();
        if(is_a($update_ts, 'DateTime'))
        {
            $update_ts = $update_ts->format('c');
        }
        else
        {
            $update_ts = null;
        }
        
        $result = array(
            'id'               => $this->getId(),
            'title'            => $this->getTitle(),
            'description'      => $this->getDescription(),
            'owner'            => $this->getOwner(),
            'priority'         => $this->getPriority(),
            'complete_till_ts' => $complete_till_ts,
            'done'             => $this->getDone(),
            'insert_ts'        => $insert_ts,
            'update_ts'        => $update_ts
        );
        
        return $result;
    }
    
    
    // ##### auto-generated ##################################################################################

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCompleteTillTs(): ?\DateTimeInterface
    {
        return $this->complete_till_ts;
    }

    public function setCompleteTillTs(?\DateTimeInterface $complete_till_ts): self
    {
        $this->complete_till_ts = $complete_till_ts;

        return $this;
    }

    public function getInsertTs(): ?\DateTimeInterface
    {
        return $this->insert_ts;
    }

    public function setInsertTs(\DateTimeInterface $insert_ts): self
    {
        $this->insert_ts = $insert_ts;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        // only allow values 1, 0 or -1
        if($priority > 0)
        {
            $this->priority = 1;
        }
        else if($priority < 0)
        {
            $this->priority = -1;
        }
        else
        {
            $this->priority = 0;
        }

        return $this;
    }

    public function getDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function getUpdateTs(): ?\DateTimeInterface
    {
        return $this->update_ts;
    }

    public function setUpdateTs(\DateTimeInterface $update_ts): self
    {
        $this->update_ts = $update_ts;

        return $this;
    }
}
