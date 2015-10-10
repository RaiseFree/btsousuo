<?php
/**
 * Film
 *
 * @desc $Id$
 *
 * @author Michael Song 2015-09-28
 */

class FilmModel
{
    private $id, $infohash, $name, $length, $ctime, $click, $lastac, $ymd;

    public function __get($name) 
    {
        if (!empty($name)) {
            $method = 'get'.ucwords($name);
            if (method_exists($this, $method)) {
                return $this->$method();
            }elseif(isset($this->$name)){
                return $this->$name;

            }
        }
        return null;
    }

    public function getMagnet() 
    {
        if (!empty($this->infohash)) {
            return "magnet:?xt=urn:btih:{$this->infohash}&dn={$this->name}";
        }
    }

    public function getName() 
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLength()
    {
        return Foo::formatBytes($this->length);
    }

    public function getCtime()
    {
        return date('Y-m-d H:i:s', $this->ctime);
    }

    public function getClick()
    {
        return $this->click;
    }

    public function getLastac()
    {
        return $this->lastac;
    }

    public function getYmd()
    {
        return $this->ymd;
    }

    public function isNew() 
    {
        if (time() - $this->ctime <= (2 * 86400)) {
            return true;
        } else {
            return false;
        }
        
    }
}
