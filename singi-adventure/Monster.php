<?php
/**
 * Created by PhpStorm.
 * User: lishuting
 * Date: 2018/5/17
 * Time: 下午10:17
 */

namespace singiAdventure;


class Monster
{
    private $name;
    private $hp;
    private $att;
    private $def;
    private $dmg;

    /**
     * Monster constructor.
     * @param $name
     * @param $hp
     * @param $att
     * @param $def
     */
    public function __construct($name, $hp, $att, $def)
    {
        $this->name = $name;
        $this->hp = $hp;
        $this->att = $att;
        $this->pd = $def;
    }

    /**
     * 受到伤害
     * @param $dmg 伤害值
     */
    public function hurt($dmg){
        if ($dmg > 0) $this->setHp($this->hp - $dmg);
    }

    public function attack($enemy){
        $enemy->hurt($enemy->getAtt() - $this->pd);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getHp()
    {
        return $this->hp;
    }

    /**
     * @param mixed $hp
     */
    public function setHp($hp)
    {
        if ($hp < 0) $hp = 0;
        $this->hp = $hp;
    }

    /**
     * @return mixed
     */
    public function getAtt()
    {
        return $this->att;
    }

    /**
     * @param mixed $att
     */
    public function setAtt($att)
    {
        $this->att = $att;
    }

    /**
     * @return mixed
     */
    public function getDef()
    {
        return $this->pd;
    }

    /**
     * @param mixed $def
     */
    public function setDef($def)
    {
        $this->pd = $def;
    }

    /**
     * @return mixed
     */
    public function getDmg()
    {
        return $this->dmg;
    }

    /**
     * @param mixed $dmg
     */
    public function setDmg($dmg)
    {
        $this->dmg = $dmg;
    }

}