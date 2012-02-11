<?php

namespace Millwright\MenuBundle\Annotation;

/**
 * Represents a @Translate annotation.
 *
 * @Annotation
 * @Target("METHOD")
 * @author Stefan Zerkalica <zerkalica@gmail.com>
 */
final class Menu
{
    public $label;
    public $translateDomain;
    public $translateParameters;

    public $name;
    public $showNonAuthorized;
    public $showAsText;

    public function __construct(array $values)
    {
        foreach($values as $property => $value) {
            $this->$property = $value;
        }

        if ($this->translateParameters) {
            $this->translateParameters = array_map('trim', explode(',', $this->translateParameters));
        }
    }
}
