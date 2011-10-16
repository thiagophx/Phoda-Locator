<?php

/**
* LICENSE
*
* Copyright (c) 2011, Thiago Rigo.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without modification,
* are permitted provided that the following conditions are met:
*
*     * Redistributions of source code must retain the above copyright notice,
* 		this list of conditions and the following disclaimer.
*
*     * Redistributions in binary form must reproduce the above copyright notice,
* 		this list of conditions and the following disclaimer in the documentation
* 		and/or other materials provided with the distribution.
*
*     * Neither the name of Thiago Rigo nor the names of its
* 		contributors may be used to endorse or promote products derived from this
* 		software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
* ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
* ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*/

namespace Phoda\Locator;

use Phoda\Locator\Reader\Reader;

class Container implements \ArrayAccess
{
    protected $parameters = array();
    protected $services = array();
    
    public function __construct($reader = null)
    {
        if ($reader)
            $this->addParameters($reader);
    }

    public function offsetExists($offset)
    {
        return isset($this->parameters[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->parameters[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->parameters[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->parameters[$offset]);
    }

    public function addParameters(Reader $reader)
    {
        $this->parameters += $reader->getParameters();
    }
    
    public function staticService($name, \Closure $service)
    {
        $this->services[$name] = function ($c) use ($service)
        {
            static $object;
            
            if (!$object)
                $object = $service($c);
                
            return $object;
        };
    }
    
    public function __set($name, \Closure $value)
    {
        $this->services[$name] = $value;
    }
    
    public function __get($name)
    {
        return $this->services[$name]($this);
    }
    
    public function __isset($name)
    {
        return isset($this->services[$name]);
    }
}