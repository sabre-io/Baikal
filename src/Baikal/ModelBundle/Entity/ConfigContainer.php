<?php

namespace Baikal\ModelBundle\Entity;

class ConfigContainer implements ConfigContainerInterface {
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $config;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return HierarchicalConfig
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set config
     *
     * @param array $config
     * @return HierarchicalConfig
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return array 
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function has($prop) {
        return array_key_exists($prop, $this->config);
    }

    public function get($prop) {
        
        if(!$this->has($prop)) {
            throw new \RuntimeException('ConfigContainer: attempt to access undefined config property "' . $prop . '"');
        }

        return $this->config[$prop];
    }

    public function set($prop, $value) {
        $this->config[$prop] = $value;

        return $this;
    }
}
